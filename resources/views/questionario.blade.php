<?php
  use VMaker\VHtml;
  use VMaker\VPanel;
  use VMaker\VForm;
  
  $js = "
    $(document).ready(function(){
      $('form').submit(function(){
        $('#submit').attr('disabled', true);
      });
    });";
  
  //Html
  $html = new VHtml("layouts.app");
  $html->openSection("content");
  $html->addScript($js);
  
  //Panel
  $panel = new VPanel();
  $panel->setClass("panel panel-primary");
  $panel->addHeading("Questionário");
  
  //Form
  $form = new VForm();
  $form->setAction("PesquisaController@registraRespostas");
  $form->setErrors($errors);
  
  if (is_object($data))
  {
    $cd_pergunta_old = "";
    
    $t_qt_perguntas = 0;
    
    foreach ($data as $obj)
    {
      $t_cd_pergunta = $obj->cd_pergunta;
      
      if ($t_cd_pergunta != $cd_pergunta_old)
      {
        if ($cd_pergunta_old)
        {
          $form->addInputField($ds_resposta);
          
          if (isset($ds_justificativa) && is_object($ds_justificativa))
          {
            $form->addInputField($ds_justificativa);
            $form->addInputField($id_identifica);
          }

          unset($ds_resposta);
          unset($ds_justificativa);
        }
        
        $t_qt_perguntas++;
        
        $id = "ds_resposta_{$t_qt_perguntas}";
        
        //cd_pergunta
        $cd_pergunta = new \VMaker\VInputHidden("cd_pergunta_{$t_qt_perguntas}", $obj->cd_pergunta);
        $form->addInputField($cd_pergunta);
        
        //id_tipo
        $id_tipo = new \VMaker\VInputHidden("id_tipo_pergunta_{$t_qt_perguntas}", $obj->id_tipo);
        $form->addInputField($id_tipo);
        
        switch ($obj->id_tipo)
        {
          case "1": //Texto
            $ds_resposta = new \VMaker\VInputText($id);
            $ds_resposta->setLabel($obj->ds_pergunta);
            $ds_resposta->setExtraLabel($obj->ds_comentario);
            $ds_resposta->setRequired($obj->id_obrigatorio);
          break;

          case "2": //Número
            $ds_resposta = new \VMaker\VInputNumber($id);
            $ds_resposta->setLabel($obj->ds_pergunta);
            $ds_resposta->setExtraLabel($obj->ds_comentario);
            $ds_resposta->setRequired($obj->id_obrigatorio);
          break;

          case "3": //Seleção
            $ds_resposta = new \VMaker\VInputSelect($id);
            $ds_resposta->setLabel($obj->ds_pergunta);

            if ($obj->ds_analise)
              $ds_resposta->setExtraLabel("<b>Analise do responsável sobre sua última avaliação: </b>".$obj->ds_analise."</br></br>".$obj->ds_comentario);
            else
              $ds_resposta->setExtraLabel($obj->ds_comentario);

            $ds_resposta->setRequired($obj->id_obrigatorio);
            $ds_resposta->setOpcional();
            $ds_resposta->addOption($obj->cd_pergunta_opcao_item, $obj->nm_pergunta_opcao_item);
          break;
        
        /*
          case "4": //Multiseleção
            $nr_pergunta = new VMaker\VInputDualList($id);
            $nr_pergunta->setLabel($obj->ds_pergunta);
            $nr_pergunta->setRequired($obj->id_obrigatorio);
            $nr_pergunta->addOption($obj->cd_pergunta_opcao_item, $obj->nm_pergunta_opcao_item);
          break;
         */
        }

        if (isset($obj->vl_nota_justificativa) && ($t_cd_pergunta != $cd_pergunta_old))
        {
          $vlNotaJustificativa = ($obj->vl_nota_justificativa - 10) / 10;

          $ds_justificativa = new \VMaker\VInputText("ds_justificativa_{$t_qt_perguntas}");
          $ds_justificativa->setExtraLabel("(Justificativa) Favor justificar se a sua resposta for menor ou igual a {$vlNotaJustificativa}.");

          $id_identifica = new VMaker\VInputSelect("id_identificado_{$t_qt_perguntas}");
          $id_identifica->setLabel("Deseja identificar-se?");
          $id_identifica->addOption('0', 'Não');
          $id_identifica->addOption('1', 'Sim');
        }
      }
      elseif ($ds_resposta instanceof \VMaker\VInputSelect)
        $ds_resposta->addOption($obj->cd_pergunta_opcao_item, $obj->nm_pergunta_opcao_item);
      
      $cd_pergunta_old = $t_cd_pergunta;
    }
    
    $form->addInputField($ds_resposta);

    if (isset($ds_justificativa) && is_object($ds_justificativa))
    {
      $form->addInputField($ds_justificativa);
      $form->addInputField($id_identifica);
    }

    //qt_perguntas
    $qt_perguntas = new \VMaker\VInputHidden("qt_perguntas", $t_qt_perguntas);
    $form->addInputField($qt_perguntas);

    $submit = new \VMaker\VInputSubmit("submit", "Enviar");
    $submit->setClass("btn btn-success");
    $submit->setStyle("width: 100%");
    $form->addInputField($submit);
  }
  
  if (isset($data[0]->ds_pesquisa))
  {
    $panelDescricao = new VPanel();
    $panelDescricao->setClass("panel panel-info");
    $panelDescricao->addHeading("<i class=\"fas fa-info-circle\"></i> ".nl2br($data[0]->ds_pesquisa));
    
    $panel->addBody($panelDescricao->make() . $form->make());
  }
  else
    $panel->addBody($form->make());
  
  $html->addObject($panel);
  
  $html->addContent("<a href=\"/pesquisa/\" class=\"btn btn-info\"><i class=\"fas fa-backward\"></i> Voltar</a>");
  
  echo $html->make();