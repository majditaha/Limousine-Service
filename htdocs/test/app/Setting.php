<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['descripion', 'value'];

    public $timestamps = false;

    public static function getValue($name) {
        return cache()->driver('array')->remember("setting_{$name}", 1, function() use ($name) {
            $setting = Setting::whereName($name)->first();
            return optional($setting)->value;
        });
    }
}
