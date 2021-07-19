<?php
  require './config.inc.php';
  require './header.inc.php';

  include "det_userdata.inc.php";

  if ($_SERVER['REQUEST_METHOD'] == "GET")
    $id = $_GET['id'];
  else
    $id = $_POST['id'];

  read_passwd_file($id);

  if (isset($_POST['submit'])) {
    $username  = trim($_POST['username']);
    $password  = trim($_POST['password']);
    $passwordv = trim($_POST['passwordv']);
    $realname  = trim($_POST['realname']);
    $realname  = ucwords($realname);
    $email     = trim($_POST['email']);
    $email     = strtolower($email);

    if (is_user($username)) {
      echo "<font class=\"tdmain\">Benutzer \"$username\" existiert bereits</font><p>\n";
      $username = '';
    }
    elseif (is_valid_string($username)) {
      echo "<font class=\"tdmain\">Benutzername enthält ungültige Zeichen</font><p>\n";
      $username = '';
    }
    elseif (is_valid_string($password)) {
      echo "<font class=\"tdmain\">Passwort enthält ungültige Zeichen</font><p>\n";
    }
    elseif (is_valid_string($passwordv)) {
      echo "<font class=\"tdmain\">Passwort (Verify) enthält ungültige Zeichen</font><p>\n";
    }
    elseif ($password != $passwordv) {
      echo "<font class=\"tdmain\">Passwörter sind verschieden</font><p>\n";
    }
    elseif (is_valid_realname($realname)) {
      echo "<font class=\"tdmain\">Userlevel enthält ungültige Zeichen</font><p>\n";
      $realname = '';
    }
    elseif (is_valid_email($email)) {
      echo "<font class=\"tdmain\">E-Mail enthält ungültige Zeichen</font><p>\n";
      $email = '';
    }
    else {
      echo "<font class=\"tdmain\">Benutzer \"$username\" wurde hinzugefügt<p>\n";
      $userid = count($htpUser);
      $htpUser[$userid][username] = $username;
      //$htpUser[$userid][password] = $password;
      $htpUser[$userid][password] = crypt_password($password);
      $htpUser[$userid][realname] = $realname;
      $htpUser[$userid][email]	  = $email;
      write_passwd_file($id);
      read_passwd_file($id);
      //hack für pde, userdatei anlegen
      $filename = "user/".$username.".txt";
      $fp = fopen ($filename, 'w');
      fputs($fp, $email."\n");
      $realname=(int)$realname;
      if ($realname<2 OR $realname>99)$realname=99;
      fputs($fp, $realname."\n");
      fclose($fp);

      # clean form
      $username = '';
      $realname = '';
      $email    = '';
    }
  }
?>
<table border="0" cellspacing="3" cellpadding="2" width="600">
  <form method="post" action="<?php echo $PHP_SELF.'?'.random() ?>">
  <input type="hidden" name="id" value="<?php echo $id ?>">
  <tr>
    <td colspan="2" width="100%" align="left" class="tdheader"><?php echo $cfgProgName.' '.$cfgVersion ?></td>
  </tr>
  <tr>
    <td colspan="2" width="100%" align="left" class="tdheader">[ <?php echo $cfgHTPasswd[$id][D] ?> ]</td>
  </tr>
  <tr>
    <td width="30%" align="right" class="tdmain">Benutzername:</td>
    <td width="70%" align="left" class="tdmain"><input type="text" name="username" size="25" maxlength="25" value="<?php echo $username ?>"></td>
  </tr>
  <tr>
    <td width="30%" align="right" class="tdmain">Passwort:</td>
    <td width="70%" align="left" class="tdmain"><input type="password" name="password" size="25" maxlength="25"></td>
  </tr>
  <tr>
    <td width="30%" align="right" class="tdmain">Passwort (Bestätigen):</td>
    <td width="70%" align="left" class="tdmain"><input type="password" name="passwordv" size="25" maxlength="25"></td>
  </tr>
  <tr>
    <td width="30%" align="right" class="tdmain">Userlevel(2-99):</td>
    <td width="70%" align="left" class="tdmain"><input type="text" name="realname" size="25" maxlength="100" value="<?php echo $realname ?>"></td>
  </tr>
  <tr>
    <td width="30%" align="right" class="tdmain">E-Mail:</td>
    <td width="70%" align="left" class="tdmain"><input type="text" name="email" size="25" maxlength="150" value="<?php echo $email ?>"></td>
  </tr>
  <tr>
    <td colspan="2" width="100%" align="center" class="tdmain"><input type="submit" name="submit" value=" hinzufügen "></td>
  </tr>
  </form>
</table>
<table border="0" cellspacing="3" cellpadding="2" width="600">
  <tr>
    <td width="100%" align="left" class="tdmain">[
    <a href="index.php?<?php echo random() ?>"><?php echo $mainpagetext ?></a> |
    <a href="browse.php?id=<?php echo $id ?>&sid=<?php echo random() ?>"><?php echo $showuserlisttext ?></a> |
    <?php echo $newusertext ?> |
    <a href="view-htpasswd.php?id=<?php echo $id ?>&sid=<?php echo random() ?>"><?php echo $viewhtpasswdtext ?></a> | 
    <a href="create-htaccess.php?id=<?php echo $id ?>&sid=<?php echo random() ?>"><?php echo $createhtaccesstext ?></a> ]</td>
  </tr>
</table>
<?php
  require './footer.inc.php';
?>
