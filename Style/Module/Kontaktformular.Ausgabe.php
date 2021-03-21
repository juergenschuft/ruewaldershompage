<?php
/***********************************************
 * CONTENIDO MODUL - OUTPUT
 *
 * Modulname   :     w3concepts.form.v1
 * Author      :     Andreas Kummer
 * Copyright   :     mumprecht & kummer w3concepts
 * Created     :     20-08-2004
 * Modified    :     08-11-2020
 * modified for captcha von Volker Uhlig
 ************************************************/

class w3form {
    var $email, $aufgedroeselt, $form, $formularFeld, $user_Email, $user_From;

    function w3form() {
    }

    // PRIVATE
    function sendEmail() {
        $this->aufdroeseln($this->suppress('sent'));
        $this->generateEmailMessage();
        mail($this->email['adresses'],$this->email['subject'],$this->email['message'],"From: {$this->email['from']['name']} <{$this->email['from']['email']}>\nReply-To: {$this->email['from']['email']}\nX-Mailer: PHP/" . phpversion());
    }

    // PRIVATE
    function aufdroeseln($aufzudroeseln,$prefix = '') {
        foreach ($aufzudroeseln as $key => $value) {
            if (is_array($value)) {
                $this->aufdroeseln($value,$key." ");
            } else {
                $this->aufgedroeselt["{$prefix}{$key}"] = $value;
            }
        }
    }

    // PRIVATE
    function add2Message($key,$value) {
        if (strlen($key) > 25 OR strlen($value) > 54) {
            $this->email['message'] .= "$key\n$value\n";
        } else {
            $this->email['message'] .= $key;
            $this->email['message'] .= str_repeat(' ',25-strlen($key));
            $this->email['message'] .= "$value\n";
        }
    }

    // PRIVATE
    function generateEmailMessage() {
        if ($this->aufgedroeselt) foreach ($this->aufgedroeselt as $key => $value) {

            if ($key != "Submit")
            {
                $this->add2Message($key,$value);
            }
        }
    }

    // PRIVATE
    function suppress() {
        $suppress = func_get_args();
        foreach ($_POST as $key => $value) {
            if (array_search($key,$suppress) === false) $fields[$key] = $value;
        }
        return $fields;
    }



    // PUBLIC
    function addEmailAdress($email) {
        if (empty($this->emailAdresses)) {
            $this->email['adresses'] .= "$email";
        } else {
            $this->email['adresses'] .= ", $email";
        }
    }

    // PUBLIC
    function setEmailSubject($subject) {
        $this->email['subject'] = $subject;
    }

    // PUBLIC
    function setEmailFrom($email,$name) {
        $this->email['from']['email'] = $email;
        $this->email['from']['name'] = $name;
    }

    // PUBLIC
    function setForm($form) {
        $this->form['form'] = $form;
    }

    // PUBLIC
    function setAnswer($answer) {
        $this->form['answer'] = $answer;
    }

    // PUBLIC
    function setBackgroundError($color) {
        $this->form['colorError'] = $color;
    }

    // PUBLIC
    function setClass($class) {
        $this->form['class'] = $class;
    }

    // PRIVATE
    function formularInterpretation($formular) {
        $felder = explode('###',$formular);
        for ($i=1;$i<count($felder);$i=$i+2) {
            $attributte = explode(';',trim($felder[$i]));
            foreach ($attributte as $attribut) {
                $namewert = explode(':',trim($attribut));
                if ($namewert[0] != 'option' && $namewert[0] != 'optionvalue') {
                    $feld["{$felder[$i]}"]["{$namewert[0]}"] = $namewert[1];
                } else {
                    $feld["{$felder[$i]}"]["{$namewert[0]}"][] = $namewert[1];
                }
            }
        }
        return $feld;
    }

    // PRIVATE
    function formularAusgabe($sent = false) {
        echo '<form action="" method="post">';
        echo '<input class="'.$class.'" type="hidden" name="sent" value="true" />';
        $formular = $this->form['form'];
        $formulardaten = $this->formularInterpretation($formular);
        $formular = explode('###',$formular);    //uv suche nach den ### als Trenner
        foreach ($formular as $formularteil) {
            if (!empty($formulardaten["{$formularteil}"])) {
                $this->formularFeld($formulardaten["{$formularteil}"],$sent);  //uv hier wird das Formularfeld ausgegeben
            } else {
                echo $formularteil;
            }
        }
        echo '</form>';
    }

