<?php

namespace Umomega\Media;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Medium extends Model implements Searchable {

    use HasTranslations, Cachable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'metadata', 'path', 'alttext', 'caption', 'description'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'alttext' => 'array',
        'caption' => 'array',
        'description' => 'array'
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['alttext', 'caption', 'description'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['thumbnail_url', 'compact_url', 'public_url', 'locales'];

    /**
     * Searchable config
     *
     * @return SearchResult
     */
    public function getSearchResult(): SearchResult
    {
        return new SearchResult($this, $this->name);
    }

    /**
     * Accessor for the thumbnail URL
     *
     * @return string
     */
    public function getThumbnailUrlAttribute()
    {
        if($this->type === 'image') return $this->imageURLFor('thumbnail');

        if($this->type === 'embed' && isset($this->metadata['image_local'])) return $this->imageURLFor('thumbnail', $this->metadata['image_local']);

        return null;
    }

    /**
     * Accessor for the compact URL
     *
     * @return string
     */
    public function getCompactUrlAttribute()
    {
        if($this->type === 'image') return $this->imageURLFor('compact');

        if($this->type === 'embed' && isset($this->metadata['image_local'])) return $this->imageURLFor('compact', $this->metadata['image_local']);

        return null;
    }

    /**
     * Accessor for the public URL
     *
     * @return string
     */
    public function getPublicUrlAttribute()
    {
        if($this->type === 'embed') return $this->path;

        return asset(upload_path($this->path));
    }

    /**
     * Accessor for locales of the medium
     *
     * @return array
     */
    public function getLocalesAttribute()
    {
        return config('app.locales');
    }

    /**
     * Getter for filtered images
     *
     * @param string $filter
     * @param string|null $path
     * @return string
     */
    public function imageURLFor($filter, $path = null) {
        return asset(config('imagecache.route') . '/' . $filter . '/' . ($path ?: $this->path));
    }

    /**
     * Delete the model from the database
     * and from the filesystem
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        if ($this->deleteFile())
        {
            return parent::delete();
        }

        return false;
    }

    /**
     * Deletes the file from the filesystem
     *
     * @return bool
     */
    public function deleteFile()
    {
        if($this->type == 'embed') {
            if(isset($this->metadata['image_local'])) @unlink(upload_path($this->metadata['image_local']));

            return true;
        }

        return @unlink(upload_path($this->path));
    }

    /**
     * Helper for creating and updating embedded media
     *
     * @param string $url
     * @return self
     */
    public static function embedExternal($url)
    {
        $medium = new self;

        $medium->fill($medium->compileEmbedAttributesFrom($url))->save();

        return $medium;
    }

    /**
     * Updates an embedded medium
     *
     * @param mixed $attributes
     */
    public function updateEmbed($attributes)
    {
        if($this->path != $attributes['public_url']) {
            $this->deleteFile();
            $attributes = $this->mergeEmbedAttributes($attributes);
        }

        $this->fill($attributes)->save();
    }

    /**
     * Merges embedded attributes with edited ones
     *
     * @param mixed $attributes
     * @return mixed
     */
    protected function mergeEmbedAttributes($attributes)
    {
        $embed = $this->compileEmbedAttributesFrom($attributes['public_url']);

        $embed['alttext'] = array_merge($attributes['alttext'], $embed['alttext']);
        $embed['caption'] = array_merge($attributes['caption'], $embed['caption']);
        $embed['description'] = array_merge($attributes['description'], $embed['description']);

        return $embed;
    }

    /**
     * Compiles attributes from an embed URL
     *
     * @param string $url
     * @return array
     */
    protected function compileEmbedAttributesFrom($url)
    {
        $embed = new \Embed\Embed();
        $info = $embed->get($url);

        $attributes = [
            'name' => $info->title ?: $info->url,
            'type' => 'embed',
            'path' => $url,
            'metadata' => [
                'code' => $info->code
            ],
            'alttext' => [],
            'caption' => [config('app.locale') => $info->title ?: $info->url],
            'description' => [config('app.locale') => $info->description ?: '']
        ];

        // Let's copy the thumbnail if there is any
        if(!is_null($info->image))
        {
            $path = make_upload_path('embedded');
            $filename = get_new_file_name();

            copy($info->image, $path . '/' . $filename);

            $attributes['metadata']['image'] = $info->image;
            $attributes['metadata']['image_local'] = 'embedded/' . $filename;
        }

        return $attributes;
    }

    /**
     * Shorthand for the responsive attribute
     *
     * @return string
     */
    public function getResponsiveAttribute()
    {
        return $this->responsiveImage();
    }

    /**
     * Presenter for responsive image
     *
     * @param string $class
     * @return string
     */
    public function responsiveImage($class = 'w-full', $attributes = ['loading' => 'lazy', 'decoding' => 'async'])
    {
        if ($this->type != 'image') return null;

        $sources = [
            'xlarge' => 1920,
            'large'  => 1600,
            'medium' => 960,
            'small'  => 640,
            'xsmall' => 400,
        ];

        $srcset = [];
        foreach ($sources as $filter => $width) {
            $srcset[] = $this->imageURLFor($filter) . ' ' . $width . 'w';
        }

        // Generic sizes: assumes full width on small screens, capped on large ones
        $sizes = [
            '(max-width: 640px) 100vw',
            '(max-width: 960px) 90vw',
            '(max-width: 1600px) 80vw',
            '1200px', // default fallback for very large screens
        ];

        return sprintf(
            '<img src="%s" srcset="%s" sizes="%s" alt="%s" class="%s" width="%d" height="%d" %s/>',
            $this->imageURLFor('xlarge'),          // fallback src
            implode(', ', $srcset),               // srcset list
            implode(', ', $sizes),                // layout-aware sizes
            htmlspecialchars(empty($this->alttext) ? '' : (is_array($this->alttext) ? $this->alttext[0] : $this->alttext)), // safe alt
            $class,
            $this->metadata['width'] ?? 0,
            $this->metadata['height'] ?? 0,
            collect($attributes)->map(function($value, $key) {
                return $key . '="' . htmlspecialchars($value) . '"';
            })->implode(' ')
        );
    }

    /**
     * Compresses self if an image
     */
    public function autoCompress()
    {
        if(($this->metadata['width'] <= 2000 && $this->metadata['height'] <= 2000)
            || !in_array($this->metadata['extension'], ['jpeg', 'jpg', 'png'])) return false;

        $width = $this->metadata['width'];
        $height = $this->metadata['height'];

        $path = public_path(upload_path($this->path));

        $manager = new \Intervention\Image\ImageManager();

        $manager->make($path)->resize(
            ($width >= $height ? 2000 : null),
            ($width < $height ? 2000 : null),
            function($constraint) { $constraint->aspectRatio(); }
        )->save();

        \ImageOptimizer::optimize($path);

        list($width, $height, $type, $attr) = getimagesize($path);

        $metadata = $this->metadata;
                
        $metadata['width'] = $width;
        $metadata['height'] = $height;
        $metadata['size'] = filesize($path);

        $this->metadata = $metadata;

        $this->save();

        return true;
    }
    
}