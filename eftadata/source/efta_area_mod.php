<?php
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//gebäude definieren
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//getreidefarm
$index=0;
$bldgdef[$index][0]='Getreidefarm';//name
$bldgdef[$index][1][0]='4000';//ben. aktionspunkte bei der errichtung
$bldgdef[$index][1][1]='1000';//ben. aktionspunkte beim upgrade pro level
$bldgdef[$index][2][0]='4846;1';//getreide
$bldgdef[$index][2][1]='4845;8';//lindenholzstamm
$bldgdef[$index][2][2]='4854;3';//kupfernägel
$bldgdef[$index][3]='010';//berg wiese wald -voraussetzung
$bldgdef[$index][4][0]='5';//gebäudeid für die datenbank
$bldgdef[$index][4][1]='5';//gebäudebildid für die datenbank
$bldgdef[$index][5][0][0]='250';//aktionspunkte für das ernten/abbauen
$bldgdef[$index][5][0][1]='4852';//benötigtes werkzeug
$bldgdef[$index][5][0][2]='Getreide ernten';//abbau-linkttext
$bldgdef[$index][5][0][3]='4851';//das item das man erhält
$bldgdef[$index][5][0][4][0]='';//das item das man benötigt und das verbraucht wird



//erzmine
$index++;
$bldgdef[$index][0]='Erzmine';//name
$bldgdef[$index][1][0]='4000';//ben. aktionspunkte bei der errichtung
$bldgdef[$index][1][1]='1000';//ben. aktionspunkte beim upgrade pro level
$bldgdef[$index][2][0]='4845;10';//lindenholzstamm
$bldgdef[$index][2][1]='4854;4';//kupfernägel
$bldgdef[$index][3]='110';//berg wiese wald -voraussetzung
$bldgdef[$index][4][0]='6';//gebäudeid für die datenbank
$bldgdef[$index][4][1]='6';//gebäudebildid für die datenbank
$bldgdef[$index][5][0][0]='250';//aktionspunkte für das ernten/abbauen
$bldgdef[$index][5][0][1]='4847';//benötigtes werkzeug
$bldgdef[$index][5][0][2]='Erz abbauen';//abbau-linkttext
$bldgdef[$index][5][0][3]=4849;//das item das man erhält
$bldgdef[$index][5][0][4][0]='';//das item das man benötigt und das verbraucht wird



//steinbruch
$index++;
$bldgdef[$index][0]='Steinbruch';//name
$bldgdef[$index][1][0]='4000';//ben. aktionspunkte bei der errichtung
$bldgdef[$index][1][1]='1000';//ben. aktionspunkte beim upgrade pro level
$bldgdef[$index][2][0]='4845;8';//lindenholzstamm
$bldgdef[$index][2][1]='4854;3';//kupfernägel
$bldgdef[$index][3]='110';//berg wiese wald -voraussetzung
$bldgdef[$index][4][0]='7';//gebäudeid für die datenbank
$bldgdef[$index][4][1]='7';//gebäudebildid für die datenbank
$bldgdef[$index][5][0][0]='250';//aktionspunkte für das ernten/abbauen
$bldgdef[$index][5][0][1]='4848';//benötigtes werkzeug
$bldgdef[$index][5][0][2]='Stein schlagen';//abbau-linkttext
$bldgdef[$index][5][0][3]=4850;//das item das man erhält
$bldgdef[$index][5][0][4][0]='';//das item das man benötigt und das verbraucht wird



//holzfällerhütte
$index++;
$bldgdef[$index][0]='Holzf&auml;llerh&uuml;tte';//name
$bldgdef[$index][1][0]='4000';//ben. aktionspunkte bei der errichtung
$bldgdef[$index][1][1]='1000';//ben. aktionspunkte beim upgrade pro level
$bldgdef[$index][2][0]='4854;2';//kupfernägel
$bldgdef[$index][3]='001';//berg wiese wald -voraussetzung
$bldgdef[$index][4][0]='9';//gebäudeid für die datenbank
$bldgdef[$index][4][1]='26';//gebäudebildid für die datenbank
$bldgdef[$index][5][0][0]='250';//aktionspunkte für das ernten/abbauen
$bldgdef[$index][5][0][1]='4844';//benötigtes werkzeug
$bldgdef[$index][5][0][2]='Baum f&auml;llen';//abbau-linkttext
$bldgdef[$index][5][0][3]=4845;//das item das man erhält
$bldgdef[$index][5][0][4][0]='';//das item das man benötigt und das verbraucht wird



