<?php

/**
 * Narrator Markdown Reader for Twilio
 *
 * PHP Version 5.3
 *
 * @category  Router
 * @package   Laravel
 * @author    Brodkin CyberArts <support@brodkinca.com>
 * @copyright 2012 Brodkin CyberArts.
 * @license   All rights reserved.
 * @version   GIT: $Id$
 * @link      http://narrator.pagodabox.com/
 */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::any('/', function() {
    return View::make('hello');
});

Route::any('voice', 'Voice@start');

Route::any('voice/menu/{h1}', 'Voice@menu');
Route::any('voice/menu/{h1}/{h2}', 'Voice@menu');
Route::any('voice/menu/{h1}/{h2}/{h3}', 'Voice@menu');
Route::any('voice/menu/{h1}/{h2}/{h3}/{h4}', 'Voice@menu');
Route::any('voice/menu/{h1}/{h2}/{h3}/{h4}/{h5}', 'Voice@menu');

Route::any('voice/process/{h1}', 'Voice@processMenu');
Route::any('voice/process/{h1}/{h2}', 'Voice@processMenu');
Route::any('voice/process/{h1}/{h2}/{h3}', 'Voice@processMenu');
Route::any('voice/process/{h1}/{h2}/{h3}/{h4}', 'Voice@processMenu');
Route::any('voice/process/{h1}/{h2}/{h3}/{h4}/{h5}', 'Voice@processMenu');

Route::any('voice/content/{h1}', 'Voice@content');
Route::any('voice/content/{h1}/{h2}', 'Voice@content');
Route::any('voice/content/{h1}/{h2}/{h3}', 'Voice@content');
Route::any('voice/content/{h1}/{h2}/{h3}/{h4}', 'Voice@content');
Route::any('voice/content/{h1}/{h2}/{h3}/{h4}/{h5}', 'Voice@content');
Route::any('voice/content/{h1}/{h2}/{h3}/{h4}/{h5}/{h6}', 'Voice@content');
