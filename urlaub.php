<?php
include "inc/sv.inc.php";
include "inc/lang/".$sv_server_lang."_urlaub.lang.php";
include 'inc/'.$sv_server_lang.'_links.inc.php';

echo '
<!DOCTYPE html>
<html lang="de">
  	<head>
  		<script>
			if(top.frames.length > 0)
			top.location.href=self.location;
			</script>';

    include "cssinclude.php";

    echo '
		</head>';
	echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

	echo '
		<div style="width: 100%;">
			<div class="info_box text3" style="margin: 30px auto 0 auto; font-size: 14px;">';

    echo '
				<div style="color: #00FF00;">
					'.$urlaub_lang['msg1'].'
				<div>

			</div>
		</div>
	</body>
</html>';
    exit;

