<?php

Route::get('auth/me', function () {
    return auth()->user();
})
    ->middleware('auth')
    ->name('auth.me');

Route::post('login', 'Auth\LoginController@login')
    ->name('login');

Route::get('logout', function () {
    auth()->logout();
});

Route::post('logout', 'Auth\LoginController@logout')
    ->name('logout');

Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')
    ->name('password.email');

Route::post('password/reset', 'Auth\ResetPasswordController@reset')
    ->name('password.update');

Route::get('{team}/{project}/{build}/console', 'ConsoleController')
    ->name('builds.console')
    ->middleware('disableBuffer');

Route::fallback(function() {
    return view('page');
});
