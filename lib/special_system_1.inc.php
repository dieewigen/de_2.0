<?php
////////////////////////////////////////////////////////////
//Startwelt DER EINGANG
////////////////////////////////////////////////////////////
	
//Description
$content.='<div>Dies ist die erste erreichbare Welt der vergessenen Systeme, wir nennen Sie daher DER EINGANG, da sie uns den Zugriff auf viele weitere Systeme ergm&ouml;glicht.</div>';

//Geb�udedaten laden
$playerBldg=loadPlayerBuildings($_SESSION['ums_user_id'], $this->system_id);

//$content.='<br>A:'.print_r($playerBldg,true);

//gibt es schon einen Außenposten?
$hasOutpost=false;
$fd=getBldgByFieldID($playerBldg, 0);
if($fd['bldg_id']==-1){//Außenposten noch nicht vorhanden
	$content.='<br><br>Es gibt hier einen stillgelegten Au&szlig;enposten. Aktiviere ihn um einen St&uuml;tzpunkt f&uuml;r weitere Aktionen zu haben.';

	if($_REQUEST['action']=='activate'){
		if($fid==0 && $fd['bldg_id']==-1){
			//Geb�ude in die DB packen
			setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, 0, 0, 1, 0);
			$fd['bldg_id']=0;
		}
	}					


	if($fd['bldg_id']!=-1){
		//ist bereits aktiv
		$content.='<div>Der Au&szlig;enposten wurde aktiviert <a href="?id='.$this->system_id.'">weiter</a></div>';
	}else{
		//ist noch nicht aktiv
		$content.='<div>Deaktivierter Au&szlig;enposten <a href="?id='.$this->system_id.'&action=activate&fid=0">aktivieren</a></div>';
	}


}else{//Minen

	$content.='<br><br>Es gibt hier stillgelegte Minen und Fabriken. Aktiviere sie um daraus Nutzen zu ziehen.';

	$fid=intval($_REQUEST['fid']);
	//kostenlos: Eisen-Mine deaktiviert x 1
	for($f=1;$f<=4;$f++){
		$fd=getBldgByFieldID($playerBldg, $f);

		//$content.='<br>B:'.print_r($fd,true);

		if($_REQUEST['action']=='activate'){
			if($fid==$f && $fd['bldg_id']==-1){
				//Gebäude in die DB packen
				setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, $fid, 3, 1, 0);
				$fd['bldg_id']=0;
			}
		}

		if($fd['bldg_id']!=-1){
			//ist bereits aktiv
			$content.='<div>Aktive Eisen-Mine</div>';
		}else{
			//ist noch nicht aktiv
			$content.='<div>Deaktivierte Eisen-Mine <a href="?id='.$this->system_id.'&action=activate&fid='.$f.'">aktivieren</a></div>';
		}

	}

	//kostenlos: Omega-Fabrik deaktiviert x 1
	for($f=5;$f<=7;$f++){
		$fd=getBldgByFieldID($playerBldg, $f);

		//$content.='<br>B:'.print_r($fd,true);

		if($_REQUEST['action']=='activate'){
			if($fid==$f && $fd['bldg_id']==-1){
				//Gebäude in die DB packen
				setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, $fid, 13, 10, 0);
				$fd['bldg_id']=0;
			}
		}

		if($fd['bldg_id']!=-1){
			//ist bereits aktiv
			$content.='<div>Aktive Omega-Fabrik</div>';
		}else{
			//ist noch nicht aktiv
			$content.='<div>Deaktivierte Omega-Fabrik <a href="?id='.$this->system_id.'&action=activate&fid='.$f.'">aktivieren</a></div>';
		}

	}


}

?>