<?php

use Umomega\Media\Medium;

if ( ! function_exists('upload_path'))
{
	/**
     * Get the path to the upload folder.
     *
     * @param string $path
     * @return string
     */
    function upload_path($path = '')
    {
        return config('media.upload_path') . ($path ? '/' . $path : $path);
    }
}

if ( ! function_exists('allowed_extensions'))
{
	/**
     * Returns an optionally imploded list of allowed file extensions
     *
     * @param string|null $glue
     * @return string|array
     */
    function allowed_extensions($glue = null)
    {
    	if($glue) return implode($glue, config('media.extensions'));

    	return config('media.extensions');
    }
}

if ( ! function_exists('allowed_mimetypes'))
{
	/**
     * Returns an optionally imploded list of allowed file mimetypes
     *
     * @param string|null $glue
     * @return string|array
     */
    function allowed_mimetypes($glue = null)
    {
    	if($glue) return implode($glue, config('media.mimetypes'));

    	return config('media.mimetypes');
    }
}

if ( ! function_exists('max_upload_size'))
{
    /**
     * Returns maximum upload size
     *
     * @return int
     */
    function max_upload_size()
    {
        return config('media.max_size');
    }
}

if ( ! function_exists('readable_size'))
{
    /**
     * Returns readable file size
     *
     * @param int
     * @return string
     */
    function readable_size($size)
    {
        $unit = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB');

        return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }
}

if ( ! function_exists('make_upload_path'))
{
    /**
     * Creates the current upload directory
     *
     * @param string $uploadPath
     * @return string
     * @throws RuntimeException
     */
    function make_upload_path($uploadPath)
    {
        $uploadPath = upload_path($uploadPath);

        if ( ! file_exists($uploadPath))
        {
            if ( ! mkdir($uploadPath, 0777, true))
            {
                throw new RuntimeException('Directory (' . $uploadPath . ') could not be created.');
            }
        }

        return $uploadPath;
    }
}

if ( ! function_exists('get_upload_path'))
{
    /**
     * Creates a new upload path
     *
     * @return array
     */
    function get_upload_path()
    {
        $relativePath = date('Y/m');

        $fullPath = make_upload_path($relativePath);

        return [$fullPath, $relativePath];
    }
}

if ( ! function_exists('get_new_file_name'))
{
    /**
     * Creates a new random file name
     *
     * @param string|null $extension
     * @return string
     */
    function get_new_file_name($extension = null)
    {
        return md5(uniqid(mt_rand(), true)) . ($extension ? '.' . $extension : '');
    }
}

if( ! function_exists('get_medium'))
{
    /**
     * Returns a medium
     *
     * @param int $id
     * @return Medium
     */
    function get_medium($id)
    {
        return Medium::find($id);
    }
}

if( ! function_exists('get_media'))
{
    /**
     * Returns a medium
     *
     * @param array $id
     * @return Medium
     */
    function get_media(array $ids)
    {
        return Medium::whereIn('id', $ids)
            ->orderByRaw('FIELD (id, ' . implode(', ', $ids) . ') ASC')
            ->get();
    }
}