<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'name', 'order', 'parent_id', 'user_role', 'alias'
    ];

    public function pages() {
        return $this->hasMany(Page::class);
    }

    public function parent() {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }
}
