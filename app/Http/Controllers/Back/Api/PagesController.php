<?php

namespace App\Http\Controllers\Back\Api;

use App\Http\Controllers\Back\Controller;
use App\Http\Resources\PageResource;
use App\Jobs\UpdatePagePath;
use App\Models\Meta;
use App\Models\Page;
use App\Registries\PageTemplates;
use App\Rules\NotDescendantOrSelf;
use App\Rules\ValidPageTemplate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class PagesController extends Controller
{
    /**
     * Display a list of pages.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        /** @var Collection $pages */
        $pages = Page::withDrafts()
            ->applyFilters($request->all())
            ->withCount('children')
            ->with('contents', 'meta')
            ->get();

        return PageResource::collection($pages);
    }

    /**
     * Create a new page.
     *
     * @param Request $request
     * @return PageResource
     */
    public function store(Request $request)
    {
        $this->validatePage($request);

        $template = PageTemplates::get(
            $templateId = $request->input('template_id')
        );

        $template::validateData(
            $templateData = $request->input('template_data', [])
        );

        $page = new Page([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'template_id' => $templateId,
            'parent_id' => $request->input('parent_id'),
            'is_standalone' => $request->input('is_standalone', false),
        ]);

        $page->has_fixed_path = false;
        $page->has_fixed_template = false;
        $page->is_deletable = true;

        $page->save();

        $template::saveData($page, $templateData);

        $page->saveMeta(
            $request->input('meta', [])
        );

        UpdatePagePath::dispatch($page)->onQueue('sync');

        $page->publish(
            $request->input('is_published')
        );

        return new PageResource($page);
    }

    /**
     * Display the specified page.
     *
     * @param int $id
     * @return PageResource
     */
    public function show($id)
    {
        /** @var Page $page */
        $page = Page::withDrafts()->findOrFail($id);

        return new PageResource($page);
    }

    /**
     * Update the specified page.
     *
     * @param Request $request
     * @param int $id
     * @return PageResource
     */
    public function update(Request $request, $id)
    {
        /** @var Page $page */
        $page = Page::withDrafts()->findOrFail($id);

        $this->validatePage($request, $page);

        $templateId = ! $page->has_fixed_template
            ? $request->input('template_id')
            : $page->template_id;

        $template = PageTemplates::get($templateId);

        $template::validateData(
            $templateData = $request->input('template_data', [])
        );

        $slug = ! $page->has_fixed_path
            ? $request->input('slug')
            : $page->slug;

        $page->update([
            'title' => $request->input('title'),
            'slug' => $slug,
            'template_id' => $templateId,
            'parent_id' => $request->input('parent_id'),
            'is_standalone' => $request->input('is_standalone'),
        ]);

        $template::resetData($page);
        $template::saveData($page, $templateData);

        $page->saveMeta(
            $request->input('meta', [])
        );

        if (! $page->has_fixed_path) {
            UpdatePagePath::dispatch($page)->onQueue('sync');
        }

        $page->publish(
            $request->input('is_published')
        );

        return new PageResource($page);
    }

    /**
     * Delete the specified page.
     *
     * @param int $id
     * @return Response
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        /** @var Page $page */
        $page = Page::withDrafts()->findOrFail($id);

        if (! $page->is_deletable) {
            abort(403, 'This page cannot be deleted.');
        }

        $page->delete();

        return response()->noContent();
    }

    /**
     * Validate the request.
     *
     * @param Request $request
     * @param Page|null $page
     * @return void
     */
    protected function validatePage(Request $request, Page $page = null)
    {
        $parentIdRules = [
            'nullable', 'exists:pages,id',
        ];

        if ($page) {
            $parentIdRules[] = new NotDescendantOrSelf(
                $page->id, 'pages'
            );
        }

        $request->validate(array_merge([
            'title' => 'required|string|max:255',
            'slug' => [
                $page ? 'required' : 'nullable', 'string', 'max:255',
                Rule::unique('pages')->ignore($page),
            ],
            'template_id' => [
                'required', new ValidPageTemplate(),
            ],
            'template_data' => 'array',
            'parent_id' => $parentIdRules,
            'is_standalone' => 'present|boolean',
            'is_published' => 'present|boolean',
        ], Meta::rules()));
    }
}