    // PRIVATE
    function formularFeld($attribute,$sent) {
        $parameter = "name=\"{$attribute['name']}\"";
        if ($sent && !$this->formularFeldKorrekt($attribute)) $style ="style=\"background-color:{$this->form['colorError']};\"";
        $class = $this->form['class'];
        switch ($attribute['type']) {
            case 'select':
            case 'password':
            case 'text':
                if (!empty($attribute['size'])) $parameter .= " size=\"{$attribute['size']}\"";
                break;
        }
        switch ($attribute['type']) {
            case 'textarea':
            case 'text':
                if (!empty($attribute['size'])) $parameter .= " size=\"{$attribute['size']}\"";
                if (!empty($attribute['value'])) $value = $attribute['value'];
                if (!empty($_POST["{$attribute['name']}"])) $value = $_POST["{$attribute['name']}"];
                break;
        }
        switch ($attribute['type']) {
            case 'text':
                echo "<input class=\"$class\" type=\"text\" $parameter value=\"$value\" $style />";
                break;
            case 'password':
                echo "<input class=\"$class\" type=\"password\" $parameter value=\"$value\" $style />";
                break;
            case 'textarea':
                echo "<textarea class=\"$class\" name=\"{$attribute['name']}\" cols=\"";
                echo (empty($attribute['cols']))?('20'):($attribute['cols']);
                echo "\" rows=\"";
                echo (empty($attribute['rows']))?('2'):($attribute['rows']);
                echo "\" $style>$value</textarea>";
                break;
            case 'select':
                echo "<select class=\"$class\" $parameter $style>";
                for ($i=0;$i<count($attribute['option']);$i++) {
                    if (!empty($attribute['optionvalue'][$i])) {
                        if (!empty($_POST["{$attribute['name']}"]) && $_POST["{$attribute['name']}"] == $attribute['optionvalue'][$i]) {
                            echo "<option value=\"{$attribute['optionvalue'][$i]}\" selected=\"selected\">{$attribute['option'][$i]}</option>\n";
                        } else {
                            if (empty($_POST["{$attribute['name']}"]) && !empty($attribute['optionvalue'][$i]) && $attribute['optionvalue'][$i] == $attribute['value']) {
                                echo "<option value=\"{$attribute['optionvalue'][$i]}\" selected=\"selected\">{$attribute['option'][$i]}</option>\n";
                            } else {
                                echo "<option value=\"{$attribute['optionvalue'][$i]}\">{$attribute['option'][$i]}</option>\n";
                            }
                        }
                    } else {
                        if (!empty($_POST["{$attribute['name']}"]) && $_POST["{$attribute['name']}"] == $attribute['option'][$i]) {
                            echo "<option selected=\"selected\">{$attribute['option'][$i]}</option>\n";
                        } else {
                            if (empty($_POST["{$attribute['name']}"]) && $attribute['option'][$i] == $attribute['value']) {
                                echo "<option selected=\"selected\">{$attribute['option'][$i]}</option>\n";
                            } else {
                                echo "<option>{$attribute['option'][$i]}</option>\n";
                            }
                        }
                    }
                }
                echo "</select>";
                break;
            case 'checkbox':
                $formularbezeichner = preg_split('[\[|\]]',$attribute['name']);
                if ($sent) {
                    if ($_POST["{$formularbezeichner[0]}"]["{$formularbezeichner[1]}"] == $attribute['value']) {
                        echo "<input class=\"$class\" type=\"checkbox\" $parameter value=\"{$attribute['value']}\" checked=\"checked\"/>";
                    } else {
                        echo "<input class=\"$class\" type=\"checkbox\" $parameter value=\"{$attribute['value']}\"/>";
                    }
                } else {
                    if (!empty($attribute['selected']) && $attribute['selected'] == 'true') {
                        echo "<input class=\"$class\" type=\"checkbox\" $parameter value=\"{$attribute['value']}\" checked=\"checked\"/>";
                    } else {
                        echo "<input class=\"$class\" type=\"checkbox\" $parameter value=\"{$attribute['value']}\"/>";
                    }
                }
                break;
            case 'radio':
                if (!empty($_POST["{$attribute['name']}"])) {
                    if ($_POST["{$attribute['name']}"] == $attribute['value']) {
                        echo "<input class=\"$class\" type=\"radio\" $parameter value=\"{$attribute['value']}\" checked=\"checked\"/>";
                    } else {
                        echo "<input class=\"$class\" type=\"radio\" $parameter value=\"{$attribute['value']}\" />";
                    }
                } else {
                    if (!empty($attribute['selected']) && $attribute['selected'] == 'true') {
                        echo "<input class=\"$class\" type=\"radio\" $parameter value=\"{$attribute['value']}\" checked=\"checked\"/>";
                    } else {
                        echo "<input class=\"$class\" type=\"radio\" $parameter value=\"{$attribute['value']}\"/>";
                    }
                }
                break;
            case 'captcha' :
               echo "<img src=\"verein/gaestebuch/captcha.html?RELOAD=\" alt=\"Captcha\" title=\"Klicken, um das Captcha neu zu laden\" onclick=\"this.src+=1;document.getElementById('captcha_code').value='';\" width=\"140\" height=\"40\">";
               break;  
        }
    }

    // PRIVATE
    function formularVollstaendig() {

        $formular = $this->form['form'];

        $felder = $this->formularInterpretation($formular);

        foreach ($felder as $feld) {
            if (!$this->formularFeldKorrekt($feld)) return false;
        }

        return true;
    }

