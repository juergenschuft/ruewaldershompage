<?php

/***********************************************
* CONTENIDO MODUL - OUTPUT
*
* Modulname   :     vpGuestbook 1.9 - Eintrag
* Author      :     Ingo van Peeren
* Copyright   :     Ingo van Peeren (ingo@van-peeren.de)
* Created     :     2005-03-14
* Modified    :     2006-01-16
************************************************/
// fuer captcha, vor irgendeiner ausgabe eine session starten
session_start();

// Mailer-Klasse einbinden
cInclude('classes',  'class.phpmailer.php');

// CMS_VARs initialisieren
$ausgabe_cat            = "CMS_VALUE[1]";
$ausgabe_cont           = "CMS_VALUE[2]";
$tpl_datei              = "CMS_VALUE[3]";
$tpl_art                = "CMS_VALUE[4]";
$smilies_aktiv          = "CMS_VALUE[5]";
$smiliespfad            = "CMS_VALUE[6]";
$bbcode_aktiv           = "CMS_VALUE[7]";
$userfeld1              = "CMS_VALUE[8]";
$userfeld2              = "CMS_VALUE[9]";
$userfeld3              = "CMS_VALUE[10]";
$email_benachrichtigung = "CMS_VALUE[11]";
$emails_an              = "CMS_VALUE[12]";
$cookie_aktiv           = "CMS_VALUE[13]";
$cookie_dauer           = "CMS_VALUE[14]";
$direkt_sichtbar        = "CMS_VALUE[15]";
if ($direkt_sichtbar != 1) $direkt_sichtbar = 0;
$xhtml                  = "CMS_VALUE[16]";
$bilderpfad             = "CMS_VALUE[17]";
$avatare_aktiv          = "CMS_VALUE[18]";
// CMS_VARs initialisieren Ende

// eXtended-Template-Klasse (von swelpot) einbinden
// zur Verwendung siehe:
// http://www.contenido.org/forum/viewtopic.php?t=5851
if ($tpl_art == "extended") cInclude('classes',  'class.ExtendedTemplate.php');
else cInclude('classes',  'class.template.php');

if ($xhtml) $einzeltag = " /";
else $einzeltag = "";

$cfg["tab"]["vpguestbook"] = $cfg['sql']['sqlprefix']."_vpguestbook";
$db = new DB_Contenido;

// Funktionen
function testURL($url) {
  if(eregi("http://", $url))   {
    $url = str_replace ("http://", "", $url);

  }
  if ($url != "") {
    $url = "http://".$url;
  }
  return $url;
}

function cookie_setzen ($dauer) {
  global $sess, $vpgblastentry;

  $vpgblastentry = time()+$dauer*60;
  if ($sess->is_registered("vpgblastentry")) $sess->unregister("vpgblastentry");
  $sess->register("vpgblastentry");

}

