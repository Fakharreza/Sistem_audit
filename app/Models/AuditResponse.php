<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditResponse extends Model
{
    protected $guarded = [];

    public function audit() {
        return $this->belongsTo(Audit::class);
    }
    public function question() {
        return $this->belongsTo(CobitQuestion::class, 'cobit_question_id');
    }
}