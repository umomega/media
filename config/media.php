<?php

return [
	
	/*
	|--------------------------------------------------------------------------
	| Upload Path
	|--------------------------------------------------------------------------
	|
	| You may define a custom upload path for your files here. Supply the
    | upload path relative to base path.
	|
	*/
    'upload_path' => 'uploads',

	 /*
	|--------------------------------------------------------------------------
	| Maximum Allowed Upload File Size
	|--------------------------------------------------------------------------
	|
    | If the 'validates' option is set to 'true', this option is used
	| to limit the maximum allowed file size for upload. It chooses
    | the minimum value between the one configured here and the return
    | value of Symfony\Component\HttpFoundation\File\UploadedFile::getMaxFilesize().
    | Supply this value in bytes.
	|
	*/
    'max_size' => 10485760,


    /*
	|--------------------------------------------------------------------------
	| Allowed File Extensions
	|--------------------------------------------------------------------------
	|
	| If the 'validates' option is set to 'true', this option is used to
    | validate file extensions allowed for upload.
	|
	*/
    'extensions' => [
        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg',
        'txt', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'aac', 'mp3', 'mp4', 'mpeg', 'mpg', 'ogg', 'wav', 'webm',
    ],

    /*
	|--------------------------------------------------------------------------
	| Allowed File Mime Types
	|--------------------------------------------------------------------------
	|
	| If the 'validates' option is set to 'true', this option is used to
    | validates file mime types allowed for upload.
	|
	*/
    'mimetypes' => [
        'image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml',
        'text/plain', 'application/pdf', 'application/msword', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'audio/aac', 'audio/mp4', 'audio/mpeg', 'audio/ogg', 'audio/wav', 'audio/webm',
        'video/mp4', 'video/ogg', 'video/webm',
    ],

    /*
	|--------------------------------------------------------------------------
	| Media Types
	|--------------------------------------------------------------------------
	|
	| Determine media type keys here for the file mime types which will be
    | used for automatic media type determination.
	|
	*/
	'media_types' => [
		'image/jpeg' => 'image',
		'image/gif' => 'image',
		'image/png' => 'image',
		'image/bmp' => 'image',
		'image/svg+xml' => 'image',
		'text/plain' => 'document',
        'application/pdf' => 'document',
        'application/msword' => 'document',
        'application/vnd.ms-excel' => 'document',
        'application/vnd.ms-powerpoint' => 'document',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'document',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'document',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'document',
		'audio/aac' => 'audio',
        'audio/mp4' => 'audio',
        'audio/mpeg' => 'audio',
        'audio/ogg' => 'audio',
        'audio/wav' => 'audio',
        'audio/webm' => 'audio',
        'video/mp4' => 'video',
        'video/ogg' => 'video',
        'video/webm' => 'video',
 	]

    
];