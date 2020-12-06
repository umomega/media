<?php

namespace Umomega\Media\ImageFilters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Large implements FilterInterface
{
	/**
     * Applies filter to given image
     *
     * @param  Image $image
     * @return Image
     */
    public function applyFilter(Image $image)
    {
        if ($image->width() > 1600) return $image->resize(1600, null, function ($constraint) { $constraint->aspectRatio(); });

        return $image;
    }
}