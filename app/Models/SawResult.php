<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SawResult extends Model
{
    protected $guarded = [];

    public function audit() {
        return $this->belongsTo(Audit::class);
    }
    public function domain() {
        return $this->belongsTo(Domain::class);
    }
}
