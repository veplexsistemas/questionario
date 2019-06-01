<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ValidaPesoResposta implements Rule
{
    protected $cdOpcaoItem;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($cdOpcaoItem)
    {
        $this->cdOpcaoItem = $cdOpcaoItem;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $obj = 
            DB::table("pesquisa AS p")
                ->select("poi.vl_peso", "pp.vl_nota_justificativa")
                ->join("pesquisa_pergunta AS pp", "pp.cd_pesquisa", "=", "p.cd_pesquisa")
                ->join("pergunta_opcao AS po", "po.cd_pergunta_opcao", "=", "pp.cd_pergunta_opcao")
                ->join("pergunta_vaga  AS pv", "pv.cd_pergunta", "=", "pp.cd_pergunta")
                ->leftjoin("pergunta_opcao_item AS poi", "poi.cd_pergunta_opcao", "=", "po.cd_pergunta_opcao")    
                ->where("poi.cd_pergunta_opcao_item", "=", $this->cdOpcaoItem)
                ->whereNotNull("pp.vl_nota_justificativa")
                ->whereNotNull("poi.vl_peso")
                ->first();
                
        if (is_object($obj))
            return (((float)$obj->vl_peso < (float)$obj->vl_nota_justificativa) ? false : true);
        else
            return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Justificativa obrigatória.';
    }
}
