<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $guarded = [];

    public function questions() {
        return $this->hasMany(CobitQuestion::class);
    }
    public function audits() {
        return $this->belongsToMany(Audit::class, 'audit_domain');
    }
}