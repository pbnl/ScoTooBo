<?php

namespace AppBundle;


class ArrayMethods
{
    public static function valueToKeyAndValue($array)
    {
        $out = Array();
        foreach ($array as $value)
        {
            $out[$value] = $value;
        }
        return $out;
    }
}