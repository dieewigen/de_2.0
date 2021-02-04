<?php
if(isset($_REQUEST["eftapic"]))
{
  //$picid=intval($_REQUEST["eftapic"]);
  $im = imagecreatefrompng("eftadata/cron/eftamap2000x2000.png");	
  header("Content-type: image/png");
  imagePng($im);
  die();
}

?>