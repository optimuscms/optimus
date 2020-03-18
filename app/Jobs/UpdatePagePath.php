<?php

namespace App\Jobs;

use App\Models\Page;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePagePath
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Page */
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
        $this->page->path = $this->page->buildPath();
        $this->page->save();

        if ($this->page->wasChanged('path')) {
            UpdateChildPagePaths::dispatch($this->page);
        }
    }
}
