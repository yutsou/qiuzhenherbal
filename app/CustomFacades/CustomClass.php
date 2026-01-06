<?php

namespace App\CustomFacades;

class CustomClass
{
    public static function viewWithTitle($viewWithParams, $title)
    {
        return $viewWithParams->with('head', $title)->with('title', $title.' - '.env("APP_NAME"));
    }
}