//erzschmelze
$index++;
$bldgdef[$index][0]='Erzschmelze';//name
$bldgdef[$index][1][0]='4000';//ben. aktionspunkte bei der errichtung
$bldgdef[$index][1][1]='1000';//ben. aktionspunkte beim upgrade pro level
$bldgdef[$index][2][0]='4845;8';//lindenholzstamm
$bldgdef[$index][2][1]='4850;12';//sandstein
$bldgdef[$index][2][2]='4854;4';//kupfernägel
$bldgdef[$index][3]='010';//berg wiese wald -voraussetzung
$bldgdef[$index][4][0]='10';//gebäudeid für die datenbank
$bldgdef[$index][4][1]='27';//gebäudebildid für die datenbank
//kupfererz
$bldgdef[$index][5][0][0]='250';//aktionspunkte für das ernten/abbauen
$bldgdef[$index][5][0][1]='';//benötigtes werkzeug
$bldgdef[$index][5][0][2]='Kupfererz verh&uuml;tten';//abbau-linkttext
$bldgdef[$index][5][0][3]='4855';//kupferbarren, das item das man erhält
$bldgdef[$index][5][0][4][0]='4849;1';//kupfererz, das item das man benötigt und das verbraucht wird
$bldgdef[$index][5][0][4][1]='4845;1';//lindenholzstamm, das item das man benötigt und das verbraucht wird
//zinnerz
/*
$bldgdef[$index][5][1][0]='25';//aktionspunkte für das ernten/abbauen
$bldgdef[$index][5][1][1]='';//benötigtes werkzeug
$bldgdef[$index][5][1][2]='Zinnerz verh&uuml;tten';//abbau-linkttext
$bldgdef[$index][5][1][3]='4857';//das item das man erhält
$bldgdef[$index][5][1][4][0]='4856;1';//zinnerz, das item das man benötigt und das verbraucht wird
$bldgdef[$index][5][1][4][1]='4845;1';//lindenholzstamm, das item das man benötigt und das verbraucht wird
*/



//windmühle
$index++;
$bldgdef[$index][0]='Windm&uuml;hle';//name
$bldgdef[$index][1][0]='4000';//ben. aktionspunkte bei der errichtung
$bldgdef[$index][1][1]='1000';//ben. aktionspunkte beim upgrade pro level
$bldgdef[$index][2][0]='4845;10';//lindenholzstamm
$bldgdef[$index][2][1]='4850;8';//sandstein
$bldgdef[$index][2][2]='4854;6';//kupfernägel
$bldgdef[$index][3]='010';//berg wiese wald -voraussetzung
$bldgdef[$index][4][0]='11';//gebäudeid für die datenbank
$bldgdef[$index][4][1]='28';//gebäudebildid für die datenbank
$bldgdef[$index][5][0][0]='250';//aktionspunkte für das ernten/abbauen
$bldgdef[$index][5][0][1]='';//benötigtes werkzeug
$bldgdef[$index][5][0][2]='Getreide mahlen';//abbau-linkttext
$bldgdef[$index][5][0][3]='4858';//mehl, das item das man erhält
$bldgdef[$index][5][0][4][0]='4851;1';//getreide, das item das man benötigt und das verbraucht wird



