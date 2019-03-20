<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PesquisaRespostaAlternativa extends Model
{
  protected $table = "pesquisa_resposta_alternativa";
  
  protected $primaryKey = ["cd_resposta", "cd_pergunta_opcao_item"];
  
  public $timestamps = false;
  
  public $incrementing = false;
}
