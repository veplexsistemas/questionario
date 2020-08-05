<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\QuestionarioRequest;

class PesquisaController extends Controller
{
  protected $dados;
  
  public function sqlBase()
  {
    unset($this->dados);
    
    $this->dados = 
      DB::table("pesquisa AS p")
        ->join("pesquisa_pergunta AS pp", "pp.cd_pesquisa", "=", "p.cd_pesquisa")
        ->join("pergunta_opcao AS po", "po.cd_pergunta_opcao", "=", "pp.cd_pergunta_opcao")
        ->join("pergunta_vaga  AS pv", "pv.cd_pergunta", "=", "pp.cd_pergunta")
        ->leftjoin("pergunta_opcao_item AS poi", "poi.cd_pergunta_opcao", "=", "po.cd_pergunta_opcao")    
        ->leftjoin("pesquisa_resposta AS pres", function($join){
          $join->on('pres.cd_pergunta', '=', 'pp.cd_pergunta');
          $join->where(DB::raw("TO_CHAR(pres.dt_resposta, 'MM-YYYY')"), '=' , date("m-Y"));
          $join->where("pres.cd_usuario_resposta", "=", Auth::user()->cd_pessoa);
        })
        ->where("pp.id_tipo", "<>", "4")
        ->whereNull("pres.cd_pergunta")
        ->where("p.dt_inicial", "<=", date("Y-m-d"))
        ->where("p.dt_final",   ">=", date("Y-m-d"))
        ->where("pv.cd_vaga", "=", $this->obtemContratoFuncionario()->cd_vaga)
        ->distinct();
  }
  
  public function index()
  {
    $this->sqlBase();
      
    $pesquisa = 
      $this->dados
        ->select("p.cd_pesquisa", "p.nm_pesquisa")
        ->paginate();
/*
    $avaliacao = DB::table("pesquisa_pergunta AS pp")
      ->join("contrato_funcionario AS cf", "cf.cd_vaga", "=", "pp.cd_vaga_responsavel")
      ->join("pesquisa_resposta AS pr", "pr.cd_pergunta", "=", "pp.cd_pergunta")
      ->join("pessoa AS p", "p.cd_pessoa", "=", "pr.cd_usuario_resposta")
      ->whereRaw("(dt_aviso_previo IS NULL OR dt_aviso_previo > CURRENT_DATE)")
      ->where("cf.cd_pessoa", "=", Auth::user()->cd_pessoa)
      ->where("pp.cd_pesquisa", "=", "45")
      ->where("pr.id_analisada", "<>", "1")
      ->whereRaw("pr.dt_resposta >= TO_CHAR(CURRENT_DATE , 'YYYY-MM-01')::DATE - INTERVAL '1' MONTH")
      ->whereRaw("pr.dt_resposta < TO_CHAR(CURRENT_DATE , 'YYYY-MM-01')::DATE")
      ->whereRaw("pr.ds_justificativa IS NOT NULL")
      ->selectRaw("pr.cd_resposta, pr.cd_usuario_resposta, p.nm_pessoa, pr.cd_pergunta, 
               TO_CHAR(pr.dt_resposta, 'DD/MM/YYYY') AS dt_resposta, pr.ds_justificativa, pr.id_identificado")
      ->orderBy("pr.dt_resposta")
      ->get();

    $nota = DB::table("pesquisa_pergunta AS pp")
      ->join("contrato_funcionario AS cf", "cf.cd_vaga", "=", "pp.cd_vaga_responsavel")
      ->join("pesquisa_resposta AS pr", "pr.cd_pergunta", "=", "pp.cd_pergunta")
      ->join("pessoa AS p", "p.cd_pessoa", "=", "pr.cd_usuario_resposta")
      ->join("pesquisa_resposta_alternativa AS pra", "pra.cd_resposta", "=","pr.cd_resposta")
      ->join("pergunta_opcao_item AS poi", "poi.cd_pergunta_opcao_item", "=","pra.cd_pergunta_opcao_item")
      ->whereRaw("(dt_aviso_previo IS NULL OR dt_aviso_previo > CURRENT_DATE)")
      ->where("cf.cd_pessoa", "=", Auth::user()->cd_pessoa)
      ->where("pp.cd_pesquisa", "=", "45")
      ->where("poi.vl_peso", "<>", null)
      ->whereRaw("pr.dt_resposta >= TO_CHAR(CURRENT_DATE , 'YYYY-MM-01')::DATE - INTERVAL '1' MONTH")
      ->whereRaw("pr.dt_resposta < TO_CHAR(CURRENT_DATE , 'YYYY-MM-01')::DATE")
      ->selectRaw("ROUND(SUM(poi.vl_peso) / COUNT(*), 1) AS vl_media, TO_CHAR(CURRENT_DATE - INTERVAL '1' MONTH, 'MM/YYYY') AS dt_mes")
      ->get();
    
    $comunicacao = DB::table("pesquisa_pergunta AS pp")
      ->join("pesquisa_resposta AS pr", "pr.cd_pergunta", "=", "pp.cd_pergunta")
      ->join("pessoa AS p", "p.cd_pessoa", "=", "pr.cd_usuario_analise")
      ->where("pp.cd_pesquisa", "=", "45")
      ->where("pr.cd_usuario_resposta", "=", "50020")
      ->whereRaw("pr.dt_analise >= TO_CHAR(CURRENT_DATE , 'YYYY-MM-01')::DATE - INTERVAL '1' MONTH")
      ->whereRaw("pr.dt_analise < TO_CHAR(CURRENT_DATE , 'YYYY-MM-01')::DATE + INTERVAL '1' MONTH")
      ->selectRaw("pp.ds_pergunta, COALESCE(pr.ds_analise, 'Sem descrição') AS ds_analise, pr.cd_usuario_analise, p.nm_pessoa,
                   TO_CHAR(pr.dt_analise, 'DD/MM/YYYY') AS dt_analise, pr.ds_justificativa")
      ->get();
*/

 

    return view("seleciona_pesquisa")->with("data", $pesquisa);
/*                             
                                     ->with("avaliacao", $avaliacao)
                                     ->with("nota", $nota)
                                     ->with("comunicacao", $comunicacao);
*/
  }
  
