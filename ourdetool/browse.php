<?php
  require './config.inc.php';
  require './header.inc.php';

  include "det_userdata.inc.php";

  $id	  = $_GET['id'];
  $userid = $_GET['userid'];

  read_passwd_file($id);

  if (isset($userid)) {
    if (!is_user($htpUser[$userid][username]))
      ht_error("Benutzer nicht vorhanden (UserID: $userid)", "Seite anzeigen (Löschen)");
    echo "<font class=\"tdmain\">Benutzer \"".$htpUser[$userid][username]."\" wurde gelöscht</font><p>\n";
    $htpUser[$userid][username] = '';
    $htpUser[$userid][password] = '';
    $htpUser[$userid][realname] = '';
    $htpUser[$userid][email]    = '';
    write_passwd_file($id);
    read_passwd_file($id);
  }
?>
<script language="JavaScript">
function delUser(userid) {
  cf = confirm("Möchten Sie diesen Benutzer wirklich löschen?");
  if (cf) {
    document.location = "browse.php?id=<?php echo $id ?>&userid="+userid+"&sid=<?php echo random() ?>";
  }
}
</script>
<table border="0" cellspacing="3" cellpadding="2" width="100%">
  <tr>
    <td colspan="8" width="100%" align="left" class="tdheader"><?php echo $cfgProgName.' '.$cfgVersion ?></td>
  </tr>
  <tr>
    <td colspan="8" width="100%" align="left" class="tdheader">[ <?php echo $cfgHTPasswd[$id][D] ?> ]</td>
  </tr>
<?php
  $htpCount = 0;
  for ($userid = 0; $userid < count($htpUser); $userid++) {
    //$bgcolor = "#EEEEEE";
    //if ($userid % 2) $bgcolor = "#FFFFFF";
    if (!empty($htpUser[$userid][username])) {
      $htpCount++;
      echo "  <tr>\n".
	   "    <td bgcolor=\"$bgcolor\" align=\"left\" class=\"tdmain\"><b>".($userid+1).")</b> ".$htpUser[$userid][username]."</td>\n".
	   "    <td bgcolor=\"$bgcolor\" align=\"left\" class=\"tdmain\">".$htpUser[$userid][realname]."</td>\n".
	   "    <td bgcolor=\"$bgcolor\" align=\"left\" class=\"tdmain\">".$htpUser[$userid][email]."</td>\n".
	   "    <td bgcolor=\"$bgcolor\" align=\"center\" class=\"tdmain\"><a href=\"edit.php?id=$id&userid=$userid&sid=".random()."\">[ $edittext ]</a></td>\n".
	   "    <td bgcolor=\"$bgcolor\" align=\"center\" class=\"tdmain\"><a href=\"javascript:delUser('$userid')\">[ $deletetext ]</a></td>\n".
	   "    <td bgcolor=\"$bgcolor\" align=\"center\" class=\"tdmain\"><a href=\"showlog.php?id=".$htpUser[$userid][username]."\">[ Logfile ]</a></td>\n".
	   "    <td bgcolor=\"$bgcolor\" align=\"center\" class=\"tdmain\"><a href=\"showactlog.php?id=".$htpUser[$userid][username]."\">[ Aktivität ]</a></td>\n".
	   "    <td bgcolor=\"$bgcolor\" align=\"center\" class=\"tdmain\"><a href=\"deletelog.php?id=".$htpUser[$userid][username]."\" onclick=\"return confirm('Warnung: Die Datei wird unwiderruflich gelöscht!')\">[ Log löschen ]</a></td>\n".
	   "  </tr>\n";
    }
  }  
  if ($htpCount < 1) {
    echo "  <tr>\n".
	 "    <td colspan=\"5\" bgcolor=\"$bgcolor\" width=\"100%\" align=\"left\" class=\"tdmain\">Diese .htpasswd Datei ist leer</td>\n".
	 "  </tr>\n";
  }
?>
</table>
<table border="0" cellspacing="3" cellpadding="2" width="600">
  <tr>
    <td width="100%" align="left" class="tdmain">[
    <a href="index.php?<?php echo random() ?>"><?php echo $mainpagetext ?></a> |
    <?php echo $showuserlisttext ?> |
    <a href="add.php?id=<?php echo $id ?>&sid=<?php echo random() ?>"><?php echo $newusertext ?></a> |
    <a href="view-htpasswd.php?id=<?php echo $id ?>&sid=<?php echo random() ?>"><?php echo $viewhtpasswdtext ?></a> |
    <a href="create-htaccess.php?id=<?php echo $id ?>&sid=<?php echo random() ?>"><?php echo $createhtaccesstext ?></a> ]</td>
  </tr>
</table>
<br><b>Niemals den Admin-User löschen!!!</b>
<?php

