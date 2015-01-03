<?php

if(!function_exists('underscore')){
    function underscore($value)
    {
        return strtolower(str_replace('.', '_', $value));
    }
}

