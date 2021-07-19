<?php
  require 'config.inc.php';
  require 'header.inc.php';

  include "det_userdata.inc.php";

  if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $id	    = $_GET['id'];
    $userid = $_GET['userid'];
  }
  else {
    $id     = $_POST['id'];
    $userid = $_POST['userid'];
  }

  read_passwd_file($id);

  if (!is_user($htpUser[$userid][username]))
    ht_error("Benutzer existiert nicht (UserID: $userid)", "Bearbeiten");

  if (isset($_POST['submit'])) {
    $password  = trim($_POST['password']);
    $passwordv = trim($_POST['passwordv']);
    $realname  = trim($_POST['realname']);
    $realname  = ucwords($realname);
    $email     = trim($_POST['email']);
    $email     = strtolower($email);

    if (is_valid_string($password)) {
      echo "<font class=\"tdmain\">Neues Passwort enthält ungültige Zeichen</font><p>\n";
    }
    elseif (is_valid_string($passwordv)) {
      echo "<font class=\"tdmain\">Neues Passwort (Bestätigen) enthält ungültige Zeichen</font><p>\n";
    }
    elseif ($password != $passwordv) {
      echo "<font class=\"tdmain\">Passwörter sind nicht gleich</font><p>\n";
    }
    elseif (is_valid_realname($realname)) {
      echo "<font class=\"tdmain\">Userlevel enthält ungültige Zeichen</font><p>\n";
    }
    elseif (is_valid_email($email)) {
      echo "<font class=\"tdmain\">E-Mail enthält ungültige Zeichen</font><p>\n";
    }
    else {
      echo "<font class=\"tdmain\">Benutzer \"".$htpUser[$userid][username]."\" wurde geändert<p>\n";
      //$htpUser[$userid][password] = $password;
      $htpUser[$userid][password] = crypt_password($password);
      $htpUser[$userid][realname] = $realname;
      $htpUser[$userid][email]	  = $email;
      write_passwd_file($id);
      read_passwd_file($id);
      //hack für pde, userdatei anlegen
      $filename = "user/".$htpUser[$userid][username].".txt";
      $fp = fopen ($filename, 'w');
      fputs($fp, $email."\n");
      $realname=(int)$realname;
      if ($htpUser[$userid][username]!='Admin')
      if ($realname<2 OR $realname>99)$realname=99;
      fputs($fp, $realname."\n");
      fclose($fp);
    }
  }
?>
<table border="0" cellspacing="3" cellpadding="2" width="600">
  <form method="post" action="<?php echo $PHP_SELF.'?'.random() ?>">
  <input type="hidden" name="id" value="<?php echo $id ?>">
  <input type="hidden" name="userid" value="<?php echo $userid ?>">
  <tr>
    <td colspan="2" width="100%" align="left" class="tdheader"><?php echo $cfgProgName.' '.$cfgVersion ?></td>
  </tr>
  <tr>
    <td colspan="2" width="100%" align="left" class="tdheader">[ <?php echo $cfgHTPasswd[$id][D] ?> ]</td>
  </tr>
  <tr>
    <td width="30%" align="right" class="tdmain">Benutzername:</td>
    <td width="70%" align="left" class="tdmain"><?php echo $htpUser[$userid][username] ?></td>
  </tr>
  <tr>
    <td width="30%" align="right" class="tdmain">Neues Passwort:</td>
    <td width="70%" align="left" class="tdmain"><input type="password" name="password" size="25" maxlength="25"></td>
  </tr>
  <tr>
    <td width="30%" align="right" class="tdmain">Neues Passwort (Bestätigen):</td>
    <td width="70%" align="left" class="tdmain"><input type="password" name="passwordv" size="25" maxlength="25"></td>
  </tr>
  <tr>
    <td width="30%" align="right" class="tdmain">Userlevel(2-99):</td>
    <td width="70%" align="left" class="tdmain"><input type="text" name="realname" size="25" maxlength="100" value="<?php echo ((isset($realname)) ? $realname : $htpUser[$userid][realname]) ?>"></td>
  </tr>
  <tr>
    <td width="30%" align="right" class="tdmain">E-Mail:</td>
    <td width="70%" align="left" class="tdmain"><input type="text" name="email" size="25" maxlength="150" value="<?php echo ((isset($email)) ? $email : $htpUser[$userid][email]) ?>"></td>
  </tr>
  <tr>
    <td colspan="2" width="100%" align="center" class="tdmain"><input type="submit" name="submit" value=" speichern "></td>
  </tr>
  </form>
</table>
<table border="0" cellspacing="3" cellpadding="2" width="600">
  <tr>
    <td width="100%" align="left" class="tdmain">[
    <a href="index.php?<?php echo random() ?>"><?php echo $mainpagetext ?></a> |
    <a href="browse.php?id=<?php echo $id ?>&sid=<?php echo random() ?>"><?php echo $showuserlisttext ?></a> |
    <a href="add.php?id=<?php echo $id ?>&sid=<?php echo random() ?>"><?php echo $newusertext ?></a> |
    <a href="view-htpasswd.php?id=<?php echo $id ?>&sid=<?php echo random() ?>"><?php echo $viewhtpasswdtext ?></a> | 
    <a href="create-htaccess.php?id=<?php echo $id ?>&sid=<?php echo random() ?>"><?php echo $createhtaccesstext ?></a> ]</td>
  </tr>
</table>
<?php
  require 'footer.inc.php';
?>
