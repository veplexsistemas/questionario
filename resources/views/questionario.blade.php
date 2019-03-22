<?php
  use VMaker\VHtml;
  use VMaker\VPanel;
  use VMaker\VForm;
  
  //Html
  $html = new VHtml("layouts.app");
  $html->openSection("content");
  
  //Panel
  $panel = new VPanel();
  $panel->setClass("panel panel-primary");
  $panel->addHeading("Questionário");
  
  //Form
  $form = new VForm();
  $form->setAction("PesquisaController@registraRespostas");
  
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
          unset($ds_resposta);
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
         * 
         */
        }
      }
      elseif ($ds_resposta instanceof \VMaker\VInputSelect)
        $ds_resposta->addOption($obj->cd_pergunta_opcao_item, $obj->nm_pergunta_opcao_item);
      
      $cd_pergunta_old = $t_cd_pergunta;
    }
    
    $form->addInputField($ds_resposta);
    
    //qt_perguntas
    $qt_perguntas = new \VMaker\VInputHidden("qt_perguntas", $t_qt_perguntas);
    $form->addInputField($qt_perguntas);
    
    
    $submit = new \VMaker\VInputSubmit("submit", "Enviar");
    $submit->setClass("btn btn-success");
    $submit->setStyle("width: 100%");
    $form->addInputField($submit);
  }
  
  $panel->addBody($form->make());
  
  $html->addObject($panel);
  
  $html->addContent("<a href=\"/pesquisa/\" class=\"btn btn-info\"><i class=\"fas fa-backward\"></i> Voltar</a>");
  
  echo $html->make();