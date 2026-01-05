<?php


use Illuminate\Support\Str;

if (!function_exists('generate_uuid')) {
    function generate_uuid()
    {
        return (string) Str::uuid();
    }
}

if (!function_exists('format_date')) {
    function format_date($date)
    {
        return $date ? $date->format(config('app.date_format')) : null;
    }
}
