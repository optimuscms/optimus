<?php

namespace App\Http\Controllers\Back\Api;

use App\Http\Controllers\Back\Controller;
use App\Http\Resources\MediaFolderResource;
use App\Models\MediaFolder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class MediaFoldersController extends Controller
{
    /**
     * Display a list of media folders.
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
     * Create a new media folder.
     *
     * @param Request $request
     * @return MediaFolderResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:media,id',
        ]);

        $folder = new MediaFolder($data);

        $folder->save();

        return new MediaFolderResource($folder);
    }

    /**
     * Display the specified media folder.
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
     * Update the specified media folder.
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
            'parent_id' => 'nullable|exists:media,id',
            // Todo: NotSelfOrAncestor...
        ]);

        $folder->fill($data)->save();

        return new MediaFolderResource($folder);
    }

    /**
     * Delete the specified media folder.
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
