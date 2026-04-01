<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $guarded = [];

    public function domains() {
        return $this->belongsToMany(Domain::class, 'audit_domain');
    }
    public function responses() {
        return $this->hasMany(AuditResponse::class);
    }
    public function sawEvaluations() {
        return $this->hasMany(SawEvaluation::class);
    }
    public function sawResults() {
        return $this->hasMany(SawResult::class);
    }
}
