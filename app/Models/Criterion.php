<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    protected $guarded = [];

    public function evaluations() {
        return $this->hasMany(SawEvaluation::class);
    }
}
