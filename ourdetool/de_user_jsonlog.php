<?php
include "det_userdata.inc.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>

    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>DE LogViewer</title>
    <link rel="stylesheet" type="text/css" href="f.css">
    <link rel="stylesheet" type="text/css" href="jq/flora.datepicker.css">

    <script type="text/javascript" src="jq/jquery.js"></script>
    <script type="text/javascript" src="jq/jquery.flydom.js"></script>
    <script type="text/javascript" src="jq/ui.datepicker.js"></script>

    <script type="text/javascript" src="logview.js"></script>
    <style>
    span[onclick] {cursor:pointer;}
    div[onclick] {cursor:pointer;}
    .Zeit 	{ width:150px;  vertical-align:top; font-size:9pt; border:0px; }
    .IP		{ width:100px;  vertical-align:top; font-size:9pt; border:0px; }
    .Datei 	{ width:120px;  vertical-align:top; font-size:9pt; border:0px; }
    .Get 	{ width:180px;  vertical-align:top; font-size:9pt; border:0px; }
    .Post 	{ width:200px;  vertical-align:top; font-size:9pt; border:0px; }
    </style>
  </head>
  <body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0">

  <div style=" position:fixed; background:url(bg_blue.gif); z-index:100;">
  <table>
  <tr>
  	<td><img src="busy.gif" width="15" height="15" border="0" alt="" style="display:none;" id="working"></td>
  	<td><input type="button" value="Config Ein/Ausblenden" onclick="$('#sidebar').slideToggle('fast')"></td>
  	<td><input type="button" value="laden" onclick="LOG.getLog()"></td>
  	<td>User ID <?=$_REQUEST["uid"]?><input type="hidden" id="uid" value="<?=$_REQUEST["uid"]?>"></td>
  	<td>Ab <input type="Text" id="timefrom" value="2008-01-27 12:59:58"></td>
  	<td><input type="Text" id="lines" value="30" size="4">Zeilen</td>

  </tr>
  </table>
  </div>
  <div id="info" style=" position:absolute; top:50px; right:0px; width:100% z-index:101; border:1px solid;"></div>
  <hr>
  <div id="sidebar" style=" position:fixed; top:60px; left:0px;width:200px; bottom:10px; overflow:scroll; display:none; background:url(bg_blue.gif); z-index:100; border:5px solid;">
    <table>
    	<tr><td id="hide">
         	Einblenden: <span onclick="LOG.ShowHide(true);">alle</span> | <span onclick="LOG.ShowHide(false);">keine</span><hr>
                 Ausblenden: <span onclick="LOG.ShowHide(false,['eftastart','eftamain','eftaindex','efta_menu','efta_chat','chat','sou_chat']);">efta & chat</span><hr>
                 Ein/Ausblenden:<br>
                 <span onclick="$('.Zeit') .toggle();LOG.Redraw();">Zeit</span> |
                 <span onclick="$('.IP')   .toggle();LOG.Redraw();">IP</span>   |
                 <span onclick="$('.Datei').toggle();LOG.Redraw();">Datei</span> |
                 <span onclick="$('.Get')  .toggle();LOG.Redraw();">Get</span> |
                 <span onclick="$('.Post') .toggle();LOG.Redraw();">Post</span><hr>

         </td></tr>
    </table>
  </div>
  <div id="data" style=" display:inline; border:1px solid; position:relative; top:50px; left:0px;">

  </div>



  <div id="working_off" style="position:fixed; top:0px; left:0px; width:100%; height:100%; background:black; display:none;">
  	<center><h1>Ich Arbeite .....</h1></center></div>
   </body>
</html>