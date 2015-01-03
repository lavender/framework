<?php
namespace Lavender\Catalog\Composers;

class NavComposer
{

    public function compose($view)
    {
        $view->with('navigation', $this->createNav());
    }


    protected function createNav()
    {
        if(!$root_category = app('current.store')->root_category) return [];

        return $root_category->children;
    }

}