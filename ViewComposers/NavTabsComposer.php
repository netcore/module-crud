<?php

namespace Modules\Crud\ViewComposers;

use Illuminate\View\View;
use Netcore\Translator\Helpers\TransHelper;

class NavTabsComposer implements ComposerInterface
{
    /**
     * Compose the view
     *
     * @param View $view
     * @return mixed
     */
    public function compose(View $view)
    {
        $languages = TransHelper::getAllLanguages();
        
        $view->with(compact('languages'));
    }
}