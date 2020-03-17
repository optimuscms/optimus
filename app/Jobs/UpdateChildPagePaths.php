<?php

namespace App\Jobs;

use App\Models\Page;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateChildPagePaths
{
    use Dispatchable;

    /** @var Page $page */
    protected $page;

    /**
     * Create a new job instance.
     *
     * @param Page $page
     * @return void
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->updateChildPagePaths($this->page);
    }

    protected function updateChildPagePaths(Page $parent)
    {
        $children = $parent->children()
            ->where('has_fixed_path', false)
            ->get();

        $children->each(function (Page $page) use ($parent) {
            $page->setRelation('parent', $parent);

            $page->path = $page->buildPath();
            $page->save();

            $this->updateChildPagePaths($page);
        });
    }
}
