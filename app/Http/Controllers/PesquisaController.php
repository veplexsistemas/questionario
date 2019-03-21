<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PesquisaController extends Controller
{
  public function index()
  {
    if ($contratoFuncionario = $this->obtemContratoFuncionario())
    {
      $pesquisa =
        DB::table("pesquisa AS p")
          ->select("p.cd_pesquisa", "p.nm_pesquisa")
          ->join("pesquisa_pergunta AS pp", "pp.cd_pesquisa", "=", "p.cd_pesquisa")
          ->join("pergunta_vaga     AS pv", "pv.cd_pergunta", "=", "pp.cd_pergunta")
          ->where("dt_inicial", "<=", date("Y-m-d"))
          ->where("dt_final",   ">=", date("Y-m-d"))
          ->where("pv.cd_vaga", "=", $contratoFuncionario->cd_vaga)
          ->distinct()
          ->get();
      
      return view("seleciona_pesquisa")->with("data", $pesquisa);
    }
    else
      return view("erro")->with("msgErro", "Atenção: Usuário não possui contrato ativo!");
  }
  
  public function responder($id)
  {
    $questionario =
      DB::table("pesquisa AS p")
        ->select("p.cd_pesquisa", "pp.cd_pergunta", "pp.ds_pergunta", "pp.id_tipo", "pp.id_obrigatorio", "poi.cd_pergunta_opcao_item", "poi.nm_pergunta_opcao_item", "poi.nr_ordem")
          ->join("pesquisa_pergunta AS pp", "pp.cd_pesquisa", "=", "p.cd_pesquisa")
          ->join("pergunta_opcao AS po", "po.cd_pergunta_opcao", "=", "pp.cd_pergunta_opcao")
          ->join("pergunta_vaga  AS pv", "pv.cd_pergunta", "=", "pp.cd_pergunta")
          ->leftjoin("pergunta_opcao_item AS poi", "poi.cd_pergunta_opcao", "=", "po.cd_pergunta_opcao")
          ->where("p.cd_pesquisa", "=", $id)
          ->where("pp.id_tipo", "<>", "4")
          ->where("pv.cd_vaga", "=", $this->obtemContratoFuncionario()->cd_vaga)
          ->distinct()
          ->orderBy("p.cd_pesquisa")
          ->orderBy("pp.cd_pergunta")
          ->orderBy("poi.nr_ordem")
          ->get();
    
    return view('questionario')->with("data", $questionario);
  }
  
  public function registraRespostas(Request $request)
  {
    $t_qt_perguntas = $request->get("f_qt_perguntas");
    
    for ($i = 1; $i <= $t_qt_perguntas; $i++)
    {
      $t_cd_pergunta      = $request->get("f_cd_pergunta_{$i}");
      $t_id_tipo_pergunta = $request->get("f_id_tipo_pergunta_{$i}");
      $t_ds_resposta      = $request->get("f_ds_resposta_{$i}");
      
      $PesquisaResposta = new \App\PesquisaResposta();
      $PesquisaResposta->cd_pergunta         = $t_cd_pergunta;
      $PesquisaResposta->cd_usuario_resposta = Auth::user()->cd_pessoa;
      $PesquisaResposta->dt_resposta         = date('c');
      $PesquisaResposta->ds_resposta         = ($t_id_tipo_pergunta != 3 ? $t_ds_resposta : "");
      $PesquisaResposta->save();
      
      if ($t_id_tipo_pergunta == 3)
      {
        $PesquisaRespostaAlt = new \App\PesquisaRespostaAlternativa();
        $PesquisaRespostaAlt->cd_resposta            = $PesquisaResposta->cd_resposta;
        $PesquisaRespostaAlt->cd_pergunta_opcao_item = (int) $t_ds_resposta;
        $PesquisaRespostaAlt->save();
      }
    }
    
    return redirect("/pesquisa")->with("status", "Pesquisa respondida com sucesso!");
  }
  
  protected function obtemContratoFuncionario()
  {
    return
      DB::table("contrato_funcionario AS cf")
        ->where("cf.dt_admissao", "<=", date('Y-m-d'))
        ->whereNull("dt_aviso_previo")
        ->where("cf.cd_pessoa", "=", Auth::user()->cd_pessoa)
        ->orderBy("cf.dt_admissao", "desc")
        ->get()->first();
  }
}