<?php

namespace Umomega\Media\ImageFilters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Small implements FilterInterface
{
	/**
     * Applies filter to given image
     *
     * @param  Image $image
     * @return Image
     */
    public function applyFilter(Image $image)
    {
        if ($image->width() > 640) return $image->resize(640, null, function ($constraint) { $constraint->aspectRatio(); });

        return $image;
    }
}