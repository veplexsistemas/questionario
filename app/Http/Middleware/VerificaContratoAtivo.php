<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VerificaContratoAtivo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if (is_object(Auth::user()))
      {
        $contratoFuncionario = 
          DB::table("contrato_funcionario AS cf")
            ->where("cf.dt_admissao", "<=", date('Y-m-d'))
            ->whereNull("dt_aviso_previo")
            ->where("cf.cd_pessoa", "=", Auth::user()->cd_pessoa)
            ->orderBy("cf.dt_admissao", "desc")
            ->get()->first();
        
        if (!$contratoFuncionario)
          return redirect("/erro");
      }
      
      return $next($request);
    }
}
