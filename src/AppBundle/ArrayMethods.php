<?php

namespace AppBundle;

class ArrayMethods
{
    public static function valueToKeyAndValue($array)
    {
        $out = array();
        foreach ($array as $value)
        {
            $out[$value] = $value;
        }
        return $out;
    }
}
