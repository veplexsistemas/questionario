<?php
  use VMaker\VHtml;
  use VMaker\VPanel;
  use VMaker\VForm;
  use VMaker\VDataGrid;
  
  //Html
  $html = new VHtml("layouts.app");
  $html->openSection("content");
  
  //Status
  if ($status = session("status"))
  {
    $panelStatus = new VPanel();
    $panelStatus->setClass("panel panel-success");
    $panelStatus->addHeading($status);
    $html->addContent($panelStatus->make());
  }
  
  if (sizeof($data))
  {
    //Grid
    $grid = new VDataGrid();
    $grid->setShowPagination(false);
    $grid->setData($data);
    $grid->setFields(["nm_pesquisa" => "Selecione uma pesquisa"]);

    $grid->addExtraField("", "<i class=\"fas fa-check\"></i> Responder", "/pesquisa/responder", ["cd_pesquisa"], "btn btn-primary btn-sm");

    $html->addObject($grid);
  }
  else
  {
    $panel = new VPanel();
    $panel->setClass("panel panel-info");
    $panel->addHeading("<i class=\"fas fa-info-circle\"></i> Informação");
    $panel->addBody("Você não possui pesquisas ou já respondeu a pesquisa neste mês.");
    $html->addObject($panel);
  }
/*
  if (sizeof($avaliacao))
  {
    $panel = new VPanel();
    $panel->setClass("panel panel-primary");
    $panel->addHeading("Avaliações mês ".$nota[0]->dt_mes."<span style='float: right'>Média ".$nota[0]->vl_media."</span>");

    $form = new VForm();
    $form->setAction("PesquisaController@registraAnalise");
    $form->setErrors($errors);

    $t_qt_perguntas = 0;

    foreach ($avaliacao AS $obj)
    {
      $t_qt_perguntas++;
      
      $cd_resposta = new \VMaker\VInputHidden("cd_resposta_{$t_qt_perguntas}", $obj->cd_resposta);
      $form->addInputField($cd_resposta);

      $id = "ds_analise_{$t_qt_perguntas}";
      $ds_analise = new \VMaker\VInputText($id);
      
      if ($obj->id_identificado)
        $ds_analise->setLabel($obj->cd_usuario_resposta." / ".$obj->nm_pessoa . "  -  ". $obj->dt_resposta);
      
      $ds_analise->setExtraLabel($obj->ds_justificativa);
      $form->addInputField($ds_analise);
    }

    $qt_perguntas = new \VMaker\VInputHidden("qt_perguntas", $t_qt_perguntas);
    $form->addInputField($qt_perguntas);

    $submit = new \VMaker\VInputSubmit("submit", "Enviar");
    $submit->setClass("btn btn-success");
    $submit->setStyle("width: 100%");
    $form->addInputField($submit);

    $panel->addBody($form->make());
    $html->addObject($panel);
  }

  if (sizeof($comunicacao))
  {
    $panel = new VPanel();
    $panel->setClass("panel panel-primary");
    $panel->addHeading("Respostas do último mês");


    foreach ($comunicacao AS $obj)
    {
      $panel->addBody("<b>".$obj->ds_pergunta."</br>R: </b>".$obj->ds_justificativa.
                      "</br><b><i>".$obj->cd_usuario_analise." / ".$obj->nm_pessoa.": </b> ".$obj->ds_analise."</i>");
    }
    $html->addObject($panel);
  }
*/
  echo $html->make();