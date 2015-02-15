<?php


if ( ! function_exists('entity'))
{
    function entity($e)
    {
        return app("entity.{$e}");
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