//wasserbrunnen
$index++;
$bldgdef[$index][0]='Brunnen';//name
$bldgdef[$index][1][0]='4000';//ben. aktionspunkte bei der errichtung
$bldgdef[$index][1][1]='1000';//ben. aktionspunkte beim upgrade pro level
$bldgdef[$index][2][0]='4845;2';//lindenholzstamm
$bldgdef[$index][2][1]='4850;10';//sandstein
$bldgdef[$index][2][2]='4854;1';//kupfernägel
$bldgdef[$index][3]='010';//berg wiese wald -voraussetzung
$bldgdef[$index][4][0]='12';//gebäudeid für die datenbank
$bldgdef[$index][4][1]='29';//gebäudebildid für die datenbank
$bldgdef[$index][5][0][0]='250';//aktionspunkte für das ernten/abbauen
$bldgdef[$index][5][0][1]='';//benötigtes werkzeug
$bldgdef[$index][5][0][2]='Wasser sch&ouml;pfen';//abbau-linkttext
$bldgdef[$index][5][0][3]='4860';//voller wassereimer, das item das man erhält
$bldgdef[$index][5][0][4][0]='4859;1';//leerer wassereimer, das item das man benötigt und das verbraucht wird



//bäckerei
$index++;
$bldgdef[$index][0]='B&auml;ckerei';//name
$bldgdef[$index][1][0]='4000';//ben. aktionspunkte bei der errichtung
$bldgdef[$index][1][1]='1000';//ben. aktionspunkte beim upgrade pro level
$bldgdef[$index][2][0]='4845;5';//lindenholzstamm
$bldgdef[$index][2][1]='4850;10';//sandstein
$bldgdef[$index][2][2]='4854;4';//kupfernägel
$bldgdef[$index][3]='010';//berg wiese wald -voraussetzung
$bldgdef[$index][4][0]='13';//gebäudeid für die datenbank
$bldgdef[$index][4][1]='30';//gebäudebildid für die datenbank
$bldgdef[$index][5][0][0]='250';//aktionspunkte für das ernten/abbauen
$bldgdef[$index][5][0][1]='';//benötigtes werkzeug
$bldgdef[$index][5][0][2]='Brot backen';//abbau-linkttext
$bldgdef[$index][5][0][3]='4861';//brot, das item das man erhält
$bldgdef[$index][5][0][4][0]='4860;1';//voller wassereimer, das item das man benötigt und das verbraucht wird
$bldgdef[$index][5][0][4][1]='4858;1';//mehl, das item das man benötigt und das verbraucht wird
$bldgdef[$index][5][0][4][2]='4845;1';//lindenholzstamm, das item das man benötigt und das verbraucht wird



