<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discipline extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'icon_file',
    ];

    public function sections() {
        return $this->hasMany(Section::class)->orderBy('order', 'ASC');
    }

    public function variants() {
        return $this->hasMany(Variant::class);
    }

    // Finished section is determined by presence of 5 answered practices of type training
    // Therefore unfinished section is where there are less than 5 answered practices
    public function getFirstUnfinishedSection($user) {
        $finishedSectionIds = $user->finishedSections->pluck('section_id');

        return $this->sections()
            ->whereNotIn('id', $finishedSectionIds)
            ->orderBy('order', 'ASC')
            ->first();
    }
}