    // PRIVATE
    function success() {
        $this->sendEmail();    //uv nachdem die Mail versendet wurde, wird die Antwort angezeigt
        echo $this->form['answer'];
    }

    // PRIVATE
    function formularFeldKorrekt($feld) {
        // prüfung des Captcha-Code 
        if ($feld['name'] == 'captcha_code') { 
            if ($_POST['captcha_code'] != $_SESSION['captcha_spam']) {
                return false;
            }
        }           
        
        // prüfung, ob pflichtfeld vorhanden
        if (!empty($feld['mandatory']) && $feld['mandatory'] == 'true' && empty($_POST["{$feld['name']}"])) return false;

        // wenn das formularfeld kein pflichtfeld und nicht vorhanden ist, true zurück geben
        if (empty($_POST["{$feld['name']}"])) return true;

        // regular expression prüfungen
        if (!empty($feld['valid']) && $feld['valid'] == 'simpletext' && !preg_match("^[öäüéàèâêîça-z-]*$/i",$_POST["{$feld['name']}"])) return false;
        if (!empty($feld['valid']) && $feld['valid'] == 'text' && !preg_match("/^[ .,;!?()öäüéàèâêîça-z-]*$/i",$_POST["{$feld['name']}"])) return false;
        if (!empty($feld['valid']) && $feld['valid'] == 'integer' && !preg_match("/^[0-9]*$/i",$_POST["{$feld['name']}"])) return false;
        if (!empty($feld['valid']) && $feld['valid'] == 'float' && !preg_match("/^[0-9]*[.]{0,1}[0-9]*$/i",$_POST["{$feld['name']}"])) return false;
        if (!empty($feld['valid']) && $feld['valid'] == 'date' && !preg_match("/^[0-9]{1,2}.[0-9]{1,2}.[0-9]{2}$/i",$_POST["{$feld['name']}"])) return false;

        if (!empty($feld['valid']) && $feld['valid'] == 'email' && !preg_match("/^[öäüéàèâêîç_a-z0-9-]+(\.[öäüéàèâêîç_a-z0-9-]+)*@[öäüéàèâêîça-z0-9-]+(\.[öäüéàèâêîça-z0-9-]+)*$/i",$_POST["{$feld['name']}"]))
        {
            return false;

        }
        if (!empty($feld['valid']) && $feld['valid'] == 'name' && preg_match("/^[öäüéàèâêîça-z-]*$/i",$_POST["{$feld['name']}"]))
        {
            $this->user_From = $_POST["{$feld['name']}"];
        }

        if (!empty($feld['valid']) && $feld['valid'] == 'email' && preg_match("/^[öäüéàèâêîç_a-z0-9-]+(\.[öäüéàèâêîç_a-z0-9-]+)*@[öäüéàèâêîça-z0-9-]+(\.[öäüéàèâêîça-z0-9-]+)*$/i",$_POST["{$feld['name']}"]))
        {
            $this->user_Email = $_POST["{$feld['name']}"];
        }

        $this->setEmailFrom($this->user_Email,$this->user_From);

        // grössenbereich bei integer und float prüfen
        if (!empty($feld['minvalue']) && $_POST["{$feld['name']}"] < $feld['minvalue']) return false;
        if (!empty($feld['maxvalue']) && $_POST["{$feld['name']}"] > $feld['maxvalue']) return false;

        // längenbereich bei allen typen prüfen
        if (!empty($feld['minlength']) && strlen($_POST["{$feld['name']}"]) < $feld['minlength']) return false;
        if (!empty($feld['maxlength']) && strlen($_POST["{$feld['name']}"]) > $feld['maxlength']) return false;

        return $feld;
    }
    
    //PUBLIC
    function process() {
        session_start(); // session is required by captcha
        if (!isset($_POST['sent'])) {
            $this->formularAusgabe();
        } elseif ($this->formularVollstaendig()) {
            $this->success();
        } else {
            $this->formularAusgabe(true);
        }
    }

}

if ($edit) {
    echo "<p>Hier ist das Formular sowie der Text einzugeben, <br>der zusammen mit dem Formular ausgegeben werden soll:</p>";
    echo "CMS_HTML[3]";
    echo "<p>Hier ist die Ausgabe einzugeben, die erscheint, wenn das Formular erfolgreich prozessiert worden ist:</p>";
    echo "CMS_HTML[4]";
} else {

    $formular = new w3form();
    $formular->addEmailAdress("CMS_VALUE[0]");
    $formular->setEmailSubject("CMS_VALUE[1]");

    $formular->setBackgroundError("CMS_VALUE[4]");
    $formular->setClass("CMS_VALUE[5]");
    $formular->setForm("CMS_HTML[3]");

    $formular->setAnswer("CMS_HTML[4]");
    $formular->process();
}

?>