<?php
/* ------------------------------------------------------------------------
 * outputlib.php
 * ------------------------------------------------------------------------
 * Version 1.00
 * by Ying Zhang (ying@zippydesign.com)
 * Last Modified: June 17, 2000
 *
 * ------------------------------------------------------------------------
 * TERMS OF USAGE:
 * ------------------------------------------------------------------------
 * You are free to use this library in any way you want, no warranties are
 * expressed or implied.  This works for me, but I don't guarantee that it
 * works for you, USE AT YOUR OWN RISK.
 *
 * While not required to do so, I would appreciate it if you would retain
 * this header information.  If you make any modifications or improvements,
 * please send them via email to Ying Zhang <ying@zippydesign.com>.
 *
 * ------------------------------------------------------------------------
 * DESCRIPTION:
 * ------------------------------------------------------------------------
 * This library privides functions for displaying formatted output.
 */

function format_output($output) {
	
	global $outputlib_dontchangetags;
/****************************************************************************
 * Takes a raw string ($output) and formats it for output using a special
 * stripped down markup that is similar to HTML
 ****************************************************************************/
	/* Anführungszeichen abfangen */
	$output = str_replace('>', '&gt;', $output);
	$output = str_replace('<', '&lt;', $output);

	$output = str_replace('\"', '&quot;', $output);
	$output = str_replace('\'', '&acute;', $output);

	$output = str_replace('"', '&quot;', $output);
	$output = str_replace("'", '&acute;', $output);	


	$output = htmlspecialchars(stripslashes($output), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

	if($outputlib_dontchangetags!=1){
		/* new paragraph */
		$output = str_replace('[p]', '<p>', $output);

		/* bold */
		$output = str_replace('[b]', '<b>', $output);
		$output = str_replace('[/b]', '</b>', $output);

		/* italics */
		$output = str_replace('[i]', '<i>', $output);
		$output = str_replace('[/i]', '</i>', $output);

		/* preformatted */
		$output = str_replace('[pre]', '<pre>', $output);
		$output = str_replace('[/pre]', '</pre>', $output);

		/* indented blocks (blockquote) */
		$output = str_replace('[indent]', '<blockquote>', $output);
		$output = str_replace('[/indent]', '</blockquote>', $output);

		/* anchors */
		$output = preg_replace('/\[anchor=&quot;([[:graph:]]+)&quot;\]/', '<a name="\\1"></a>', $output);

		/* links, note we try to prevent javascript in links */
		$output = str_replace('[link=&quot;javascript', '[link=&quot; javascript', $output);
		$output = preg_replace('/\[link=&quot;([[:graph:]]+)&quot;\]/', '<a href="\\1">', $output);
		$output = str_replace('[/link]', '</a>', $output);
	}

	return nl2br($output);
}

function print_output($output) {
/****************************************************************************
 * Calls format_output and displays the output
 ****************************************************************************/

	echo format_output($output);
}
?>