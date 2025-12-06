<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/',[MainController::class, 'viewTest']);