//////////////////////////////////////////////////////////////  
//////////////////////////////////////////////////////////////
// statistik der useraktivität
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////

//alle supporter definieren, die ausgewertet werden sollen

$supporter_list=array ("issomad", "peng", "cannopio", "raufbold", "downfall");

//die daten der supporter auslesen
if(!$db)include "../inccon.php";

include("../lib/pData.class");   
include("../lib/pChart.class");

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//die daten der letzten 4 monate auslesen
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
echo '<br><b>4 Monate</b><br>';
$datum=date("Y-m-d",time()-(3600*24*120));
unset($data);
for($i=0;$i<count($supporter_list);$i++)
{
  $username=$supporter_list[$i];

  //echo "SELECT SUM(h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23) AS wert FROM de_supporttool.de_user_stat WHERE username='$username' AND datum>'$datum'<br>";
  
  $db_daten=mysql_query("SELECT SUM(h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23) AS wert FROM de_supporttool.de_user_stat WHERE username='$username' AND datum>'$datum'",$db);
  $row = mysql_fetch_array($db_daten);
  
  echo '<br>'.$username.': '.$row["wert"];
  if($row["wert"]=='')$row["wert"]=0;
  $data[]=$row["wert"];
}

// Dataset definition   
unset($DataSet );
unset($Test);
$DataSet = new pData;   
$DataSet->AddPoint($data,"Serie1");
$DataSet->AddPoint($supporter_list,"Serie2");   
$DataSet->AddAllSeries();   
$DataSet->SetAbsciseLabelSerie("Serie2");   
  
// Initialise the graph   
$Test = new pChart(420,200);   
$Test->drawFilledRoundedRectangle(7,7,413,193,5,240,240,240);   
//$Test->drawRoundedRectangle(5,5,375,195,5,230,230,230);   
  
// Draw the pie chart   
$Test->setFontProperties("../fonts/font0.ttf",8);   
$Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),150,90,110,PIE_PERCENTAGE,TRUE,50,20,5);   
$Test->drawPieLegend(310,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);   
  
$Test->Render("supstat1.png");     
  
  echo '<div style="background-color: #F0F0F0; width:430;"><img src="supstat1.png"></div>';
  
  echo '</div>'; 


//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//die daten der letzten 3 monate auslesen
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
echo '<br><b>3 Monate</b><br>';
$datum=date("Y-m-d",time()-(3600*24*90));
unset($data);
for($i=0;$i<count($supporter_list);$i++)
{
  $username=$supporter_list[$i];

  //echo "SELECT SUM(h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23) AS wert FROM de_supporttool.de_user_stat WHERE username='$username' AND datum>'$datum'<br>";
  
  $db_daten=mysql_query("SELECT SUM(h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23) AS wert FROM de_supporttool.de_user_stat WHERE username='$username' AND datum>'$datum'",$db); 
  $row = mysql_fetch_array($db_daten);
  
  echo '<br>'.$username.': '.$row["wert"];
  if($row["wert"]=='')$row["wert"]=0;
  $data[]=$row["wert"];
}

// Dataset definition    
unset($DataSet );
unset($Test);
$DataSet = new pData;   
$DataSet->AddPoint($data,"Serie1");
$DataSet->AddPoint($supporter_list,"Serie2");   
$DataSet->AddAllSeries();   
$DataSet->SetAbsciseLabelSerie("Serie2");   
  
// Initialise the graph   
$Test = new pChart(420,200);   
$Test->drawFilledRoundedRectangle(7,7,413,193,5,240,240,240);   
//$Test->drawRoundedRectangle(5,5,375,195,5,230,230,230);   
  
// Draw the pie chart   
$Test->setFontProperties("../fonts/font0.ttf",8);   
$Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),150,90,110,PIE_PERCENTAGE,TRUE,50,20,5);   
$Test->drawPieLegend(310,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);   
  
$Test->Render("supstat2.png");     
  
  echo '<div style="background-color: #F0F0F0; width:430;"><img src="supstat2.png"></div>';
  
  echo '</div>'; 
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//die daten der letzten 2 monate auslesen
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
echo '<br><b>2 Monate</b><br>';
$datum=date("Y-m-d",time()-(3600*24*60));
unset($data);
for($i=0;$i<count($supporter_list);$i++)
{
  $username=$supporter_list[$i];

  //echo "SELECT SUM(h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23) AS wert FROM de_supporttool.de_user_stat WHERE username='$username' AND datum>'$datum'<br>";
  
  $db_daten=mysql_query("SELECT SUM(h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23) AS wert FROM de_supporttool.de_user_stat WHERE username='$username' AND datum>'$datum'",$db); 
  $row = mysql_fetch_array($db_daten);
  
  echo '<br>'.$username.': '.$row["wert"];
  if($row["wert"]=='')$row["wert"]=0;
  $data[]=$row["wert"];
}

