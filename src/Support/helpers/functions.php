<?php


if ( ! function_exists('paginate'))
{
    /**
     * todo make a better html paginator
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $collection
     * @return string
     */
    function paginate(\Illuminate\Contracts\Pagination\LengthAwarePaginator $collection)
    {
        $current = $collection->currentPage();

        $per = $collection->perPage();

        $count = $collection->count();

        $from = $current * $per;

        $to = $from + $count;

        $html = '';// "From {$from} to {$to}";

        $previous = $current > 1 ? $collection->url("?page=".($current - 1)) : false;

        $next = $collection->hasMorePages() ? $collection->nextPageUrl() : false;

        if($previous) $html .= '<li><a href="'.$previous.'">prev</a></li>';

        if($next) $html .= '<li><a href="'.$next.'">next</a></li>';

        return '<ul>'.$html.'</ul>';
    }
}


if ( ! function_exists('entity'))
{
    function entity($e)
    {
        return app("entity.{$e}");
    }
}


if ( ! function_exists('form'))
{
    function form($form = null, $params = [])
    {
        if($form === null) return Form::getInstance();

        return Form::make($form, $params);
    }
}


if ( ! function_exists('append_section'))
{
    function append_section($section, $config)
    {
        $injector = app('view.injector');

        if(is_array($config)){

            return $injector->append($section, $config);

        }

        return $injector->inject($section, $config);
    }
}


if ( ! function_exists('compose_section'))
{
    function compose_section($layout, $section, $content)
    {
        view()->composer($layout,function($view) use ($section, $content){

            append_section($section, $content);

        });
    }
}


if ( ! function_exists('recursive_merge'))
{
    /**
     * Array merge recursive
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    function recursive_merge($arr1, $arr2)
    {
        if(!is_array($arr1) || !is_array($arr2)) return $arr2;

        foreach($arr2 as $key => $val2){

            $val1 = isset($arr1[$key]) ? $arr1[$key] : [];

            $arr1[$key] = recursive_merge($val1, $val2);

        }

        return $arr1;
    }
}


if ( ! function_exists('sort_children'))
{
    /**
     * Stable sort children using Schwartzian Transforms to pre-decorate
     * the passed array, sort, and un-decorate.
     *
     * @param array $array
     * @param string $key index used for positions
     * @param int $pos default starting position
     */
    function sort_children(array &$array, $key = 'position')
    {
        $pos = 1;

        // decorate array
        foreach($array as $k => $v) $array[$k] = [$v, $pos++];

        // sort array
        uasort($array, function ($a, $b) use ($key){

            $_a = isset($a[0][$key]) ? $a[0][$key] : null;

            $_b = isset($b[0][$key]) ? $b[0][$key] : null;

            if($_a != $_b) return ($_a < $_b) ? -1 : 1;

            return $a[1] < $b[1] ? -1 : 1;
        });

        // un-decorate array
        foreach($array as $k => $v) $array[$k] = $v[0];
    }
}


if ( ! function_exists('array_walk_depth'))
{
    /**
     * Walk an array at a specified depth. For the iterator, foreach seems much
     * faster than array_walk.
     *
     * @param array $arr
     * @param int $depth a depth of 1 has same effect as array_walk
     * @param callback $callback If callback needs to be working with the actual
     * values of the array, specify the first parameter of callback as a reference.
     * @param string|null $index match only children nodes within index
     */
    function array_walk_depth(array &$arr, $depth, $callback, $index = null)
    {
        $depth--;

        foreach($arr as $key => &$val){

            if(!$depth){

                $callback($val, $key);

            } elseif(is_array($val)){

                // skip parent nodes that do not match $index
                if(!$index || ($depth > 1) || ($depth == 1 && $key == $index)){

                    array_walk_depth($val, $depth, $callback, $index);

                }

            }

        }
    }
}


if ( ! function_exists('attr'))
{
    /**
     * Build an HTML attribute string from an array.
     *
     * @param array $attrs
     * @return string
     */
    function attr(array $attrs)
    {
        $html = [];

        foreach($attrs as $key => $value){

            if(is_array($value)) $value = json_encode($value);

            if(is_bool($value) && $value){

                $html[$key] = $key;

            } elseif($value){

                $html[$key] = $key.'="'.e($value).'"';

            }

        }

        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }
}

if ( ! function_exists('mailer'))
{
    /**
     * @param $email
     * @param $name
     * @param $subject
     * @param $template
     * @param array $params
     */
    function mailer($email, $name, $subject, $template, array $params = [])
    {
        app('mailer')->queueOn(
            'default',
            $template,
            $params,
            function ($message) use ($email, $name, $subject){
                $message
                    ->to($email, $name)
                    ->subject($subject);
            }
        );
    }
}

