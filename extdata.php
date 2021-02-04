<?php
//db connect
include 'inccon.php';

//nachrichten
if($_REQUEST['typ']==1)
{
	$anz=intval($_REQUEST['param1']);
	unset($dataarray);$c=0;
	$sel_news=mysql_query("SELECT * FROM de_news_overview ORDER BY id desc Limit 0,$anz");
  	while($rew=mysql_fetch_array($sel_news))
  	{
  		$dataarray[$c]['id']=$rew['id'];
  		$dataarray[$c]['typ']=$rew['typ'];
  		$dataarray[$c]['betreff']=utf8_encode($rew['betreff']);
  		$dataarray[$c]['nachricht']=utf8_encode($rew['nachricht']);
  		$dataarray[$c]['time']=$rew['time'];
  		$c++;
  }
  
	$data = array (
	'data' => $dataarray
	);
	echo json_encode($data);
	exit;
}

?>