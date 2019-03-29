<?php
  use VMaker\VHtml;
  use VMaker\VPanel;
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
    $panel->addBody("Você não possui pesquisas para responder no momento.");
    $html->addObject($panel);
  }
  
  echo $html->make();