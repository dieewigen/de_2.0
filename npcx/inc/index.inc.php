<?php
// Include environment configuration
require_once '../inc/env.inc.php';
?>
<!doctype html>
<html>
    <head>
        <title>NPCX</title>
	    <meta charset="UTF-8">
		<base href="/">
	    <script src="/npcx/js/jquery-3.7.1.min.js"></script>
		<script>
			var env_api_key = '<?php echo $GLOBALS['env_api_key']; ?>';
		</script>		
		<script src="/npcx/js/npcx.js?time=<?php echo time()?>"></script>
		<link rel="stylesheet" type="text/css" href="/npcx/g/npcx.css?time=<?php echo time()?>">
    </head>
    <body>
        
	
		<div class="grid-container">
			<div class="column">
				<h2>NPC-Accounts <span style="cursor: pointer;" onclick="getAllNpcUsers();">&#8635;</span></h2>
				<span id="target_npc_accounts"></span>
			</div>

			<div class="column">
				<h2>JSON-Output <span style="cursor: pointer;" onclick="getServerData();">&cir;</span></h2>
				<span id="target_json_output"></span>
			</div>			

			<div class="column tall" style="width: 600px;">
				<h2>Gameoutput</h2>
				<iframe id="target_gameoutput" style="width:600px; height:1000px; border:none;"></iframe>
			</div>

		</div>		

    </body>
</html>
