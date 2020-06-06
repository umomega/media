<?php

namespace Umomega\Media;

use Intervention\Image\Image;
use Image as ImageFacade;

class ImageProcessor {

	/**
	 * Processes an image
	 *
	 * @param Medium $medium
	 * @param string $action
	 * @return Model
	 */
	public function process(Medium $medium, $action)
	{
		$action = explode(':', $action);

		switch ($action[0]) {
			case 'rotate':
				$this->rotate($medium, $action[1]);
				break;
			case 'flip':
				$this->flip($medium, $action[1]);
				break;
			case 'greyscale':
				$this->greyscale($medium);
				break;
			case 'crop':
				$this->crop($medium, $action[1]);
				break;
		}
	}

	/**
	 * Loads an image
	 *
	 * @param string $path
	 * @return Image
	 */
	protected function loadImage($path)
	{
		return ImageFacade::make(upload_path($path));
	}

	/** 
	 * Saves an image
	 *
	 * @param Medium $medium
	 * @param $image
	 */
	protected function saveImage(Medium $medium, Image $image)
	{
		list($fullPath, $relativePath) = get_upload_path();
		$filename = get_new_file_name($medium->metadata['extension']);

		$p = $fullPath . '/' . $filename;

		$image->save($p);

		$medium->deleteFile();

		$medium->path = $relativePath . '/' . $filename;

		list($width, $height, $type, $attr) = getimagesize($p);
        $media->metadata['size'] = filesize($p);
        $media->metadata['width'] = $width;
        $media->metadata['height'] = $height;

		$medium->save();
	}

	/**
	 * Rotates an image
	 *
	 * @param Medium $medium
	 * @param string $angle
	 */
	protected function rotate(Medium $medium, $angle)
	{
		$image = $this->loadImage($medium->path)->rotate($angle);

		$this->saveImage($medium, $image);
	}

	/**
	 * Rotates an image
	 *
	 * @param Medium $medium
	 * @param string $direction
	 */
	protected function flip(Medium $medium, $direction)
	{
		$image = $this->loadImage($medium->path)->flip($direction);

		$this->saveImage($medium, $image);
	}

	/**
	 * Makes an image greyscale
	 *
	 * @param Medium $medium
	 */
	protected function greyscale(Medium $medium)
	{
		$image = $this->loadImage($medium->path)->greyscale();

		$this->saveImage($medium, $image);
	}

	/**
	 * Crops an image
	 *
	 * @param Medium $medium
	 * @param string $crop
	 */
	protected function crop(Medium $medium, $crop)
	{
		$crop = explode(',', $crop);

		$image = $this->loadImage($medium->path)->crop(floor($crop[0]), floor($crop[1]), floor($crop[2]), floor($crop[3]));

		$this->saveImage($medium, $image);
	}

}