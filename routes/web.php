<?php

Route::any('github/{project}', 'GithubController')
    ->name('github')
    ->middleware('github');

Route::get('auth/me', 'AuthController@me')
    ->middleware('auth')
    ->name('auth.me');

Route::post('login', 'Auth\LoginController@login')
    ->name('login');

Route::any('logout', 'Auth\LoginController@logout')
    ->name('logout');

Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')
    ->name('password.email');

Route::post('password/reset', 'Auth\ResetPasswordController@reset')
    ->name('password.update');

Route::get('{team}/{project}/{number}/console', 'ConsoleController')
    ->name('builds.console')
    ->middleware('disableBuffer');

Route::fallback('PageController');
