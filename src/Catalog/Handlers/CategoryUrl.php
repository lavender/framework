<?php
namespace Lavender\Catalog\Handlers;

class CategoryUrl
{


    public function before_save(&$value)
    {
        // create unique url if one not provided
        if(!$value) $value = uniqid(\Config::get('store.category_url')."/");
    }

}