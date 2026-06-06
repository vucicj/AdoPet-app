<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return file_exists(public_path('index.html'))
        ? response()->file(public_path('index.html'))
        : response()->json(['status' => 'AdoPet API running']);
})->where('any', '.*');
