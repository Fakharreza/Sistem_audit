<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CobitQuestion extends Model
{
    protected $guarded = [];

    public function domain() {
        return $this->belongsTo(Domain::class);
    }
}
