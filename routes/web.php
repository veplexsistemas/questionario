<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return redirect()->action("PesquisaController@index");
});

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth', 'verificaContrato'], 'prefix' => '/pesquisa'], function(){
  Route::get("/",               "PesquisaController@index");
  Route::get("/responder/{id}", "PesquisaController@responder")->where('id', '[0-9]+');
  Route::post("/registraRespostas", "PesquisaController@registraRespostas");
});

Route::get('/erro', function($msgErro = ""){
  return view("erro")->with("msgErro", "Atenção: Usuário não possui contrato ativo!");
});
