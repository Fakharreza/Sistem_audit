<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditProgress extends Model
{
  
    protected $table = 'audit_progresses';
    
    protected $fillable = ['audit_id', 'domain_name', 'notes'];
}