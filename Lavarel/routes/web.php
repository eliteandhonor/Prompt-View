<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\PromptVersionController;
use App\Http\Controllers\OutcomeController;
use App\Http\Controllers\CommentController;

Route::resource('prompts', PromptController::class);
Route::post('prompts/import', [PromptController::class, 'import'])->name('prompts.import');
Route::get('prompts/export/csv', [PromptController::class, 'exportCsv'])->name('prompts.export.csv');
Route::get('prompts/export/json', [PromptController::class, 'exportJson'])->name('prompts.export.json');
Route::resource('prompt-versions', PromptVersionController::class);
Route::resource('outcomes', OutcomeController::class);
Route::resource('comments', CommentController::class);

/*
// Route::get('/', function () {
//     return view('welcome');
// });
*/
// Updated root route: direct to prompts list
Route::get('/', [PromptController::class, 'index']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
