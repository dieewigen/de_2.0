<?php
function getformattedlog($string,$mode,$returnType = "kurz") {

	if($mode == "/military.php") {
		$reg1 = '/\s*(\w*)\s*=>(.*)/';
		preg_match_all($reg1,$string,$blub);
		for($i = 0; $i < count($blub[1]); $i++) {
			if(substr($blub[1][$i],0,2) == "m8") {
					switch(substr($blub[1][$i],2,1)) {
						case 1:
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Gesamt'] += $blub[2][$i];
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Nissen'] = $blub[2][$i];
							continue;
						case 2:
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Boote'] = $blub[2][$i];
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Gesamt'] += $blub[2][$i];
							continue;
						case 3:
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Zerren'] = $blub[2][$i];
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Gesamt'] += $blub[2][$i];
							continue;
						case 4:
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Kreuzer'] = $blub[2][$i];
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Gesamt'] += $blub[2][$i];
							continue;
						case 5:
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Schlachter'] = $blub[2][$i];
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Gesamt'] += $blub[2][$i];
							continue;
						case 6:
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Bomber'] = $blub[2][$i];
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Gesamt'] += $blub[2][$i];
							continue;
						case 7:
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Transen'] = $blub[2][$i];
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Gesamt'] += $blub[2][$i];
							continue;
						case 8:
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Traeger'] = $blub[2][$i];
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Gesamt'] += $blub[2][$i];
							continue;
					}				
				} else if(substr($blub[1][$i],0,5) == "zsecf") {
					$blub2["Flotte".substr($blub[1][$i],-1,1)]['Zielsek'] = $blub[2][$i];
				} else if(substr($blub[1][$i],0,5) == "zsysf") {
					$blub2["Flotte".substr($blub[1][$i],-1,1)]['Zielsystem'] = $blub[2][$i];
				} else if(substr($blub[1][$i],0,2) == "af") {
					switch($blub[2][$i]) {
						case 1:
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Auftrag'] = "Recall";	
							continue;
						case 2:
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Auftrag'] = "Att";	
							continue;
						case 3:	
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Auftrag'] = "Deff(1)";	
							continue;
					case 4:	
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Auftrag'] = "Deff(2)";	
							continue;
					case 5:	
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Auftrag'] = "Deff(3)";	
							continue;
					case 6:	
							$blub2["Flotte".substr($blub[1][$i],-1,1)]['Auftrag'] = "Archo";	
							continue;
					}
				} else if($blub[1][$i] == "verlegen") {
					$blub2['verlegen'] = $blub[2][$i];
				}
			}
		$html = "<table><thead><tr><th>Schiffsname</th><th>Flotte1</th><th>Flotte2</th><th>Flotte3</th></tr></thead><tbody>";
		for($i = 0; $i < count($blub2['Flotte1']); $i++) {
			if(key($blub2['Flotte1']) == "Auftrag") {
				$html .= "<tr><td>".key($blub2['Flotte1'])."</td><td>".$blub2['Flotte1']['Auftrag']." ".trim($blub2['Flotte1']['Zielsek']).":".trim($blub2['Flotte1']['Zielsystem'])."</td><td>".$blub2['Flotte2']['Auftrag']." ".trim($blub2['Flotte2']['Zielsek']).":".trim($blub2['Flotte2']['Zielsystem'])."</td><td>".$blub2['Flotte3']['Auftrag']." ".trim($blub2['Flotte3']['Zielsek']).":".trim($blub2['Flotte3']['Zielsystem'])."</td></tr>";
				break;
			} else {	
				$html .= "<tr><td>".key($blub2['Flotte1'])."</td><td>".current($blub2['Flotte1'])."</td><td>".current($blub2['Flotte2'])."</td><td>".current($blub2['Flotte3'])."</td></tr>";
			}
			next($blub2['Flotte1']);
			next($blub2['Flotte2']);
			next($blub2['Flotte3']);
		}
		$html .= "</tbody></table>";
		if($returnType == 'lang') {
			return $html;
		} else {
			return "".$blub2['Flotte1']['Gesamt']." ".trim($blub2['Flotte1']['Zielsek']).":".trim($blub2['Flotte1']['Zielsystem'])." ".$blub2['Flotte1']['Auftrag']."  <br/>  ".$blub2['Flotte2']['Gesamt']." ".trim($blub2['Flotte2']['Zielsek']).":".trim($blub2['Flotte2']['Zielsystem'])." ".$blub2['Flotte2']['Auftrag']." <br /> ".$blub2['Flotte3']['Gesamt']." ".trim($blub2['Flotte3']['Zielsek']).":".trim($blub2['Flotte3']['Zielsystem'])." ".$blub2['Flotte3']['Auftrag']."";
		}
	} else if($mode == "/hyperfunk.php") {
		$reg1 = '/\s*(zielsek|zielsys)\s*=>\s*(\d+)/';
		$reg2 = '/\s*nachricht\s*=>(.*)antbut/s';
		preg_match_all($reg2,$string,$nachricht);
		preg_match_all($reg1,$string,$ziele);
		return "".$ziele[2][0].":".$ziele[2][1]." ->".$nachricht[1][0]."";
	} else if($mode == "/chat.php") {
		$reg1 = '/\s*chat_message\s*=>\s*(.*)Post/s';
		preg_match_all($reg1,$string,$chatmessage);
		return $chatmessage[1][0];
	} else {
		return $string;	
	}
}
?>