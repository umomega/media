<?php

namespace Umomega\Media\ImageFilters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Medium implements FilterInterface
{
	/**
     * Applies filter to given image
     *
     * @param  Image $image
     * @return Image
     */
    public function applyFilter(Image $image)
    {
        if ($image->width() > 960) return $image->resize(960, null, function ($constraint) { $constraint->aspectRatio(); });

        return $image;
    }
}