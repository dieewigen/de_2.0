<?php
namespace DieEwigen\Api\Model;

/**
 * Service that checks file access
 */
class ValidateGameFilename	
{

	const VALID_FILENAMES = array(
		'hyperfunk.php',
		'military.php',
		'missions.php',
		'production.php',
		'resource.php',
		'sector.php',
		'secstatus.php',
		'ally_register2.php',
		'ally_join.php',
		'sysnews.php',
        'ally_war.php',
        'ally_antrag.php'
	);

    public function isValid($filename) :bool {
		if(in_array($filename, self::VALID_FILENAMES)) {
			return true;
		} else {
			return false;
		}
	}

}
