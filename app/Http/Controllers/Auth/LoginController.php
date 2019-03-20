<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/pesquisa';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    public function username()
    {
        return 'ds_apelido';
    }
    
    protected function attemptLogin(Request $request)
    {
      $user = \App\User::where([
          'ds_apelido' => $request->ds_apelido,
          'ds_senha'   => md5($request->password),
          'id_ativo'   => 1
      ])->first();
      
      if ($user)
      {
        $this->guard()->login($user, $request->has('remember'));
        return true;
      }
      
      return false;
    }
    
}
