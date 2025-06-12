<?php

use Illuminate\Support\Facades\Route;

Route::get('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');
