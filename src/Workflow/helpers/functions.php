<?php

if ( ! function_exists('workflow'))
{
    function workflow($workflow)
    {
        return function() use ($workflow){

            return app('workflow.resolver')->resolve($workflow);

        };
    }
}
