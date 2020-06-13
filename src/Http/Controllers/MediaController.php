<?php

namespace Umomega\Media\Http\Controllers;

use Umomega\Media\Medium;
use Umomega\Media\ImageProcessor;
use Umomega\Foundation\Http\Controllers\Controller;
use Umomega\Media\Http\Controllers\Traits\UploadsFiles;
use Umomega\Media\Http\Requests\UploadMedium;
use Umomega\Media\Http\Requests\EmbedMedium;
use Umomega\Media\Http\Requests\UpdateMedium;
use Umomega\Media\Http\Requests\UpdateImage;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;

class MediaController extends Controller
{
	
	use UploadsFiles;

	/**
	 * Returns a list of tags
	 *
	 * @param Request $request
	 * @return json
	 */
	public function index(Request $request)
	{
		$media = Medium::orderBy($request->get('s', 'created_at'), $request->get('d', 'desc'));

		if($request->get('f', 'all') != 'all') {
			$media = $media->whereType($request->get('f'));
		}

		return $media->paginate(30);
	}

	/**
	 * Returns a list of media filtered by search
	 *
	 * @param Request $request
	 * @return json
	 */
	public function search(Request $request)
	{
		return ['data' => (new Search())
			->registerModel(Medium::class, 'name')
			->search($request->get('q'))
			->map(function($medium) {
				return $medium->searchable;
			})];
	}

	/**
	 * Stores the uploaded file
	 *
	 * @param UploadMedium $request
	 * @return json
	 */
	public function upload(UploadMedium $request)
	{
		$file = $request->file('file');

		if(!$file->isValid()) abort(422);

		$medium = $this->createMedium($file);

		activity()->on($medium)->log('MediumUploaded');

		return [
			'message' => __('media::media.uploaded'),
			'payload' => $medium
		];
	}

	/**
	 * Embeds a new medium
	 *
	 * @param EmbedMedium $request
	 * @return json
	 */
	public function embed(EmbedMedium $request)
	{
		$medium = Medium::embedExternal($request->get('url'));

		activity()->on($medium)->log('MediumEmbedded');

		return [
			'message' => __('media::media.embedded'),
			'payload' => $medium
		];
	}

	/**
	 * Retrieves the medium information
	 *
	 * @param Medium $medium
	 * @return json
	 */
	public function show(Medium $medium)
	{
		return $medium;
	}

	/**
	 * Updates the medium
	 *
	 * @param UpdateMedium $request
	 * @param Medium $medium
	 * @return json
	 */
	public function update(UpdateMedium $request, Medium $medium)
	{
		if($medium->type == 'embed') {
			$medium->updateEmbed($request->validated());
		} else {
			$medium->update($request->validated());
		}

		activity()->on($medium)->log('MediumUpdated');

		return [
			'message' => __('media::media.edited'),
			'payload' => $medium
		];
	}

	/**
	 * Updates the image
	 *
	 * @param UpdateImage $request
	 * @param Medium $medium
	 * @return json
	 */
	public function updateImage(UpdateImage $request, Medium $medium)
	{
		$ip = (new ImageProcessor())->process($medium, $request->get('action'));

		activity()->on($medium)->log('ImageUpdated');

		return [
			'message' => __('media::media.edited_image'),
			'payload' => $medium
		];
	}

	/**
	 * Bulk deletes media
	 *
	 * @param Request $request
	 * @return json
	 */
	public function destroyBulk(Request $request)
	{
		$items = $this->validate($request, ['items' => 'required|array'])['items'];

		$names = [];

		$media = Medium::whereIn('id', $items)->get();

		foreach($media as $medium)
		{
			$names[] = $medium->name;
			$medium->delete();
		}

		activity()->withProperties(compact('names'))->log('MediaDestroyedBulk');

		return ['message' => __('media::media.deleted_multiple')];
	}

	/**
	 * Deletes a medium
	 *
	 * @param Medium $medium
	 * @return json
	 */
	public function destroy(Medium $medium)
	{
		$name = $medium->name;

		$medium->delete();

		activity()->withProperties(compact('name'))->log('MediumDestroyed');

		return [
			'message' => __('media::media.deleted'),
			'redirect' => 'media.index'
		];
	}

}