<?php
  use VMaker\VHtml;
  use VMaker\VPanel;
  
  //Html
  $html = new VHtml("layouts.app");
  $html->openSection("content");
  
  //Panel
  $panel = new VPanel();
  $panel->setClass("panel panel-warning");
  $panel->addHeading("<i class=\"fas fa-exclamation-triangle\"></i> Ops, algo deu errado...");
  
  $panel->addBody("A página que você está procurando não foi encontrada!<br>Verifique o endereço informado e tente novamente.");
  
  $html->addObject($panel);
  
  echo $html->make();