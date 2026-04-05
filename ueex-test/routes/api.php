<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;

Route::post('/company', [CompanyController::class, 'upsert']);
Route::get('/company/{edrpou}/versions', [CompanyController::class, 'versions']);