// Dataset definition
unset($DataSet );
unset($Test);
$DataSet = new pData;   
$DataSet->AddPoint($data,"Serie1");
$DataSet->AddPoint($supporter_list,"Serie2");   
$DataSet->AddAllSeries();   
$DataSet->SetAbsciseLabelSerie("Serie2");   
  
// Initialise the graph   
$Test = new pChart(420,200);   
$Test->drawFilledRoundedRectangle(7,7,413,193,5,240,240,240);   
//$Test->drawRoundedRectangle(5,5,375,195,5,230,230,230);   
  
// Draw the pie chart   
$Test->setFontProperties("../fonts/font0.ttf",8);   
$Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),150,90,110,PIE_PERCENTAGE,TRUE,50,20,5);   
$Test->drawPieLegend(310,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);   
  
$Test->Render("supstat3.png");     
  
  echo '<div style="background-color: #F0F0F0; width:430;"><img src="supstat3.png"></div>';
  
  echo '</div>'; 
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//die daten der letzten 1 monate auslesen
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
echo '<br><b>1 Monat</b><br>';
$datum=date("Y-m-d",time()-(3600*24*30));
unset($data);
for($i=0;$i<count($supporter_list);$i++)
{
  $username=$supporter_list[$i];

  //echo "SELECT SUM(h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23) AS wert FROM de_supporttool.de_user_stat WHERE username='$username' AND datum>'$datum'<br>";
  
  $db_daten=mysql_query("SELECT SUM(h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23) AS wert FROM de_supporttool.de_user_stat WHERE username='$username' AND datum>'$datum'",$db); 
  $row = mysql_fetch_array($db_daten);
  
  echo '<br>'.$username.': '.$row["wert"];
  if($row["wert"]=='')$row["wert"]=0;
  $data[]=$row["wert"];
}

// Dataset definition
unset($DataSet );
unset($Test);
$DataSet = new pData;   
$DataSet->AddPoint($data,"Serie1");
$DataSet->AddPoint($supporter_list,"Serie2");   
$DataSet->AddAllSeries();   
$DataSet->SetAbsciseLabelSerie("Serie2");   
  
// Initialise the graph   
$Test = new pChart(420,200);   
$Test->drawFilledRoundedRectangle(7,7,413,193,5,240,240,240);   
//$Test->drawRoundedRectangle(5,5,375,195,5,230,230,230);   
  
// Draw the pie chart   
$Test->setFontProperties("../fonts/font0.ttf",8);   
$Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),150,90,110,PIE_PERCENTAGE,TRUE,50,20,5);   
$Test->drawPieLegend(310,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);   
  
$Test->Render("supstat4.png");     
  
  echo '<div style="background-color: #F0F0F0; width:430;"><img src="supstat4.png"></div>';
  
  echo '</div>'; 
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//die daten der letzten 14 tage auslesen
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
echo '<br><b>14 Tage</b><br>';
$datum=date("Y-m-d",time()-(3600*24*14));
unset($data);
for($i=0;$i<count($supporter_list);$i++)
{
  $username=$supporter_list[$i];

  //echo "SELECT SUM(h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23) AS wert FROM de_supporttool.de_user_stat WHERE username='$username' AND datum>'$datum'<br>";
  
  $db_daten=mysql_query("SELECT SUM(h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23) AS wert FROM de_supporttool.de_user_stat WHERE username='$username' AND datum>'$datum'",$db); 
  $row = mysql_fetch_array($db_daten);
  
  echo '<br>'.$username.': '.$row["wert"];
  if($row["wert"]=='')$row["wert"]=0;
  $data[]=$row["wert"];
}

// Dataset definition
unset($DataSet );
unset($Test);
$DataSet = new pData;   
$DataSet->AddPoint($data,"Serie1");
$DataSet->AddPoint($supporter_list,"Serie2");   
$DataSet->AddAllSeries();   
$DataSet->SetAbsciseLabelSerie("Serie2");   
  
// Initialise the graph   
$Test = new pChart(420,200);   
$Test->drawFilledRoundedRectangle(7,7,413,193,5,240,240,240);   
//$Test->drawRoundedRectangle(5,5,375,195,5,230,230,230);   
  
// Draw the pie chart   
$Test->setFontProperties("../fonts/font0.ttf",8);   
$Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),150,90,110,PIE_PERCENTAGE,TRUE,50,20,5);   
$Test->drawPieLegend(310,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);   
  
$Test->Render("supstat5.png");     
  
  echo '<div style="background-color: #F0F0F0; width:430;"><img src="supstat5.png"></div>';
  
  echo '</div>'; 


  require './footer.inc.php';
?>
