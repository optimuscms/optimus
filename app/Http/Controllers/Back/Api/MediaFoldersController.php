<?php

namespace App\Http\Controllers\Back\Api;

use App\Http\Controllers\Back\Controller;
use App\Http\Resources\MediaFolderResource;
use App\Models\MediaFolder;
use App\Rules\NotDescendantOrSelf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class MediaFoldersController extends Controller
{
    /**
     * Display a list of folders.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        /** @var Collection $folders */
        $folders = MediaFolder::query()
            ->applyFilters($request->all())
            ->orderBy('name')
            ->get();

        return MediaFolderResource::collection($folders);
    }

    /**
     * Create a new folder.
     *
     * @param Request $request
     * @return MediaFolderResource
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:media_folders,id',
        ]);

        $folder = new MediaFolder();

        $folder->name = $request->input('name');
        $folder->parent_id = $request->input('parent_id');

        $folder->save();

        return new MediaFolderResource($folder);
    }

    /**
     * Display the specified folder.
     *
     * @param int $id
     * @return MediaFolderResource
     */
    public function show($id)
    {
        /** @var MediaFolder $folder */
        $folder = MediaFolder::findOrFail($id);

        return new MediaFolderResource($folder);
    }

    /**
     * Update the specified folder.
     *
     * @param Request $request
     * @param int $id
     * @return MediaFolderResource
     */
    public function update(Request $request, $id)
    {
        /** @var MediaFolder $folder */
        $folder = MediaFolder::findOrFail($id);

        $data = $request->validate([
            'name' => 'filled|string|max:255',
            'parent_id' => [
                'nullable', 'exists:media_folders,id',
                new NotDescendantOrSelf($folder->id, 'media_folders'),
            ],
        ]);

        $folder->update($data);

        return new MediaFolderResource($folder);
    }

    /**
     * Delete the specified folder.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        MediaFolder::findOrFail($id)->delete();

        return response()->noContent();
    }
}
