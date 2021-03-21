<?php
    /**
     * das captcha ist
     * abgemalt bei:    http://www-coding.de/so-gehts-eigenes-captcha-mit-php/
     */
 
    session_start(); 
    unset($_SESSION['captcha_spam']);
   
    $fileDir            = 'upload/captcha/';                        // im ruewalder-cms angelegtes dir fuer captcha-kram
    
    // Variablen (k�nnen angepasst werden) //
    $captcha_bg_img     = $fileDir.'bg_captcha.png';                // Pfad zum Hintergrundbild
    $captcha_over_img   = $fileDir.'bg_captcha_over.png';           // Pfad zum Bild, was �ber das Captcha gelegt wird
    $font_file          = $fileDir.'/railway-webfont.ttf';          // Pfad zur Schriftdatei
    $font_size          = 25;                                       // Schriftgr��e
    $text_angle         = mt_rand(0, 5);                            // Schriftwinkel (Werte zwischen 0 und 5)
    $text_x             = mt_rand(0, 18);                           // X-Position (Werte zwischen 0 und 18)
    $text_y             = 35;                                       // Y-Position
    $text_chars         = 5;                                        // L�nge des Textes
    $text_color         = array(0, 0 , 0);                          // Textfarbe (R, G, B)
    
    // Funktion um zuf�lligen String zu generieren //
    function rand_string($length=5) {
        $letters = array_merge(range('A', 'H'), range('J', 'N'), range('P', 'Z'), range(2, 9)); // Verwendet keine 0, 1, I, O da sich diese �hnlich sehen
        $lettersCount = count($letters) - 1;        
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $letters[mt_rand(0, $lettersCount)];
        }

        return $result;
    }

    // Zuf�lligen Text generieren und in der Session speichern //
    $text = rand_string($text_chars);
    $_SESSION['captcha_spam'] = $text;
    
    // Header: Mitteilen, dass es sich um ein Bild handelt und dass dieses nicht im Cache gespeichert werden soll //
    header('Expires: Mon, 26 Jul 1990 05:00:00 GMT');
    header("Last-Modified: ".date("D, d M Y H:i:s")." GMT");
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    //header('Content-type: text/html');
    header('Content-type: image/png');
    
    // Captcha Bild erstellen, Text schreiben & Bild dar�ber legen //
    $img = ImageCreateFromPNG($captcha_bg_img);
    $text_color = ImageColorAllocate($img, $text_color[0], $text_color[1], $text_color[2]);
    imagettftext($img, $font_size, $text_angle, $text_x, $text_y, $text_color, $font_file, $text);
    imagecopy($img, ImageCreateFromPNG($captcha_over_img), 0, 0, 0, 0, 140, 40);
    
    // Ausgabe und L�schen des Bildes //
    imagepng($img); 
    imagedestroy($img); 
?>
