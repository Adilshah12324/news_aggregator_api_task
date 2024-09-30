<?php

namespace App\Traits\Relationships;

use App\Models\User;

trait BelongsToManyUsers
{
    public function users(){

        return $this->belongsToMany(User::class);
    }
}