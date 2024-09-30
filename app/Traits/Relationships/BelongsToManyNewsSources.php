<?php

namespace App\Traits\Relationships;

use App\Models\NewsSource;

trait BelongsToManyNewsSources
{
    public function newsSources(){

        return $this->belongsToMany(NewsSource::class);
    }
}