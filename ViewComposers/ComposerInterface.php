<?php

namespace Modules\Crud\ViewComposers;

use Illuminate\View\View;

interface ComposerInterface
{
    /**
     * Compose the view
     *
     * @param View $view
     * @return mixed
     */
    public function compose(View $view);
}