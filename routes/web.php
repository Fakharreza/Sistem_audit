<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ManagerController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'manajer') {
        return redirect()->route('manager.dashboard');
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
    Route::get('/manager/dashboard', [ManagerController::class, 'dashboard'])->name('manager.dashboard');
    Route::get('/manager/audit/{audit}/evaluate', [ManagerController::class, 'evaluate'])->name('manager.audit.evaluate');
    Route::post('/manager/audit/{audit}/calculate', [ManagerController::class, 'calculate'])->name('manager.audit.calculate');
    Route::get('/manager/audit/{audit}/result', [ManagerController::class, 'showResult'])->name('manager.audit.result');
    // --- MANAJEMEN KRITERIA SAW ---
    Route::get('/manager/criteria', [App\Http\Controllers\CriterionController::class, 'index'])->name('manager.criteria.index');
    Route::post('/manager/criteria', [App\Http\Controllers\CriterionController::class, 'store'])->name('manager.criteria.store');
    Route::put('/manager/criteria/all-update', [App\Http\Controllers\CriterionController::class, 'updateAll'])->name('manager.criteria.updateAll');
    Route::delete('/manager/criteria/{criterion}', [App\Http\Controllers\CriterionController::class, 'destroy'])->name('manager.criteria.destroy');
    Route::post('/manager/criteria/reset', [App\Http\Controllers\CriterionController::class, 'reset'])->name('manager.criteria.reset');
    Route::post('/manager/audit/{audit}/progress', [App\Http\Controllers\ManagerController::class, 'storeProgress'])->name('manager.audit.progress.store');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';