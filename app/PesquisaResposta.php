<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PesquisaResposta extends Model
{
  protected $table = "pesquisa_resposta";
  
  protected $primaryKey = 'cd_resposta';
  
  public $timestamps = false;
}
