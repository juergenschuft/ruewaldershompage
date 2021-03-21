/***********************************************
* CONTENIDO MODUL - INPUT
*
* Modulname   :     vpGuestbook 1.9 - Eintrag
* Author      :     Ingo van Peeren
* Copyright   :     Ingo van Peeren (ingo@van-peeren.de)
* Created     :     2005-03-14
* Modified    :     2006-01-16
************************************************/

$cfg["tab"]["vpguestbook"] = $cfg['sql']['sqlprefix']."_vpguestbook";
$db = new DB_Contenido;
$sql = "CREATE TABLE IF NOT EXISTS `".$cfg["tab"]["vpguestbook"]."` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `url` varchar(80) NOT NULL default '',
  `entry` text NOT NULL,
  `image` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  `iphost` varchar(60) NOT NULL default '',
  `user1` varchar(255) NOT NULL default '',
  `user2` varchar(255) NOT NULL default '',
  `user3` varchar(255) NOT NULL default '',
  `active` int(1) NOT NULL default '1',
  `client` int(10) NOT NULL default '0',
  `lang` int(10) NOT NULL default '0',
  `art` int(10) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `client` (`client`),
  KEY `lang` (`lang`),
  KEY `art` (`art`)
) TYPE=MyISAM;";
$db->query($sql);

if (! function_exists('dir_list')) {
  #Build folder list recursively
  function dir_list($dir, $sPrefix, $show_options = 0) {
  global $sSelected, $cfgClient, $client;

    $old_path = getcwd();
        $sPathDir = $cfgClient[$client]["path"]["frontend"].$cfgClient[$client]["upload"].$dir;

    if (is_dir($sPathDir)) {
        chdir($sPathDir);
        $myhandle = opendir('.');

        while (($mydir = readdir($myhandle)) !== false) {
            if (($mydir != ".") && ($mydir != "..")) {
                if (is_dir($mydir)) {
                                        $dirs[] = $dir.$mydir."/";
                                        $prefixs[] = $sPrefix.$mydir;
                    $ret = dir_list($dir.$mydir."/", '&nbsp;&nbsp;&nbsp;&nbsp;'.$sPrefix);
                                        foreach ($ret['dirs'] as $key => $value) {
                                          $dirs[] = $value;
                                          $prefixs[] = $ret['prefixs'][$key];
                                        }
                    chdir($sPathDir);
                }
            }
        }
        closedir($myhandle);
    }

    chdir($old_path);
    if ($show_options == 1) {
          array_multisort ($dirs, $prefixs);
          foreach ($dirs as $key => $value) {
        if ($sSelected == $value) {
          echo '<option selected value="'.$value.'">'.$prefixs[$key].'</option>';
        }
            else {
          echo '<option value="'.$value.'">'.$prefixs[$key].'</option>';
        }
          }
        }
        else {
          $ret['dirs'] = $dirs;
          $ret['prefixs'] = $prefixs;
          return $ret;
        }
  }

}

$hier = getcwd ();
if ($cfgClient[$client][tpl][path]) chdir($cfgClient[$client][tpl][path]);
else chdir($cfgClient[$client][path][frontend]."templates/");
$handle = opendir(".");
while ($file = readdir($handle)) {
  if (is_dir($file)) $dirlist[] = $file;
  if (is_file($file)) $filelist[] = $file;
}
closedir($handle);
chdir($hier);
if ($filelist) {
  asort($filelist);
}

$selected = "CMS_VALUE[1]";

echo "<table cellspacing=\"0\" cellpadding=\"10\" border=\"0\">";

echo "<tr><td>Templatedatei: ";
echo "<td><select size=\"1\" name=\"CMS_VAR[3]\">";
while (list ($key, $file) = each ($filelist)) {
  echo "<option value=\"$file\"";
  if ("CMS_VALUE[3]" == $file) echo " selected=\"selected\" ";
  echo ">$file</option>";
}
echo "</td></tr>";

echo "<tr>";
echo "<td>Templateklasse:</td><td><INPUT TYPE=\"radio\" NAME=\"CMS_VAR[4]\" VALUE=\"normal\" ";
if ("CMS_VALUE[4]" == "normal") echo "checked=\"checked\" ";
echo "> normale Klasse <input type=\"radio\" name=\"CMS_VAR[4]\" value=\"extended\" ";
if ("CMS_VALUE[4]" == "extended") echo "checked=\"checked\" ";
echo "> eXtended-Template-Klasse (<a href=\"http://www.contenido.org/forum/viewtopic.php?t=5851\" target=\"_blank\">von swelpot</a>)</td></tr>";

