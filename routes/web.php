<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'manajer') {
        return redirect()->route('manajer.dashboard');
    }
    return redirect()->route('auditor.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware(['auth', 'role:auditor'])->group(function () {
   Route::get('/auditor/dashboard', [AuditController::class, 'dashboard'])->name('auditor.dashboard');
    Route::get('/auditor/audit/create', [AuditController::class, 'create'])->name('auditor.audit.create');
    Route::post('/auditor/audit/store', [AuditController::class, 'store'])->name('auditor.audit.store');
    Route::get('/auditor/audit/{audit}/kuesioner', [AuditController::class, 'showKuesioner'])->name('auditor.audit.kuesioner');
    Route::post('/auditor/audit/{audit}/kuesioner', [AuditController::class, 'storeKuesioner'])->name('auditor.audit.store_kuesioner');
});


Route::middleware(['auth', 'role:manajer'])->group(function () {
    Route::get('/manajer/dashboard', function () {
        return view('manajer.dashboard'); 
    })->name('manajer.dashboard');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';