<html>
<head></head>
<body>
<?php
include 'inccon.php';
include 'functions.php';
include 'tickler/kt_einheitendaten.php';

for($r=0;$r<=4;$r++){
	for($t=0;$t<=14;$t++){

		$score=(
			$unit[$r][$t][5][0]+
			$unit[$r][$t][5][1]*2+
			$unit[$r][$t][5][2]*3+
			$unit[$r][$t][5][3]*4
			)/10
			+$unit[$r][$t][5][4]*1000;

		echo 'R'.($r).' '.$unit[$r][$t][0].': '.$score.'<br>';
	}
}


?>

</body>
</html>