echo "<tr><td>Smilies benutzen:</td>";
echo "<td><input type=\"checkbox\" name=\"CMS_VAR[5]\" value=\"1\" ";
if ("CMS_VALUE[5]" == 1) echo "checked=\"checked\" ";
echo "/></td></tr>";

echo "<tr><td>Bilderverzeichnis f�r Smilies:</td>";
echo "<td><select size=\"1\" name=\"CMS_VAR[6]\" />";
$sSelected = "CMS_VALUE[6]";
dir_list("", '->', 1);
echo "</select></td></tr>";

echo "<tr><td>Avatare benutzen:</td>";
echo "<td><input type=\"checkbox\" name=\"CMS_VAR[18]\" value=\"1\" ";
if ("CMS_VALUE[18]" == 1) echo "checked=\"checked\" ";
echo "/></td></tr>";

echo "<tr><td>Bilderverzeichnis f�r Avatare:</td>";
echo "<td><select size=\"1\" name=\"CMS_VAR[17]\" />";
$sSelected = "CMS_VALUE[17]";
dir_list("", '->', 1);
echo "</select></td></tr>";

echo "<tr><td>BB-Code aktiv:</td>";
echo "<td><input type=\"checkbox\" name=\"CMS_VAR[7]\" value=\"1\" ";
if ("CMS_VALUE[7]" == 1) echo "checked=\"checked\" ";
echo "/></td></tr>";

echo "<tr><td>Benutzerfeld 1:</td>";
echo "<td><input type=\"text\" name=\"CMS_VAR[8]\" value=\"CMS_VALUE[8]\" size=\"30\" /></td></tr>";

echo "<tr><td>Benutzerfeld 2:</td>";
echo "<td><input type=\"text\" name=\"CMS_VAR[9]\" value=\"CMS_VALUE[9]\" size=\"30\" /></td></tr>";

echo "<tr><td>Benutzerfeld 3:</td>";
echo "<td><input type=\"text\" name=\"CMS_VAR[10]\" value=\"CMS_VALUE[10]\" size=\"30\" /></td></tr>";

echo "<tr valign=\"top\"><td width=\"202\">Emailbenachrichtigung: ";
echo "Ja <input type=\"checkbox\" name=\"CMS_VAR[11]\" value=\"1\" ";
if ("CMS_VALUE[11]" == 1) echo "checked=\"checked\" ";
echo "> An:</td><td>";

echo "<input type=\"text\" name=\"CMS_VAR[12]\" value=\"CMS_VALUE[12]\"></td></tr>";
echo "<tr>";
echo "<td width=\"202\">Cookie gegen doppelte Eintr�ge :</td><td><input type=\"radio\" name=\"CMS_VAR[13]\" value=\"0\" ";
if ("CMS_VALUE[13]" == 0) echo "checked=\"checked\" ";
echo "> aus <input type=\"radio\" name=\"CMS_VAR[13]\" value=\"1\" ";
if ("CMS_VALUE[13]" == 1) echo "checked=\"checked\" ";
echo "> an</td></tr>";

echo "<tr>";
echo "<td width=\"202\">Cookie Lebensdauer (in Minuten) :
</td><td><input type=\"text\" name=\"CMS_VAR[14]\" value=\"CMS_VALUE[14]\" ></td></tr>";

echo "<tr><td>Eintr�ge direkt sichtbar?:</td>";
echo "<td><input type=\"checkbox\" name=\"CMS_VAR[15]\" value=\"1\" ";
if ("CMS_VALUE[15]" == 1) echo "checked=\"checked\" ";
echo "/></td></tr>";

echo "<tr>";
echo "<td width=\"202\">Ausgaben als valides :</td><td><input type=\"radio\" name=\"CMS_VAR[16]\" value=\"0\" ";
if ("CMS_VALUE[16]" == 0) echo "checked=\"checked\" ";
echo "> HTML <input type=\"radio\" name=\"CMS_VAR[16]\" value=\"1\" ";
if ("CMS_VALUE[16]" == 1) echo "checked=\"checked\" ";
echo "> XHTML</td></tr>";

echo "</table>";
