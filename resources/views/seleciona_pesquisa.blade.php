<?php
  use VMaker\VHtml;
  use VMaker\VPanel;
  use VMaker\VDataGrid;
  
  //Html
  $html = new VHtml("layouts.app");
  $html->openSection("content");
  $html->addContent("<h3>Selecione uma Pesquisa</h3>");
  
  //Status
  if ($status = session("status"))
  {
    $panelStatus = new VPanel();
    $panelStatus->setClass("panel panel-success");
    $panelStatus->addHeading($status);
    $html->addContent($panelStatus->make());
  }
  
  //Grid
  $grid = new VDataGrid();
  $grid->setShowPagination(false);
  $grid->setData($data);
  $grid->setFields(["nm_pesquisa" => "Pesquisa"]);
  
  $grid->addExtraField("", "<i class=\"fas fa-check\"></i> Responder", "/pesquisa/responder", ["cd_pesquisa"], "btn btn-primary btn-sm");
  
  $html->addObject($grid);
  
  echo $html->make();