<?php

namespace Umomega\Media\ImageFilters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Compact implements FilterInterface
{

	/**
     * Applies filter to given image
     *
     * @param  Image $image
     * @return Image
     */
    public function applyFilter(Image $image)
    {
        if ($image->width() >= $image->height())
        {
            return $image->resize(544, null, function ($constraint)
            {
                $constraint->aspectRatio();
            });
        }

        return $image->resize(null, 544, function ($constraint)
        {
            $constraint->aspectRatio();
        });
    }
	
}