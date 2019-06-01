<?php
  use VMaker\VHtml;
  use VMaker\VPanel;
  
  //Html
  $html = new VHtml("layouts.app");
  $html->openSection("content");
  
  //Panel
  $panel = new VPanel();
  $panel->setClass("panel panel-danger");
  
  $panel->addHeading("<i class=\"fas fa-exclamation-circle\"></i> Erro");
  $panel->addBody($msgErro);
  
  $html->addObject($panel);
  
  echo $html->make();