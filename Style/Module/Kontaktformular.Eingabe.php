/***********************************************
* CONTENIDO MODUL - INPUT
*
* Modulname   :     w3concepts.form.v1
* Author      :     Andreas Kummer
* Copyright   :     mumprecht & kummer w3concepts
* Created     :     20-08-2004
* Modified    :     05.12.2020 uhligv
* Ausgabe der Konfiguration
************************************************/


echo "<table cellspacing=\"0\" cellpadding=\"10\" border=\"0\"><tr valign=\"top\">";

        echo "<tr><td>Zieladresse (alias@mydomain.com):</td>";
        echo "<td><input type=\"text\" name=\"CMS_VAR[0]\" value=\"CMS_VALUE[0]\" size=\"30\" /></td></tr>";

    echo "<tr><td>Betreff:</td>";
        echo "<td><input type=\"text\" name=\"CMS_VAR[1]\" value=\"CMS_VALUE[1]\" size=\"30\" /></td></tr>";

    echo "<tr><td>Antwortadresse (alias@mydomain.com):</td>";
        echo "<td><input type=\"text\" name=\"CMS_VAR[2]\" value=\"CMS_VALUE[2]\" size=\"30\" /></td></tr>";

    echo "<tr><td>Emailname (z.B. Vorname Name):</td>";
        echo "<td><input type=\"text\" name=\"CMS_VAR[3]\" value=\"CMS_VALUE[3]\" size=\"30\" /></td></tr>";

    echo "<tr><td>Hintergrundfarbe bei Fehlern (z.B. red):</td>";
        echo "<td><input type=\"text\" name=\"CMS_VAR[4]\" value=\"CMS_VALUE[4]\" size=\"30\" /></td></tr>";


    echo "<tr><td>Klasse der Inputfelder und der Textareas:</td>";
        echo "<td><input type=\"text\" name=\"CMS_VAR[5]\" value=\"CMS_VALUE[5]\" size=\"30\" /></td></tr>";
        
echo "</table>";
