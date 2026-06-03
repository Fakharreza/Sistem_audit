<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Criterion extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function evaluations() {
        return $this->hasMany(SawEvaluation::class);
    }
}
