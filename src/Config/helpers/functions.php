<?php


if ( ! function_exists('recursive_merge'))
{
    /**
     * Array merge recursive
     * @param array $arr1
     * @param array $arr2
     * @param array $defaults
     * @return array
     */
    function recursive_merge($arr1, $arr2)
    {
        if(!is_array($arr1) || !is_array($arr2)) return $arr2;

        foreach($arr2 as $key => $val){

            $arr1[$key] = recursive_merge(@$arr1[$key], $val);

        }

        return $arr1;
    }
}




if ( ! function_exists('merge_defaults'))
{
    /**
     * @param array $array
     * @param string $type
     */
    function merge_defaults(array &$array, $type)
    {
        $defaults = Config::get('defaults.'.$type);

        $array = recursive_merge($defaults, $array);
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
    function sort_children(array &$array, $key = 'position', $pos = 1)
    {
        array_walk($array, function(&$v) use (&$pos){$v = [$v, $pos++];});

        uasort($array, function ($a, $b) use ($key){
            if(@$a[0][$key] != @$b[0][$key]) return (@$a[0][$key] < @$b[0][$key]) ? -1 : 1;
            return $a[1] < $b[1] ? -1 : 1;
        });

        array_walk($array, function(&$v){$v = $v[0];});
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




