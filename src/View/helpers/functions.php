<?php

if ( ! function_exists('uncamel'))
{
    function uncamel($str)
    {
        return explode(',', preg_replace_callback('/([A-Z])/', function($c){
            return ','.strtolower($c[0]);
        }, $str));
    }
}

// todo move responsibility to a Theme singleton and cache results.
if ( ! function_exists('asset_fallback'))
{
    function asset_fallback($asset)
    {
        $asset_path = $asset;

        foreach(app('current.theme')->fallbacks as $fallback){

            $asset_path = 'assets/'.$fallback.'/'.$asset;

            $filepath = public_path($asset_path);

            if(file_exists($filepath)) return $asset_path;

        }

        return $asset_path;
    }
}

if ( ! function_exists('script'))
{
    function script($asset)
    {
        return function() use ($asset) {
            return HTML::script(asset_fallback($asset));
        };
    }
}

if ( ! function_exists('style'))
{
    function style($asset)
    {
        return function() use ($asset){
            return HTML::style(asset_fallback($asset));
        };
    }
}

if ( ! function_exists('meta'))
{
    function meta($name, $content)
    {
        return function() use($name, $content){
            //TODO add HTML type for meta
            return \View::make('layouts.elements.meta')
                ->with('name', $name)
                ->with('content', $content)
                ->render();
        };
    }
}