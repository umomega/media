<?php

namespace Umomega\Media\Console;

use Illuminate\Console\Command;
use Umomega\Media\Medium;
use Intervention\Image\ImageManager;

class CompressImages extends Command {

	/**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'media:compress-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compress JPG and PNG images';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $images = Medium::where('type', 'image')->get();

        $manager = new ImageManager();

        foreach($images as $image) {
            if(
                ($image->metadata['width'] <= 2000 && $image->metadata['height'] <= 2000)
                 || !in_array($image->metadata['extension'], ['jpeg', 'jpg', 'png'])
            ) {
                echo "Skipping: {$image->name} \n";
            } else {
                $width = $image->metadata['width'];
                $height = $image->metadata['height'];

                $path = public_path(upload_path($image->path));

                echo "Processing: {$image->name} ({$path}) - Dimensions: {$width}x{$height} - Size: ". readable_size($image->metadata['size']) ."\n";

                $manager->make($path)->resize(
                    ($width >= $height ? 2000 : null),
                    ($width < $height ? 2000 : null),
                    function($constraint) { $constraint->aspectRatio(); }
                )->save();

                \ImageOptimizer::optimize($path);

                list($width, $height, $type, $attr) = getimagesize($path);

                $metadata = $image->metadata;
                
                $metadata['width'] = $width;
                $metadata['height'] = $height;
                $metadata['size'] = filesize($path);

                $image->metadata = $metadata;

                $image->save();

                echo "New size {$width}x{$height} (" . readable_size($image->metadata['size']) . ")\n\n";
            }
            
        }
    }

}