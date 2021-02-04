<?php
function get_religion_level($owner_id){

  return 8;  

  /*
  global $db, $sv_deactivate_religion;
  //transferierte credits auslesen
  $db_daten=mysql_query("SELECT de_user_data.credittransfer, de_user_data.premium, de_login.register FROM de_login LEFT JOIN de_user_data ON(de_login.user_id=de_user_data.user_id) WHERE de_login.owner_id='$owner_id' AND de_login.owner_id>0",$db);
  $row = mysql_fetch_array($db_daten);
  $credittransfer=$row['credittransfer'];
  $premium=$row['premium'];
  $accountalter=floor((time()-strtotime($row['register']))/(24*3600*30*6));
  
  $religionspunkte=0;
  
  //geworbene spieler auslesen
  $db_daten=mysql_query("SELECT de_user_data.spielername, de_user_data.sector, de_user_data.system, de_login.status, de_login.register, de_login.last_click FROM de_login LEFT JOIN de_user_data ON(de_login.user_id=de_user_data.user_id) WHERE de_user_data.werberid='$owner_id' AND de_user_data.werberid>0 ORDER BY de_user_data.spielername",$db);
  while($row = mysql_fetch_array($db_daten)){
    //�berpr�fen ob der spieler evtl. inaktiv/noch nicht aktiv ist
    //status auf 1 setzen, erstmal ist damit alles ok
    $status=1;
  
    //testen ob der account in den letzten x tagen aktiv war
    if(strtotime($row[last_click])+7*24*3600<time())$status=3;
  
    //auf urlaubsmodus testen
    if($row[status]==3)$status=4;
  
    //testen ob er aus sektor 1 raus ist
    if($row[sector]==1)$status=6;
  
    //testen ob der account bereits x tage alt ist
    if(strtotime($row[register])+30*24*3600>time())$status=2;

    //auf gesperrt status testen
    if($row[status]==2)$status=5;
  
    if($status==1)$religionspunkte+=5;
  }
  
  //premium
  //if($premium==1)$religionspunkte+=10;
  
  //accountalter
  $religionspunkte+=$accountalter;

  //$rangvorbedingungen=array(1,2,3,4,5,10,15,25,50,100);
  //$creditvorbedingungen=array(1500,3000,4500,6000,7500,9000,10500,12000,13500,15000);
  $creditvorbedingungen=array(1500,4500,7500,15000,30000,45000,75000,105000);
  
  $relpunktevorbedingungen=array(10,20,30,40,50,60,70,80,200,400);
  
  $level=0;
  
  //credittransfer
  for($i=0;$i<count($creditvorbedingungen);$i++)
  {
  	if($credittransfer>=$creditvorbedingungen[$i]){$religionspunkte+=10;}
  }
  
  //level feststellen
  for($i=0;$i<count($relpunktevorbedingungen);$i++)
  {
  	if($religionspunkte>=$relpunktevorbedingungen[$i]){$level++;}
  }
  
  if($sv_deactivate_religion==1)$level=0;
  
  //check auf korrekte verkn�pfung mit dem hauptaccount
  
  if($owner_id==0)$level=0;
  
  return($level);
  */
}
?>