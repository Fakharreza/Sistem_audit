<?php

namespace App\Http\Controllers;
use App\Models\Domain;
use Illuminate\Http\Request;

class AuditController extends Controller
{
 public function create()
    {
        $domains = Domain::all();

        return view('auditor.create', compact('domains'));
    }
}
