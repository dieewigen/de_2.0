<?php
//lm 2 tor im gebirgszug
//überprüfen ob man die familienaura trägt
$db_daten=mysql_query("SELECT user_id FROM de_cyborg_quest WHERE typ=106 AND erledigt=1 AND user_id='$efta_user_id'",$eftadb);
$anz = mysql_num_rows($db_daten);
if($anz==1)
{
	
}
else
{
  mysql_query("UPDATE de_cyborg_data SET y=y-1 WHERE user_id='$efta_user_id'",$eftadb);
  $e_text='Ein magisches Kraftfeld wirft dich zurück und du hörst folgende Worte in deinem Kopf: NUR MITGLIEDER DER ALTEN FAMILIEN DÜRFEN PASSIEREN';
}
?>