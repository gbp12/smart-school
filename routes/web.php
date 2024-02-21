<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', "App\Http\Controllers\MainController@index")->name("main.index");
Route::get('/main', "App\Http\Controllers\MainController@index")->name("main.index");
/* Consigue los consumos de luz del ultimo año dividido en meses */
Route::get('/getMonthlyElectricity', "App\Http\Controllers\MainController@getMonthlyElectricity");
/* Consigue los consumos de agua del ultimo año dividido en meses */
Route::get('/getMonthlyWater', "App\Http\Controllers\MainController@getMonthlyWater");
/* Consigue los consumos de luz del ultimo mes dividido en semanas */
Route::get('/getWeeklyElectricity', "App\Http\Controllers\MainController@getWeeklyElectricity");
/* Consigue los consumos de luz del ultimo mes dividido en semanas */
Route::get('/getWeeklyWater', "App\Http\Controllers\MainController@getWeeklyWater");
/*Consigue los consumos totales de las ultimas 8 horas*/
Route::get('/getLastEightHours', 'App\Http\Controllers\MainController@getLastEightHours')->name("main.eightHour");;
