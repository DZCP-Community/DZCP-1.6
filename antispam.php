<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

ob_start();
session_start();

$error = true;
if (isset($_GET['secure']) && !empty($_GET['secure'])) {
    $nonajax = isset($_GET['nonajax']) && $_GET['secure'] == 'shout';

## COLORS
    $backgroundColor = '#444444';
    $textColor = '#000000';
    $noiseColor = '#AAAAAA';
    $lineColor = '#555555';
## /COLORS

    if (function_exists('gd_info')) {
        if (isset($_GET['num']) && $_GET['num'] != 0 && $_GET['num'] >= 2 && !empty($_GET['num'])) {
            $num = ((int)$_GET['num']);
            $x = 100;
            $y = 30;
            $space = 10;
        } else {
            $num = 2;
            $x = 40;
            $y = 23;
            $space = 6;
        }

        $sizeMin = 13;
        $sizeMax = 19;
        $rectMin = -20;
        $rectMax = 20;

        function hex2rgb($color, $type)
        {
            $r = '';
            if ($type == 'r') $r = hexdec(substr($color, 1, 2));
            elseif ($type == 'g') $r = hexdec(substr($color, 3, 2));
            elseif ($type == 'b') $r = hexdec(substr($color, 5, 2));

            return $r;
        }

        $im = imagecreate($x, $y);

        $backgroundColor = imagecolorallocate($im, hex2rgb($backgroundColor, 'r'), hex2rgb($backgroundColor, 'g'), hex2rgb($backgroundColor, 'b'));
        imagecolortransparent($im, $backgroundColor);
        $noiseColor = imagecolorallocate($im, hex2rgb($noiseColor, 'r'), hex2rgb($noiseColor, 'g'), hex2rgb($noiseColor, 'b'));
        $lineColor = imagecolorallocate($im, hex2rgb($lineColor, 'r'), hex2rgb($lineColor, 'g'), hex2rgb($lineColor, 'b'));

// Pixel einf�gen
        if (function_exists('imagesetpixel')) {
            $noise = $x * $y / 10;
            for ($i = 0; $i < $noise; $i++)
                imagesetpixel($im, mt_rand(0, $x), mt_rand(0, $y), $noiseColor);
        }

// Linien zeichnen
        if (function_exists('imagesetpixel')) imagesetthickness($im, 1);
        if (function_exists('imageline')) {
            $anz = mt_rand(4, 9);
            for ($i = 1; $i <= $anz; $i++)
                imageline($im, mt_rand(0, $x), mt_rand(0, $y), $x - mt_rand(0, 0), mt_rand(0, $y), $lineColor);
        }

// Zahlencode einfuegen
        $code = '';
        $passwordComponents = array("ABCDEFGHIJKLMNOPQRSTUVWXYZ", "0123456789", "#$@!?&%+");
        $componentsCount = count($passwordComponents);
        for ($pos = 0; $pos < (int)$_GET['num']; $pos++) {
            $componentIndex = ($pos % $componentsCount);
            $componentLength = strlen($passwordComponents[$componentIndex]);
            $random = rand(0, $componentLength - 1);
            $w = (16 * $pos) + $space;

            if (function_exists('imagettftext'))
                imagettftext($im, rand($sizeMin, $sizeMax), rand($rectMin, $rectMax), $w, 20,
                    imagecolorallocate($im,
                        hex2rgb($textColor, 'r'),
                        hex2rgb($textColor, 'g'),
                        hex2rgb($textColor, 'b')), "./inc/images/fonts/verdana.ttf",
                    $passwordComponents[$componentIndex][$random]);

            $code .= $passwordComponents[$componentIndex][$random];
        }
        unset($passwordComponents);

        //Code in Session abspeichern
        $_SESSION["sec_" . strval($_GET['secure'])] = $code;

        if (!function_exists('imagettftext')) {
            for ($i = 0; $i <= strlen($code); $i++) $strcode .= $code[$i] . ' ';
            $text_color = imagecolorallocate($im, hex2rgb($textColor, 'r'), hex2rgb($textColor, 'g'), hex2rgb($textColor, 'b'));
            imagestring($im, 12, $x / 10, $y / 4, $strcode, $text_color);
        }

//Bild ausgeben & bildcache zerstoeren
        if (imagegif($im))
            $error = false;

        imagedestroy($im);
    } else {
        echo '<a href="http://www.libgd.org" target="_blank">GDLib</a> is not installed!';
        $error = true;
    }
} else
    echo 'Parameter Error!';

$imgData = ob_get_contents();
ob_end_clean();

ob_start();
if (!$error && !$nonajax) {
    $imgData = base64_encode($imgData);
    $src = 'data:image/gif;base64,' . $imgData;
    echo '<img class="icon" src="' . $src . '">';
} else {
    if ($nonajax && !$error)
        header("Content-type: image/gif");

    echo $imgData;
}
ob_end_flush();