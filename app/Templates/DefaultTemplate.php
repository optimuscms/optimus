<?php

namespace App\Templates;

use OptimusCMS\Pages\Models\Page;
use OptimusCMS\Pages\Contracts\Template;

class DefaultTemplate implements Template
{
    /**
     * Get the template's name.
     *
     * @return string
     */
    public static function name(): string
    {
        return 'default';
    }

    /**
     * Get the template's label.
     *
     * @return string
     */
    public static function label(): string
    {
        return 'Default';
    }

    /**
     * Validate the template data.
     *
     * @param array $data
     * @return void
     *
     * @throws ValidationException
     */
    public function validate(array $data)
    {
        //
    }

    /**
     * Save the template data to the page.
     *
     * @param Page $page
     * @param array $data
     * @return void
     */
    public function save(Page $page, array $data)
    {
        //
    }

    /**
     * Reset the template to its original state.
     *
     * @param Page $page
     * @return void
     */
    public function reset(Page $page)
    {
        //
    }

    /**
     * Render the template.
     *
     * @param Page $page
     * @return Response
     */
    public function render(Page $page)
    {
        //
    }

    /**
     * Transform the template data into an array.
     *
     * @param Page $page
     * @return array
     */
    public function toArray(Page $page): array
    {
        return [
            //
        ];
    }
}