function vpgb_js () {

  $js = "
    <script type=\"text/javascript\">

    function storeCaret ()
    {
      if (document.getElementById('vpgb_eintrag').entry.createTextRange) document.getElementById('vpgb_eintrag').entry.caretPos = document.selection.createRange().duplicate();
    }

    function insertAtCaret (icon1, icon2)
    {
      if (document.getElementById('vpgb_eintrag').entry.createTextRange && document.getElementById('vpgb_eintrag').entry.caretPos)
      {
        var caretPos = document.getElementById('vpgb_eintrag').entry.caretPos;
        selectedtext = caretPos.text;
        caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ? ' '+icon1 + ' ' : ' '+icon1+' ';
        caretPos.text = caretPos.text + selectedtext + icon2;
      }
      else document.getElementById('vpgb_eintrag').entry.value = document.getElementById('vpgb_eintrag').entry.value + ' '+icon1 + ' ' + icon2+' '
      document.getElementById('vpgb_eintrag').entry.focus();
    }

    function DoPrompt(action) {
    var revisedMessage;
    var post = document.getElementById(\"vpgb_eintrag\");
    var currentMessage = post.entry.value;

    if (action == \"url\") {
        var thisURL = prompt(\"URL der gewï¿½nschten Seite angeben\", \"http://\");
        var thisTitle = prompt(\"Titel der Seite angeben\", \"Seitentitel\");
        var urlBBCode = \"[URL=\"+thisURL+\"]\"+thisTitle+\"[/URL]\";
        revisedMessage = currentMessage+urlBBCode;
        post.entry.value=revisedMessage;
        post.entry.focus();
        return;
    }

    if (action == \"email\") {
        var thisEmail = prompt(\"gewï¿½nschte E-Mail-Adresse angeben\", \"\");
        var emailBBCode = \"[EMAIL]\"+thisEmail+\"[/EMAIL]\";
        revisedMessage = currentMessage+emailBBCode;
        post.entry.value=revisedMessage;
        post.entry.focus();
        return;
    }

    if (action == \"bold\") {
        var thisBold = prompt(\"den fetten Text angeben\", \"\");
        var boldBBCode = \"[b]\"+thisBold+\"[/b]\";
        revisedMessage = currentMessage+boldBBCode;
        post.entry.value=revisedMessage;
        post.entry.focus();
        return;
    }

    if (action == \"italic\") {
        var thisItal = prompt(\"den kursiven Text angeben\", \"\");
        var italBBCode = \"[i]\"+thisItal+\"[/i]\";
        revisedMessage = currentMessage+italBBCode;
        post.entry.value=revisedMessage;
        post.entry.focus();
        return;
    }

    if (action == \"underline\") {
        var thisUL = prompt(\"den unterstrichenenen Text angeben\", \"\");
        var ulBBCode = \"[u]\"+thisUL+\"[/u]\";
        revisedMessage = currentMessage+ulBBCode;
        post.entry.value=revisedMessage;
        post.entry.focus();
        return;
    }

    }
    </script>
  ";

  return $js;
}

function smilies_preg_quote($str, $delimiter)
{
    $text = preg_quote($str);
    $text = str_replace($delimiter, '\\' . $delimiter, $text);

    return $text;
}

function smilies_auslesen () {
global $cfgClient, $client, $smiliespfad;

  $fcontents = file($cfgClient[$client]['path']['frontend'] . $cfgClient[$client]['upload'] . $smiliespfad . 'smiles.pak');

  if (is_array($fcontents)) {
    $i = 0;
    foreach($fcontents as $zeile)
    {
      $smilies_daten = explode("=+:", trim(addslashes($zeile)));
      $smilies[$i]['icon'] = $smilies_daten[0];
      $smilies[$i]['erklaerung'] = $smilies_daten[1];
      $smilies[$i]['ersetzung'] = $smilies_daten[2];
      $i++;
    }
  }
  return $smilies;
}

function smilies_ersetzen ($entry) {
global $smilies, $cfgClient, $client, $smiliespfad;

  if (is_array($smilies)) {
    foreach ($smilies as $row) {
      $ers = "/(?<=.\W|\W.|^\W)" . smilies_preg_quote($row['ersetzung'], "/") . "(?=.\W|\W.|\W$)/";
      $entry = preg_replace($ers, '<img src="' . $cfgClient[$client]['upload'] . $smiliespfad. $row['icon'].'" alt="' . $row['erklaerung'] . '" />',$entry);
    }
  }
  return $entry;

}

function smilies_ausgeben ($smilies) {
global $cfgClient, $client, $smiliespfad, $einzeltag;

  $code = "";
  if (is_array($smilies)) {
    foreach($smilies as $smilie)
    {
      if ($smilie['icon'] != $h) $code .= "<a class=\"smilie\" href=\"javascript:insertAtCaret('" . htmlspecialchars($smilie['ersetzung']) . "','')\"><img src=\"" . $cfgClient[$client]['path']['htmlpath'] . $cfgClient[$client]['upload'] . $smiliespfad . $smilie['icon'] . "\" alt=\"" . htmlspecialchars($smilie['erklaerung']) . "\"".$einzeltag."></a>";
      $h = $smilie['icon'];
    }
  }
  return $code;
}

function bbcode_link ($type) {

  $link = "javascript:DoPrompt('".$type."')";

  return $link;
}

function bbcode_ersetzen ($text) {
global $xhtml;

  if ($xhtml) {
    $text = str_ireplace("[b]", "<strong>", $text);
    $text = str_ireplace("[/b]", "</strong>", $text);
    $text = str_ireplace("[i]", "<em>", $text);
    $text = str_ireplace("[/i]", "</em>", $text);
    $text = str_ireplace("[u]", "<u>", $text);
    $text = str_ireplace("[/u]", "</u>", $text);
  }
  else {
    $text = str_ireplace("[b]", "<b>", $text);
    $text = str_ireplace("[/b]", "</b>", $text);
    $text = str_ireplace("[i]", "<i>", $text);
    $text = str_ireplace("[/i]", "</i>", $text);
    $text = str_ireplace("[u]", "<u>", $text);
    $text = str_ireplace("[/u]", "</u>", $text);
  }
  $patterns = array();
  $replacements = array();
  $patterns[0] = "/\[url\]www.([^\[]*)\[\/url\]/i";
  $replacements[0] = "<a href=\"http://www.\\1\" target=_blank>\\1</a>";
  $patterns[1] = "/\[url\]([^\[]*)\[\/url\]/i";
  $replacements[1] = "<a href=\"\\1\" target=_blank>\\1</a>";
  $patterns[2] = "/\[url=([^\[]*)\]([^\[]*)\[\/url\]/i";
  $replacements[2] = "<a href=\"\\1\" target=_blank>\\2</a>";
  $patterns[3] = "/\[email\]([^\[]*)\[\/email\]/i";
  $replacements[3] = "<a href=\"mailto:\\1\">\\1</a>";
  $patterns[4] = "/\[email=([^\[]*)\]([^\[]*)\[\/email\]/i";
  $replacements[4] = "<a href=\"mailto:\\1\">\\2</a>";
  $text = preg_replace($patterns, $replacements, $text);

  return $text;
}

function bilder_liste () {
global $cfgClient, $client, $bilderpfad;

  $basedir = getcwd();
  chdir($cfgClient[$client]['path']['frontend'] . $cfgClient[$client]['upload'] . $bilderpfad);
  $handle = opendir(".");
  while ($file = readdir($handle)) {
    if (is_dir($file)) $dirlist[] = $file;
    if (is_file($file)) $filelist[] = $file;
  }
  closedir($handle);
  $wdir2 = $basedir . "/";
  chdir($wdir2);
  if ($filelist) {
    asort($filelist);
  }
  $i = 0;
  if (is_array($filelist)) {
    while (list ($key, $file) = each ($filelist)) {
      $bilder[$i] = $file;
      $i++;
    }
  }

  return $bilder;
}

function bilder_radio ($bilder) {
global $cfgClient, $client, $bilderpfad, $einzeltag;

  $code = "";
  if (is_array($bilder)) {
    foreach ($bilder as $bild) {
      $code .= "<div style=\"float: left;\"><img src=\"" . $cfgClient[$client]['path']['htmlpath'] . $cfgClient[$client]['upload'] . $bilderpfad . $bild . "\" alt=\"$bild\" ".$einzeltag."><br".$einzeltag."><input type=\"radio\" name=\"test\" value=\"$bild\"".$einzeltag."></div>\n";
    }
    $code .= "<br style=\"clear: all;\"".$einzeltag.">";
  }
  return $code;

}

function bilder_select ($bilder) {
global $cfgClient, $client, $bilderpfad, $einzeltag;

  $code = "";
  if (is_array($bilder)) {
    $code .= "<select size=\"1\" name=\"image\" id=\"image\" onchange=\"document.getElementById('vpgb_image').src= '" . $cfgClient[$client]['path']['htmlpath'] . $cfgClient[$client]['upload'] . $bilderpfad . "' + document.getElementById('vpgb_eintrag').image.options[document.getElementById('vpgb_eintrag').image.selectedIndex].value
    \">\n";
    $h = 0;
    foreach ($bilder as $bild) {
      $code .= "<option value=\"$bild\">$bild</option>\n";
      if ($h == 0) {
        $erstes = $bild;
        $h = 1;
      }
    }
    $code .= "</select>\n";
    $code .= "<img src=\"" . $cfgClient[$client]['path']['htmlpath'] . $cfgClient[$client]['upload'] . $bilderpfad . $erstes . "\" id=\"vpgb_image\" alt=\"\"".$einzeltag.">\n";
  }
  return $code;
}

function benachrichtigung ($name, $email, $url, $entry, $iphost, $user1, $user2, $user3, $image) {
global $emails_an, $userfeld1, $userfeld2, $userfeld3;

  $mailtext = "Es gibt einen neuen Eintrag im Gï¿½stebuch:

Name: ".html_entity_decode($name)."
E-Mail: $email
Homepage: $url
IP/Hostname: $iphost
$userfeld1: ".html_entity_decode($user1)."
$userfeld2: ".html_entity_decode($user2)."
$userfeld3: ".html_entity_decode($user3)."
Bild: $image
Eintrag:
".html_entity_decode($entry)."
";

  $mail = new phpmailer();
  $mail->AddAddress($emails_an);
  $mail->From     = $emails_an;
  $mail->FromName = "Gästebuch";
  $mail->Subject  = "Neuer Gästebucheintrag";
  $mail->Body     = $mailtext;
  if(!$mail->Send()){
    $notsend .= $lngNews["mailcouldnotbesend1"].$to.$lngNews["mailcouldnotbesend2"] . "<br>";
  }

}

function eintragen () {
global $db, $cfg, $client, $lang, $idart, $direkt_sichtbar, $email_benachrichtigung, $smilies_aktiv, $bbcode_aktiv, $bilder, $xhtml;

  // Initialisieren der Variablen
  $error = "";

  // Name überprüfen
  if ($_POST['name'] == "") {
    $error = "Bitte geben Sie Ihren Namen an!";
  }
  else {
    $name = htmlentities(strip_tags($_POST['name']));
  }

  // Email überprüfen
  $email_pattern = '/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i';
  if (($_POST['email'] == "") || (preg_match($email_pattern, $_POST['email']))) {
    $email = $_POST['email'];
  }
  else {
    $error = "Bitte geben Sie eine gï¿½ltige Email-Adresse an!";
  }

  // URL überprüfen
  if ($_POST['url'] != "") {
    $url = htmlentities(strip_tags($_POST['url']));
    $url = testURL($url);
  }

  // Eintrag überprüfen
  if ($_POST['entry'] == "") {
    $error = "Bitte schreiben Sie einen Eintrag!";
  }
  else {
    $hentry = strip_tags(htmlentities($_POST['entry']));
    $entry = $hentry;
    if ($smilies_aktiv) $entry = smilies_ersetzen($entry);
    if ($bbcode_aktiv) $entry = bbcode_ersetzen($entry);
  }

  // Bilder prüfen
  if (($_POST['image'] == "") || (in_array ($_POST['image'], $bilder))) {
    $image = $_POST['image'];
  }

  // Uservariablen überprüfen
  $user1 = htmlentities(strip_tags($_POST['user1']));
  $user2 = htmlentities(strip_tags($_POST['user2']));
  $user3 = htmlentities(strip_tags($_POST['user3']));

  // IP und Hostname speichern
  $iphost = $_SERVER["REMOTE_ADDR"];
  $host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
  if ($host) $iphost .= " / " . $host;

$spamerror = 0;

if (!($_POST["captcha_code"] == $_SESSION["captcha_spam"]))
{$spamerror++;}

if ($spamerror > 0)
{$error="spam";}


  // der eigentliche Eintrag in die Datenbank
  if ($error == "") {
    $sql = "INSERT INTO ".$cfg["tab"]["vpguestbook"]." (name, email, url, entry, image, iphost, user1, user2, user3, active, client, lang, date) VALUES ('$name', '$email', '$url', '$entry', '$image', '$iphost', '$user1', '$user2', '$user3', $direkt_sichtbar, $client, $lang, NOW())";
    if (! $db->query($sql)) $error = $db->$Error;
    elseif ($email_benachrichtigung) benachrichtigung ($name, $email, $url, $hentry, $iphost, $user1, $user2, $user3, $image);
  }
  return $error;
}
// Funktionen Ende

if ($smilies_aktiv) {
  // Smilies-Array füllen
  $smilies = smilies_auslesen();
}
$bilder = bilder_liste();

$formular_anzeigen = TRUE;

if ($_POST) {
  if ($sess->is_registered("vpgblastentry")) {
    $lastentry = $GLOBALS["vpgblastentry"];
  }
  if (($lastentry >= time()) && ($cookie_aktiv)) $fehler = "Bitte nicht mehrmals hintereinander eintragen!";
  else $fehler = eintragen();
  if ($fehler != "") {
    $formular_anzeigen = TRUE;
    echo "Fehler: " . $fehler;
  }
  elseif ($direkt_sichtbar) {
    echo "<p>Vielen Dank f&uuml;r Ihren Eintrag!</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>";
    $formular_anzeigen = FALSE;
    if ($cookie_aktiv) cookie_setzen ($cookie_dauer);
  }
  else {
    echo "<p>Vielen Dank fï¿½r Ihren Eintrag! Dieser wird nach Prï¿½fung freigeschaltet.</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>";
    $formular_anzeigen = FALSE;
    if ($cookie_aktiv) cookie_setzen ($cookie_dauer);
  }
}

if ($formular_anzeigen) {
  if ($smilies_aktiv) {
    // Smilies HTML-Ausgabe
    $smiliesform = smilies_ausgeben($smilies);
  }
  if ($smilies_aktiv || $bbcode_aktiv) echo vpgb_js();

  if ($avatare_aktiv) {
    $imageselect = bilder_select($bilder);
    $imageradio = bilder_radio($bilder);
  }

  $nameform = "<input id=\"name\" type=\"text\" name=\"name\" size=\"18\"".$einzeltag.">";
  $emailform = "<input id=\"email\" type=\"text\" name=\"email\" size=\"18\"".$einzeltag.">";
  $urlform = "<input id=\"url\" type=\"text\" name=\"url\" size=\"18\"".$einzeltag.">";
  if ($userfeld1 != "") $user1form = "<input id=\"user1\" type=\"text\" name=\"user1\" size=\"18\"".$einzeltag.">";
  if ($userfeld2 != "") $user2form = "<input id=\"user2\" type=\"text\" name=\"user2\" size=\"18\"".$einzeltag.">";
  if ($userfeld3 != "") $user3form = "<input id=\"user3\" type=\"text\" name=\"user3\" size=\"18\"".$einzeltag.">";
  $entryform = "<textarea id=\"entry\" name=\"entry\" cols=\"30\" rows=\"10\" onselect=\"storeCaret(this);\" onclick=\"storeCaret(this);\" onkeyup=\"storeCaret(this);\"></textarea>";
  $submitform = "<input type=\"submit\" class=\"submit\" name=\"vpgb_submit\" value=\"eintragen\"".$einzeltag.">";

if ($tpl_art == "extended") $tpl = new ExtendedTemplate();
else $tpl = new Template;

  $tpl->reset();
  $tpl->set('s', 'NAMEFORM',      $nameform);
  $tpl->set('s', 'EMAILFORM',     $emailform);
  $tpl->set('s', 'URLFORM',       $urlform);
  $tpl->set('s', 'USER1FORM',     $user1form);
  $tpl->set('s', 'USER2FORM',     $user2form);
  $tpl->set('s', 'USER3FORM',     $user3form);
  $tpl->set('s', 'IMAGERADIO',    $imageradio);
  $tpl->set('s', 'IMAGESELECT',   $imageselect);
  $tpl->set('s', 'SMILIESFORM',   $smiliesform);
  $tpl->set('s', 'ENTRYFORM',     $entryform);
  $tpl->set('s', 'SUBMITFORM',    $submitform);
  $tpl->set('s', 'BBFETT',        bbcode_link("bold"));
  $tpl->set('s', 'BBKURSIV',      bbcode_link("italic"));
  $tpl->set('s', 'BBUNTERSTRICH', bbcode_link("underline"));
  $tpl->set('s', 'BBURL',         bbcode_link("url"));
  $tpl->set('s', 'BBEMAIL',       bbcode_link("email"));

  ?>
  <script type="text/javascript">
  function submitForm (s) {
    s.disabled = true;

    s.value = "Vielen Dank!";
    return true;
  }
  </script>
  <?php
  echo "<form action=\"front_content.php?idcatart=$idcatart\" method=\"post\" id=\"vpgb_eintrag\" onSubmit=\"return submitForm(this.vpgb_submit)\" accept-charset=\"utf-8\">";
  $tpl->generate('templates/' . $tpl_datei);
  echo "</form>";
}

?>
