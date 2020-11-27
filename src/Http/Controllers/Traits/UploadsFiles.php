<?php

namespace Umomega\Media\Http\Controllers\Traits;

use Umomega\Media\Medium;
use Illuminate\Http\UploadedFile;

trait UploadsFiles {

	/**
	 * Creates a medium from an uploaded file
	 *
	 * @param UploadedFile $file
	 * @return Medium
	 */
	protected function createMedium(UploadedFile $file)
	{
		return Medium::create($this->storeUploadedFile($file));
	}

	/**
	 * Moves an uploaded file
	 *
	 * @param UploadedFile $file
	 * @return array
	 */
	protected function storeUploadedFile(UploadedFile $file)
	{
		$attributes = [
			'name' => $file->getClientOriginalName(),
			'type' => config('media.media_types.' . $file->getMimeType()),
			'metadata' => [
				'extension' => $file->getClientOriginalExtension(),
				'mimetype' => $file->getMimeType(),
				'size' => $file->getSize()
			],
            'alttext' => [],
            'caption' => [],
            'description' => []
		];

        $attributes['path'] = $this->moveUploadedFile($file);

        if($attributes['type'] == 'image')
        {
        	$p = upload_path($attributes['path']);

            \ImageOptimizer::optimize($p);

            list($width, $height, $type, $attr) = getimagesize($p);
            
            $attributes['metadata']['size'] = filesize($p);
            $attributes['metadata']['width'] = $width;
            $attributes['metadata']['height'] = $height;
        }

		return $attributes;
	}

	/**
	 * Moves an uploaded file to the public directory
	 *
     * @param UploadedFile $file
     * @return string
     */
    protected function moveUploadedFile(UploadedFile $file)
    {
        list($fullPath, $relativePath) = get_upload_path();

        $filename = get_new_file_name(
            $file->getClientOriginalExtension()
        );

        $file->move($fullPath, $filename);

        return $relativePath . '/' . $filename;
    }

}