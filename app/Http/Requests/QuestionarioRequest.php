<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Rules\ValidaPesoResposta;

class QuestionarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $rules = [];

        for ($i = 1; $i <= $request->get("f_qt_perguntas"); $i++)
        {
            if (strlen(trim($request->get("f_ds_justificativa_{$i}"))))    
                continue;
            else
            {
                $cdOpcaoItem = $request->get("f_ds_resposta_{$i}");
                $rules["f_ds_justificativa_{$i}"] = new ValidaPesoResposta($cdOpcaoItem);
            }
                
        }

        return $rules;
    }
}
