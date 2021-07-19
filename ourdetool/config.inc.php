<?
$cfgProgName	= 'Benutzermanager';
$cfgVersion	= '2.0';

$cfgUseAuth	= false;
$cfgSuperUser	= '';
$cfgSuperPass	= '';
$cfgBadChars	= '`~!@#$%^&*()+=[]{};\'\\:"|,/<>? ';
$cfgBadCharsE	= '`~!#$%^&*()+=[]{};\'\\:"|,/<>?, ';
$cfgBadCharsR	= '`~!@#$%^&*()+=[]{};\'\\:"|,/<>?';

$cfgHTPasswd[0][N] = '/var/.htpasswddetool';
$cfgHTPasswd[0][D] = '[Geschützter Bereich] (Administration)';
//$cfgHTPasswd[1][N] = 'c:\pfad\zur\passwd\passwd2';
//$cfgHTPasswd[1][D] = '[Geschützter Bereich] mehr bla bla bla (Administration)';

$showtext = "Anzeigen";
$newusertext = "Neuer Benutzer";
//$viewhtpasswdtext = "Passworddatei anzeigen";
//$createhtaccesstext = ".htaccess erzeugen";
$showuserlisttext = "Benutzerliste anzeigen";
$edittext = "Bearbeiten";
$deletetext = "Löschen";
//$mainpagetext = "Hauptseite";


$htpUser	= array();

$version = explode(".", phpversion());
if (intval($version[0]) < 5 && intval($version[1]) < 1) {
  $_POST   = $HTTP_POST_VARS;
  $_GET    = $HTTP_GET_VARS;
  $_SERVER = $HTTP_SERVER_VARS;
}

function is_valid_string($string) {
  global $cfgBadChars;

  if (empty($string))
    return true;

  for ($i = 0; $i < strlen($cfgBadChars); $i++) {
    if (strstr($string, $cfgBadChars[$i]))
      return true;
  }
  return false;
}

function is_valid_email($string) {
  global $cfgBadCharsE;

  if (empty($string))
    return false;

  for ($i = 0; $i < strlen($cfgBadCharsE); $i++) {
    if (strstr($string, $cfgBadCharsE[$i]))
      return true;
  }
  return false;
}

function is_valid_realname($string) {
  global $cfgBadCharsR;

  if (empty($string))
    return false;

  for ($i = 0; $i < strlen($cfgBadCharsR); $i++) {
    if (strstr($string, $cfgBadCharsR[$i]))
      return true;
  }
  return false;
}

function ht_error($errmsg, $htfunction) {
  echo "<p><font class=\"tdmain\"><b>Fehler:</b> (in function <i>$htfunction</i>) $errmsg</font><p>";
  exit;
}

function init_passwd_file($filenum, $htfunction) {
  global $cfgHTPasswd;

  if (empty($cfgHTPasswd[0][N]))
    ht_error("Die erste .htpasswd Datei ist nicht definiert", $htfunction);

  if (empty($cfgHTPasswd[$filenum][N]))
    return;

  if (!file_exists($cfgHTPasswd[$filenum][N]))
    ht_error(".htpasswd ($filenum) Datei existiert nicht", $htfunction);

  if (!is_readable($cfgHTPasswd[$filenum][N]))
    ht_error(".htpasswd ($filenum) file is not readable", $htfunction);

  if (!is_writeable($cfgHTPasswd[$filenum][N]))
    ht_error(".htpasswd ($filenum) Datei ist schreibgeschützt / nicht schreibbar", $htfunction);
}

function read_passwd_file($filenum) {
  global $cfgHTPasswd, $htpUser;

  init_passwd_file($filenum, "read_passwd_file");

  $htpUser = array();

  if (!($fpHt     = fopen($cfgHTPasswd[$filenum][N], "r"))) {
    ht_error("Datei ".$cfgHTPasswd[$filenum][N]." konnte nicht gelesen werden", "read_passwd_file");
  }
  $htpCount = 0;
  while (!feof($fpHt)) {
    $fpLine = fgets($fpHt, 512);
    $fpLine = trim($fpLine);
    $fpData = explode(":", $fpLine);
    $fpData[0] = trim($fpData[0]);
    $fpData[1] = chop(trim($fpData[1]));

    if (empty($fpLine) || $fpLine[0] == '#' || $fpLine[0] == '*'
    ||	empty($fpData[0]) || empty($fpData[1]))
      continue;

    $htpUser[$htpCount][username] = $fpData[0];
    $htpUser[$htpCount][password] = $fpData[1];
    $htpUser[$htpCount][realname] = $fpData[2];
    $htpUser[$htpCount][email]    = $fpData[3];
    $htpCount++;
  }
  fclose($fpHt);
  return;
}

function write_passwd_file($filenum) {
  global $cfgHTPasswd, $htpUser;

  init_passwd_file($filenum, "write_passwd_file");

  if (($fpHt = fopen($cfgHTPasswd[$filenum][N], "w"))) {
    for ($i = 0; $i < count($htpUser); $i++) {
      if (!empty($htpUser[$i][username]))
        fwrite($fpHt, $htpUser[$i][username].":".
		      $htpUser[$i][password].":".
		      $htpUser[$i][realname].":".
		      $htpUser[$i][email]."\n");
    }
    fclose($fpHt);
  }
  else {
    ht_error("Datei ".$cfgHTPasswd[$filenum][N]." konnte nicht gelesen werden", "write_passwd_file");
  }
  return;
}

function is_user($username) {
  global $htpUser;

  if (empty($username))
    return false;

  for ($i = 0; $i < count($htpUser); $i++) {
    if ($htpUser[$i][username] == $username)
      return true;
  }
  return false;
}

function random() {
  srand ((double) microtime() * 1000000);
  return rand();
}

function crypt_password($password) {
  if (empty($password))
    return "** LEERES PASSWORT **";

  $salt = random();
  $salt = substr($salt, 0, 2);
  return crypt($password, $salt);
}

function ht_auth() {
  global $cfgProgName, $cfgVersion, $cfgUseAuth;
  global $cfgSuperUser, $cfgSuperPass;
  global $_SERVER;

  if (!$cfgUseAuth)
    return;

  if (($_SERVER['PHP_AUTH_USER'] != $cfgSuperUser) || 
      ($_SERVER['PHP_AUTH_PW'] != $cfgSuperPass)) {
    header("WWW-Authenticate: Basic realm=\"$cfgProgName $cfgVersion\"");
    header("HTTP/1.0 401 Unauthorized");
    echo "<h1>$cfgProgName $cfgVersion</h1><h3>Authentifizierung schlug fehl.</h3>\n".
	 "Klicken Sie hier <a href=\"index.php\">here</a> um die Anmeldung zu wiederholen.\n";
    exit;
  }
}
?>
