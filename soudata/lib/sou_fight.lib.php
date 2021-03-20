<?php
mt_srand((double)microtime()*10000);
//der gegnerdatensatz muß vom aufrufenden script bereits nach $enmrow geladen worden sein
function do_fight($enm1, $enm2)
{
  $fighlog='';
  
  $enm[0]=$enm1;
  $enm[1]=$enm2;
  

  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////
  //  kampf berechnen
  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////

  $trefferwahrscheinlichkeit[0]=80;
  $trefferwahrscheinlichkeit[1]=80;
  
  $schadenssenkung[0]=0;
  $schadenssenkung[1]=0;
  
  $critchance[0]=10;
  $critchance[1]=10;
  
  $schaden[0]=$enm[0]['att'];
  $schaden[1]=$enm[1]['att'];
  
  //die daten der gegner anzeigen
  $fightlog=
  '<tr align="center" class="bg1"><td width="20%" class="c2">Einheit</td><td width="30%" class="c2">'.$enm[0][name].'</td><td width="30%" class="c2">'.$enm[1][name].'</td></tr>
   <tr align="center" class="bg1"><td class="c2">Gr&ouml;&szlige</td><td class="c2">'.number_format($enm[0]['ship_diameter'], 0,"",".").'</td><td class="c2">'.number_format($enm[1]['ship_diameter'], 0,"",".").'</td></tr>
   <tr align="center" class="bg1"><td class="c2">H&uuml;llenstruktur</td><td class="c2">'.number_format($enm[0]['hp'], 0,"",".").'</td><td class="c2">'.number_format($enm[1]['hp'], 0,"",".").'</td></tr>
   <tr align="center" class="bg1"><td class="c2">Schilde</td><td class="c2">'.number_format($enm[0]['shield'], 0,"",".").'</td><td class="c2">'.number_format($enm[1]['shield'], 0,"",".").'</td></tr>
   <tr align="center" class="bg1"><td class="c2">Waffen</td><td class="c2">'.number_format($enm[0]['att'], 0,"",".").'</td><td class="c2">'.number_format($enm[1]['att'], 0,"",".").'</td></tr>
   <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
  ';
  
  $enm[0]['hp']+=$enm[0]['shield'];
  $enm[1]['hp']+=$enm[1]['shield'];
  
  $fightlog.=
  '<tr align="center" class="bg2"><td width="10%">Runde</td><td width="45%">'.$enm[0][name].'</td><td width="45%">'.$enm[1][name].'</td></tr>
  <tr align="center" class="bg2"><td>1</td><td title="&Aufstellung">'.number_format($enm[0][hp], 0,"",".").'</td><td title="&Aufstellung">'.
  number_format($enm[1][hp], 0,"",".").'</td></tr>';
  
  
  $maxrunden=60;$haswon=0;
  for($r=1;$r<$maxrunden;$r++)
  {
    //echo '<br>'.$r;
    //gegnerschaden berechnen
    $critflag[0]=0;
    $critflag[1]=0;
    $ausweichflag[0]=0;
    $ausweichflag[1]=0;
    
    $schaden[0]=$enm[0]['att'];
    $schaden[1]=$enm[1]['att'];
    
    for($c=0;$c<=1;$c++)
    {
      //trefferwahrscheinlichkeit
      //zum testen auf 50% gesetzt
      if ($trefferwahrscheinlichkeit[$c] >= mt_rand(0, 100))
      {
        //waffenschaden
        /*
        $schaden[$c]=round(mt_rand($enm[$c][mindmg], $enm[$c][maxdmg]));
        if($c==0)$schadenssenkung_enm=$schadenssenkung[1];
        elseif($c==1)$schadenssenkung_enm=$schadenssenkung[0];
        */
        $schaden[$c]=round($schaden[$c]*(100-$schadenssenkung_enm)/100);
        
        //$schaden[$c]+=$eschaden[$c];
        
        //test auf kritischen treffer
        if($critchance[$c]>mt_rand(1,100)){$schaden[$c]=$schaden[$c]*2; $critflag[$c]=1;}
          
        if($schaden[$c]<0)$schaden[$c]=0;
        //echo 'treffer'.$schaden[$c].'<br>';
      }
      else 
      {
        $schaden[$c]=0; 
        $ausweichflag[$c]=1;
      }
    }

	//echo '<br>Schaden 0: '.$schaden[0];
	//echo '<br>Schaden 1: '.$schaden[1];
    
    
    //player 1 schlägt zu
    if($enm[1][hp]-$schaden[0]<=0)
    {
      //player 2 hat verloren
      $haswon=1;
      $enm[1][hp]-=$schaden[0];
      $ausweichflag[1]=1;
      $critflag[1]=0;
    }
    else
    {
      //player 2 hp abziehen
      $enm[1][hp]-=$schaden[0];
    }

    //player 2schlägt zu
    if($enm[0][hp]-$schaden[1]<=0 AND $haswon==0)
    {
      //player 1 hat verloren
      $enm[0][hp]-=$schaden[1];
      $haswon=2;
      $ausweichflag[0]=1;
      $critflag[0]=0;
    }
    else
    {
      //player 1 hp abziehen
      if($haswon==0)$enm[0][hp]-=$schaden[1];
    }
    
    //die einzelnen kampfphasen mitloggen
    $title[0]='&';$title[1]='&';
    //crit
    $format[0]='';
    $format[1]='';
    $format[2]='';
    $format[3]='';
    if($critflag[0]==1){$title[0].='Dein Gegner hat einen kritischen Treffer erhalten.';$format[2]='<b>';$format[3]='</b>';}
    if($critflag[1]==1){$title[1].='Du hast einen kritischen Treffer erhalten.';$format[0]='<b>';$format[1]='</b>';}
    
    //verfehlt
    if($ausweichflag[0]==1)$title[0].='Du hast Deinen Gegner verfehlt.';
    if($ausweichflag[1]==1)$title[1].='Dein Gegner hat Dich verfehlt.';
    //normaler treffer
    if($title[0]=='&')$title[0].='Du hast Deinen Gegner getroffen.';
    if($title[1]=='&')$title[1].='Dein Gegner hat Dich getroffen.';
    //$title='';
    
    $fightlog.='<tr align="center" class="bg2"><td>'.($r+1).'</td><td title="'.$title[1].'">'.$format[0].number_format($enm[0]['hp'], 0,"",".").$format[1].
    '</td><td title="'.$title[0].'">'.$format[2].number_format($enm[1]['hp'], 0,"",".").$format[3].'</td></tr>';
    
    //wenn jemand gewonnen hat kampf abbrechen
    if($haswon>0)$r=$maxrunden;
	
    //nach maxrunden runden hat der gewonnen der mehr hp hat, wenn beide gleichviel haben, dann wird gelost
    if($r==($maxrunden-1) AND $haswon==0)//unentschieden, gewinner wird ausgelost
    {
      //zufall  
      if($enm[0][hp]==$enm[1][hp])$haswon=mt_rand(1, 2);
      else 
      {
      	if($enm[0][hp]>$enm[1][hp])$haswon=1; else $haswon=2;
      }
    }
    
      //echo '<br>HP1: '.$userhp[0].'<br>';
      //echo 'HP2: '.$userhp[1].'<br>';
  }//ende maxrunden

  //echo '<br>haswon: '.$haswon.'<br>';
  $fightlog='<table cellpadding="0" cellspacing="0" border="0" width="100%">
  '.$fightlog.'</table>';
  
  
  
  $returnarray['haswon']=$haswon;
  $returnarray['fightlog']=$fightlog;
   
  return($returnarray);
}
?>