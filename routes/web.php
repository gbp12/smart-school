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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/main', "App\Http\Controllers\MainController@index")->name("main.index");
/* Consigue los consumos de luz anuales */
Route::get('/getYearlyElectricity', "App\Http\Controllers\MainController@getYearlyElectricity");
/* Consigue los consumos del ultimo año dividido en meses */
Route::get('/getMonthlyElectricity', "App\Http\Controllers\MainController@getMonthlyElectricity");
/*Consigue los consumos totales de las ultimas 8 horas*/
Route::get('/getLastEightHours', 'App\Http\Controllers\MainController@getLastEightHours')->name("main.eightHour");;