  public function responder($id)
  {
    $this->sqlBase();
    
    $questionario =
      $this->dados
        ->select("p.cd_pesquisa", "pp.nr_ordem AS nr_ordem_pergunta", "pp.cd_pergunta", "pp.ds_pergunta", "pp.id_tipo", 
                 "pp.id_obrigatorio", "poi.cd_pergunta_opcao_item", "poi.nm_pergunta_opcao_item", 
                 "poi.nr_ordem", "pp.ds_comentario", "p.ds_pesquisa", "pp.vl_nota_justificativa", "poi.vl_peso")
        ->selectRaw("(SELECT ds_analise 
                        FROM pesquisa_resposta 
                       WHERE cd_usuario_resposta = ".Auth::user()->cd_pessoa." 
                         AND cd_pergunta = pp.cd_pergunta
                         AND dt_resposta >= TO_CHAR(CURRENT_DATE , 'YYYY-MM-01')::DATE - INTERVAL '1' MONTH 
                         AND dt_resposta < TO_CHAR(CURRENT_DATE , 'YYYY-MM-01')::DATE
                       LIMIT 1) AS ds_analise")
        ->where("p.cd_pesquisa", "=", $id)
        ->orderBy("p.cd_pesquisa")
        ->orderBy("pp.nr_ordem")
        ->orderBy("pp.cd_pergunta")
        ->orderBy("poi.nr_ordem")
        ->get();
     
    return view('questionario')->with("data", $questionario);
  }
  
  public function registraAnalise(QuestionarioRequest $request)
  {
    $t_qt_perguntas = $request->get("f_qt_perguntas");

    for ($i = 1; $i <= $t_qt_perguntas; $i++)
    {
      $t_cd_resposta = $request->get("f_cd_resposta_{$i}");
      $t_ds_analise  = $request->get("f_ds_analise_{$i}");
      
      $resposta = \App\PesquisaResposta::find($t_cd_resposta);
      $resposta->id_analisada       = 1;
      $resposta->ds_analise         = $t_ds_analise;
      $resposta->dt_analise         = date('Y-m-d');
      $resposta->cd_usuario_analise = Auth::user()->cd_pessoa;

      $resposta->save();
    }

    return $this->index();
  }

  public function registraRespostas(QuestionarioRequest $request)
  {
    $t_qt_perguntas = $request->get("f_qt_perguntas");
    
    for ($i = 1; $i <= $t_qt_perguntas; $i++)
    {
      $t_cd_pergunta      = $request->get("f_cd_pergunta_{$i}");
      $t_id_tipo_pergunta = $request->get("f_id_tipo_pergunta_{$i}");
      $t_ds_resposta      = $request->get("f_ds_resposta_{$i}");
      $t_ds_justificativa = $request->get("f_ds_justificativa_{$i}");
      $t_id_identificado  = $request->get("f_id_identificado_{$i}");
      
      $PesquisaRespostaE = DB::table('pesquisa_resposta')
        ->whereRaw("cd_usuario_resposta = ". Auth::user()->cd_pessoa)
        ->whereRaw("cd_pergunta = {$t_cd_pergunta}")
        ->whereRaw("dt_resposta >= TO_CHAR(CURRENT_DATE , 'YYYY-MM-01')::DATE")
        ->whereRaw("dt_resposta < TO_CHAR(CURRENT_DATE , 'YYYY-MM-01')::DATE + INTERVAL '1' MONTH")
        ->first();

      if ($PesquisaRespostaE)
        continue;
      
      $PesquisaResposta = new \App\PesquisaResposta();
      $PesquisaResposta->cd_pergunta         = $t_cd_pergunta;
      $PesquisaResposta->cd_usuario_resposta = Auth::user()->cd_pessoa;
      $PesquisaResposta->dt_resposta         = date('c');
      $PesquisaResposta->ds_resposta         = ($t_id_tipo_pergunta != 3 ? $t_ds_resposta : "");
      $PesquisaResposta->ds_justificativa    = $t_ds_justificativa;
      $PesquisaResposta->id_identificado     = $t_id_identificado;
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