//alchemistenküche
$index++;
$bldgdef[$index][0]='Alchemistenk&uuml;che';//name
$bldgdef[$index][1][0]='4000';//ben. aktionspunkte bei der errichtung
$bldgdef[$index][1][1]='1000';//ben. aktionspunkte beim upgrade pro level
$bldgdef[$index][2][0]='4845;5';//lindenholzstamm
$bldgdef[$index][2][1]='4850;5';//sandstein
$bldgdef[$index][2][2]='4854;2';//kupfernägel
$bldgdef[$index][2][3]='4855;5';//kupferbarren
$bldgdef[$index][3]='010';//berg wiese wald -voraussetzung
$bldgdef[$index][4][0]='14';//gebäudeid für die datenbank
$bldgdef[$index][4][1]='31';//gebäudebildid für die datenbank
$bldgdef[$index][5][0][0]='250';//aktionspunkte für das ernten/abbauen
$bldgdef[$index][5][0][1]='';//benötigtes werkzeug
$bldgdef[$index][5][0][2]='Oller Fusel brauen';//abbau-linkttext
$bldgdef[$index][5][0][3]='4863';//schnaps, das item das man erhält
$bldgdef[$index][5][0][4][0]='4860;1';//voller wassereimer, das item das man benötigt und das verbraucht wird
$bldgdef[$index][5][0][4][1]='4851;2';//getreide, das item das man benötigt und das verbraucht wird
$bldgdef[$index][5][0][4][2]='4845;1';//lindenholzstamm, das item das man benötigt und das verbraucht wird
$bldgdef[$index][5][0][4][3]='4862;1';//leere glasflasche, das item das man benötigt und das verbraucht wird

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//es soll etwas abgebaut/geerntet/verarbeitet werden
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if(isset($_REQUEST["gather"]))
{
  $choose=intval($_REQUEST["choose"]);
  //feldaten auslesen
  $db_daten=mysql_query("SELECT * FROM de_cyborg_map WHERE x='$x' AND y='$y' AND z='$map'",$eftadb);
  $row = mysql_fetch_array($db_daten);
  $bldg=$row["bldg"];
  $fieldamount=$row["fieldamount"];
  
  //passendes gebäude raussuchen
  $gebid='-1';
  for($i=0;$i<count($bldgdef);$i++)
  {
    if($bldgdef[$i][4][0]==$bldg)
    {
      $gebid=$i;
  	  break;
  	}
  }
  //dort steht ein gebäude aus der liste
  if($gebid>=0)
  {
  	//schauen ob es etwas gibt, das man ernten kann
  	if($fieldamount>0)
  	{
  	  //schauen ob es die gewählte aktion überhaupt gibt
  	  if(isset($bldgdef[$gebid][5][$choose][0]))
  	  {
  	    //schauen, ob man genug aktionspunkte hat
  	    $benaktionspunkte=$bldgdef[$gebid][5][$choose][0];
  	    if($bewpunkte>=$benaktionspunkte)
  	    {
  		  //schauen ob man das passende werkzeug hat
  		  $benwerkzeug=$bldgdef[$gebid][5][$choose][1];
  		  if($benwerkzeug!='')
  		  {
  		    $result = mysql_query("SELECT id FROM de_cyborg_item WHERE id='$benwerkzeug' AND equip=0 AND user_id='$efta_user_id'", $eftadb);
            $num = mysql_num_rows($result);
  		  }
  		  else $num=1;
          if($num>0)
          {
            //schauen, ob man alle rohstoffe bei hat
            $hasall=1;
        	for($i=0;$i<count($bldgdef[$gebid][5][$choose][4]);$i++)
  			{
              //wenn das array belegt ist, dann ist es eine voraussetzung
          	  if($bldgdef[$gebid][5][$choose][4][$i]!='')
              {
                $need=explode(";",$bldgdef[$gebid][5][$choose][4][$i]);
            	$itemid=$need[0];
            	$itemmenge=$need[1];
            	//schauen ob die sachen im rucksack sind
            	$result = mysql_query("SELECT id FROM de_cyborg_item WHERE id='$itemid' AND user_id='$efta_user_id'", $eftadb);
      			$num = mysql_num_rows($result);
            	if($num<$itemmenge)$hasall=0;
              }
            }  
            if($hasall==1)
            {
          	  //aktionspunkte abziehen
              mysql_query("UPDATE de_cyborg_data SET bewpunkte=bewpunkte-'$benaktionspunkte' WHERE user_id='$efta_user_id'",$eftadb);
              //kapazität um eins verringern
              mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount-1 WHERE x='$x' AND y='$y' AND z='$map' AND fieldamount>0",$eftadb);
              //item in den rucksack packen
              $itemid=$bldgdef[$gebid][5][$choose][3];
              add_item($itemid, 1);
              //message ausgeben und im menü bleiben
              $filename='eftadata/items/'.$itemid.'.item';
              include($filename);
		      $errmsg.='<font color="#00FF00">Du verstaust folgendes in deinem Rucksack: '.$item_name.'</font>';
              
		      //items abziehen
        	  for($i=0;$i<count($bldgdef[$gebid][5][$choose][4]);$i++)
  			  {
                //wenn das array belegt ist, dann ist es eine voraussetzung
          	    if($bldgdef[$gebid][5][$choose][4][$i]!='')
                {
                  $need=explode(";",$bldgdef[$gebid][5][$choose][4][$i]);
            	  $itemid=$need[0];
            	  $itemmenge=$need[1];
            	  //aus der db löschen
            	  mysql_query("DELETE FROM de_cyborg_item WHERE id='$itemid' AND equip=0 AND user_id='$efta_user_id' LIMIT $itemmenge", $eftadb);
                }
              }  
		      
		      //die gebäudeansicht aktivieren
		      $bldgmenu=1;
            }
            else $errmsg.='<font color="#FF0000">Du hast nicht die benötigten Waren.</font>';
          }
          else $errmsg.='<font color="#FF0000">Du hast nicht das passende Werkzeug.</font>';
  	    }
  	    else $errmsg.='<font color="#FF0000">Du hast nicht genug Aktionspunkte.</font>';
  	  }
  	}
  	else $errmsg.='<font color="#FF0000">Die Kapazit&auml;t des Geb&auml;udes ist im Moment ersch&ouml;pft.</font>';
  }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//es soll etwas gebaut/erweitert werden
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if(isset($_REQUEST["buildid"]))
{
  $buildid=intval($_REQUEST["buildid"])-1;
  //zuerst auslesen wie das feld und die anliegenden felder sind
  //variablen nullen
  $berg=0;
  $wiese=0;
  $wald=0;
  $bldg=0;
  $fieldlevel=0;
  $fieldamount=0;
  //zuerst die felddaten auslesen
  $brangexa=$x-1;
  $brangexe=$x+1;
  $brangeya=$y-1;
  $brangeye=$y+1;

  //daten aus der db holen
  $db_daten=mysql_query("SELECT * FROM de_cyborg_map WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye' AND z='$map'",$eftadb);

  while($row = mysql_fetch_array($db_daten))
  {
    //schauen ob im nachbarfeld ein gebirge ist
    if(($row["x"]==$x+1 AND $row["y"]==$y) OR ($row["x"]==$x-1 AND $row["y"]==$y) OR 
      ($row["x"]==$x AND $row["y"]==$y+1) OR ($row["x"]==$x AND $row["y"]==$y-1))
  	  if($row["groundtyp"]==14)$berg=1;
    
  	//die daten auf dem feld, auf dem man steht auslesen
    if($row["x"]==$x AND $row["y"]==$y)
    {
      if($row["groundtyp"]==1)$wiese=1;
      elseif($row["groundtyp"]==15)$wald=1;
      if($row["bldg"]>0)
      {
      	$bldg=$row["bldg"];
      	$fieldlevel=$row["fieldlevel"];
      	$fieldamount=$row["fieldamount"];
      }
    }
  }
  
  //wenn bereits ein gebäude dort steht, schauen ob auch beim upgrade die richtige id übergeben worden ist
  if($bldg>0)
  {
  	//wenn die vorhandene id ungleich dem bauauftrag ist alles abbrechen
  	if($bldgdef[$buildid][4][0]!=$bldg)$buildid='-1';
  }
  
  //überprüfen, ob die id bekannt ist
  if($bldgdef[$buildid][0]!='')
  {
    //richtiger bodentyp?
    if($berg>=$bldgdef[$buildid][3][0] AND $wiese>=$bldgdef[$buildid][3][1] AND $wald>=$bldgdef[$buildid][3][2])
    {
      //überprüfen ob man genug aktionspunkte hat
      if($fieldlevel==0)$benaktionspunkte=$bldgdef[$buildid][1][0]; else $benaktionspunkte=($fieldlevel+1)*$bldgdef[$buildid][1][1];
      if($benaktionspunkte<=$bewpunkte)
      {
        //überprüfen, ob er alle benötigten waren dabei hat
        $hasall=1;
        for($i=0;$i<10;$i++)
  		{
          //wenn das array belegt ist, dann ist es eine voraussetzung
          if($bldgdef[$buildid][2][$i]!='')
          {
            $need=explode(";",$bldgdef[$buildid][2][$i]);
            $itemid=$need[0];
            $itemmenge=$need[1];
            //schauen ob die sachen im rucksack sind
            $result = mysql_query("SELECT id FROM de_cyborg_item WHERE id='$itemid' AND user_id='$efta_user_id'", $eftadb);
      		$num = mysql_num_rows($result);
            if($num<$itemmenge)$hasall=0;
          }
        }  
        if($hasall==1)
        {
          //AP abziehen, EXP verteilen
          mysql_query("UPDATE de_cyborg_data SET bewpunkte=bewpunkte-'$benaktionspunkte', exp=exp+50 WHERE user_id='$efta_user_id'",$eftadb);

          //gebäude anlegen
          $gebid=$bldgdef[$buildid][4][0];
          $gebpic=$bldgdef[$buildid][4][1];
          mysql_query("UPDATE de_cyborg_map SET bldg='$gebid', bldgpic='$gebpic', fieldlevel=fieldlevel+1 WHERE x='$x' AND y='$y' AND z='$map'",$eftadb);
		  
          //gebäude-struct-daten anlegen
          $result = mysql_query("SELECT * FROM de_cyborg_struct WHERE x='$x' AND y='$y' AND z='$map'", $eftadb);
      	  $num = mysql_num_rows($result);
      	  $time=time();
      	  if($num==1)//es gibt einen -> update
      	  {
      	    mysql_query("UPDATE de_cyborg_struct SET bldgtime='$time' WHERE x='$x' AND y='$y' AND z='$map'",$eftadb);
      	  }
      	  else //es gibt noch keinen -> insert
      	  {
      	  	mysql_query("INSERT INTO de_cyborg_struct (x,y,z, bldgid, bldgtime) VALUES('$x','$y','$map', '$gebid', '$time');", $eftadb);
     	  }
          
          
          //benötigte items abziehen
          for($i=0;$i<10;$i++)
  		  {
            //wenn das array belegt ist, dann ist es eine voraussetzung
            if($bldgdef[$buildid][2][$i]!='')
            {
              $need=explode(";",$bldgdef[$buildid][2][$i]);
              $itemid=$need[0];
              $itemmenge=$need[1];
              mysql_query("DELETE FROM de_cyborg_item WHERE id='$itemid' AND equip=0 AND user_id='$efta_user_id' LIMIT $itemmenge", $eftadb);
            }
          }  

          //nach dem bau direkt das gebäude von innen anzeigen
          $bldgmenu=1;
        }
        else $errmsg.='<font color="#FF0000">Du hast nicht alle ben&ouml;tigten Waren dabei.</font>';
      }
      else $errmsg.='<font color="#FF0000">Du hast nicht genug Aktionspunkte.</font>';
    }
  }
  //unterscheiden ob es ein upgrade, oder eine neuerrichtung ist
  if($bldg==0)//neues gebäude
  {

  }
  else //gebäudeupgrade
  {
  	
  }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//baumenü anzeigen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if (!isset($_REQUEST["createstr"]) AND isset($_REQUEST["bldgmenu"]))
{
  //variablen nullen
  $berg=0;
  $wiese=0;
  $wald=0;
  $bldg=0;
  $fieldlevel=0;
  $fieldamount=0;
  //zuerst die felddaten auslesen
  $brangexa=$x-1;
  $brangexe=$x+1;
  $brangeya=$y-1;
  $brangeye=$y+1;

  //daten aus der db holen
  $db_daten=mysql_query("SELECT * FROM de_cyborg_map WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye' AND z='$map'",$eftadb);

  while($row = mysql_fetch_array($db_daten))
  {
    //schauen ob im nachbarfeld ein gebirge ist
    if(($row["x"]==$x+1 AND $row["y"]==$y) OR ($row["x"]==$x-1 AND $row["y"]==$y) OR 
      ($row["x"]==$x AND $row["y"]==$y+1) OR ($row["x"]==$x AND $row["y"]==$y-1))
  	  if($row["groundtyp"]==14)$berg=1;
    
  	//die daten auf dem feld, auf dem man steht auslesen
    if($row["x"]==$x AND $row["y"]==$y)
    {
      if($row["groundtyp"]==1)$wiese=1;
      elseif($row["groundtyp"]==15)$wald=1;
      if($row["bldg"]>0)
      {
      	$bldg=$row["bldg"];
      	$fieldlevel=$row["fieldlevel"];
      	$fieldamount=$row["fieldamount"];
      }
    }
  }  
  //geländeart bestimmen
  if($wald==1)$areatyp='Wald';
  elseif($wiese==1 AND $berg==0)$areatyp='Wiese';
  elseif($wiese==1 AND $berg==1)$areatyp='Wiese mit Berg';
  

  /*echo '<div id="ct_city">';
  echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
  echo '<tr><td align="center"><b class="ueber">&nbsp;Baumen&uuml;&nbsp;</b></td></tr>';
  echo '</table><br>';*/

  echo '<br><br>';
  rahmen0_oben();
  rahmen1_oben('<div align="center"><b>Baumen&uuml;</b></div>');

  $bg='cell1';
  echo '<table width="100%" cellpadding="1" cellspacing="1">
		<tr align="left">
		<td width="30%" class="'.$bg.'"><b>&nbsp;Koordinaten: X: '.$x.' Y: '.$y.'</b></td>
		<td width="70%" class="'.$bg.'"><b>&nbsp;Gel&auml;ndeart: '.$areatyp.'</b></td>
		</tr>';
  echo '</table>';
  
  
  //wenn kein gebäude vorhanden ist, dann das baumenü anzeigen
  if($bldg==0)
  {

  	
  	$bg='cell';
    echo '<table width="100%" cellpadding="1" cellspacing="1">
	  	  <tr align="left">
		  <td class="'.$bg.'"><b>&nbsp;Folgende Geb&auml;ude k&ouml;nnen hier errichtet werden:</b></td>
		  </tr>';
    //schauen welche gebäude auf dem feld baubar sind
    for($i=0;$i<count($bldgdef);$i++)
    {
    	if($berg>=$bldgdef[$i][3][0] AND $wiese>=$bldgdef[$i][3][1] AND $wald>=$bldgdef[$i][3][2])
    	make_build_str($i, $bg, 0, 0);
    }
    
    if($errmsg!='')
    {
  	  $bg='cell';
  	  echo '<tr align="left">
		   <td class="'.$bg.'"><b>&nbsp;Achtung: '.$errmsg.'</b></td>
		   </tr>';
    }
    echo '</table>';    
  }
  else //gebäude ist vorhanden, dieses anzeigen
  {
  	for($i=0;$i<count($bldgdef);$i++)
  	{
  	  if($bldgdef[$i][4][0]==$bldg)
  	  {
  	  	$gebid=$i;
  	  	break;
  	  }
  	}
  	$bg='cell';
    echo '<table width="100%" cellpadding="1" cellspacing="1">
	  	  <tr align="left">
		    <td width="45%" class="'.$bg.'"><b>&nbsp;Geb&auml;ude: '.$bldgdef[$gebid][0].'</b></td>
		    <td width="15%" class="'.$bg.'"><b>&nbsp;Stufe: '.$fieldlevel.'</b></td>
		    <td width="40%" class="'.$bg.'"><b>&nbsp;Freie Kapazit&auml;t: '.$fieldamount.'</b></td>
		  </tr></table>';
    $bg='cell1';
    echo '<table width="100%" cellpadding="1" cellspacing="1">
	  	  <tr align="left">
		    <td class="'.$bg.'"><b>&nbsp;Folgende Aktionen sind hier m&ouml;glich:</b></td>
		  </tr>';    
    
    $bg='cell';
    //gebäude ausbauen:
    make_build_str($gebid, $bg, 1, $fieldlevel);
    //ernten/minen
    make_gather_str($gebid, $bg);
    //gebäude verlassen
    echo '<tr align="left">
		    <td class="'.$bg.'"><b>&nbsp;<a href="#" onClick="lnk(\'\')">Geb&auml;ude verlassen</a></b></td>
		  </tr>';
    //evtl. fehlermeldungen ausgeben
    if($errmsg!='')
    {
  	  $bg='cell';
  	  echo '<tr align="left">
		   <td class="'.$bg.'"><b>&nbsp;Achtung: '.$errmsg.'</b></td>
		   </tr>';
    }
   
    echo '</table>';
  }
  
  //echo '</div>';
  
  rahmen1_unten();
  
  rahmen0_unten();
  
	//infoleiste anzeigen
	show_infobar();
  
  
  echo '</body></html>';
  exit;
}

function make_gather_str($bldgid, $bg)
{
  global $bldgdef;
  
  for($i=0;$i<count($bldgdef[$bldgid][5]);$i++)
  {
  	$ttstr='';
    //benötigtes werkzeug
    if($bldgdef[$bldgid][5][$i][1]!='')
    {
      $itemid=$bldgdef[$bldgid][5][$i][1];
      //itemdaten auslesen
      $filename='eftadata/items/'.$itemid.'.item';
      include($filename);
      if($ttstr!='')$ttstr.='<br>';
      $ttstr.='1x '.$item_name;
    }
    //items die dabei verbraucht werden
    for($j=0;$j<count($bldgdef[$bldgid][5][$i][4]);$j++)
    {
      if($bldgdef[$bldgid][5][$i][4][$j]!='')
      {
        $need=explode(";",$bldgdef[$bldgid][5][$i][4][$j]);
        $itemid=$need[0];
        $itemmenge=$need[1];      
        //itemdaten auslesen
        $filename='eftadata/items/'.$itemid.'.item';
        include($filename);
        if($ttstr!='')$ttstr.='<br>';
        $ttstr.=$itemmenge.'x '.$item_name;
      }
    }
    
    //aktionspunkte mit angeben
    if($ttstr!='')$ttstr.='<br>';
    $ttstr.=$bldgdef[$bldgid][5][$i][0].' Aktionspunkte';
    //tooltip ausgeben

    $gtip = 'Voraussetzung&'.$ttstr;
  
    //linktext erzeugen
    $linktext=$bldgdef[$bldgid][5][$i][2];
  
    echo '<tr align="left">
          <td class="'.$bg.'" title="'.$gtip.'"><b>&nbsp;<a href="#" onClick="lnk(\'bldgmenu=1&gather=1&choose='.$i.'\')">'.$linktext.'</b></a></td>
		  </tr>';
  }
}

function make_build_str($bldgid, $bg, $upgrade, $fieldlevel)
{
  global $bldgdef;
  
  $ttstr='';
  //benötigte rohstoffe feststellen
  for($i=0;$i<10;$i++)
  {
    //wenn das array belegt ist, dann das item darstellen
    if($bldgdef[$bldgid][2][$i]!='')
    {
      $need=explode(";",$bldgdef[$bldgid][2][$i]);
      $itemid=$need[0];
      $itemmenge=$need[1];
      //itemdaten auslesen
      $filename='eftadata/items/'.$itemid.'.item';
      include($filename);
      if($ttstr!='')$ttstr.='<br>';
      $ttstr.=$itemmenge.'x '.$item_name;
    }
  }  
  //aktionspunkte mit angeben
  if($upgrade==0)$ttstr.='<br>'.$bldgdef[$bldgid][1][0].' Aktionspunkte';
  else $ttstr.='<br>'.($bldgdef[$bldgid][1][1]*($fieldlevel+1)).' Aktionspunkte';
  //tooltip ausgeben

  $btip = 'Baukosten '.$bldgdef[$bldgid][0].'&'.$ttstr;
  
  //linktext erzeugen
  if($upgrade==0)$linktext=$bldgdef[$bldgid][0];
  else $linktext=$bldgdef[$bldgid][0].' auf Stufe '.($fieldlevel+1).' ausbauen';
  
  echo '<tr align="left">
          <td class="'.$bg.'" title="'.$btip.'"><b>&nbsp;<a href="#" onClick="lnk(\'bldgmenu=1&buildid='.($bldgid+1).'\')">'.$linktext.'</b></a></td>
		</tr>';
}
?>