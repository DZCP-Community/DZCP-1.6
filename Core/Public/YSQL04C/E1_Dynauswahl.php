<?php
/**
 * @author Lucas Brucksch
 * @vertrags-nr: 800427710
 * @date 09.08.2019
 */

/*
 * Ich verwende keine Klartext Umlaute im Code, sondern nur die HTML Zeichencodierung, darum ist der Code etwas anders als in der Vorgabe.
 * Ich verwende hier zusätzlich die Scalar type declarations, diese stehen ab php >= 7 zur verfügung.
 */

/**
 * Ersetzt die HTML Umlaute in einfachen Klartext für "value" inputs.
 * @param string $text
 * @param boolean $reverse
 * @return string
 */
function replaceUML(string $text, bool $reverse = false): string {
    $uml = ['&Auml;','&auml;','&Ouml;','&ouml;','&Uuml;','&uuml;','&szlig;'];
    $replace = ['ae','ae','oe','oe','ue','ue','ss'];
    return ($reverse ? str_ireplace($replace,$uml,$text) : str_ireplace($uml,$replace,$text));
}

/**
 * Erstellt ein
 * @param $id
 * @param $name
 * @param array $options
 * @param bool $multiple
 * @return string
 */
function dynAuswahl(string $id, string $name, array $options, bool $multiple): string {
    $select = "<select name=\"$name\" id=\"$id\"".
        ($multiple ? " multiple=\"multiple\" size=\"5\"" : "").">\n";

    //Loop options
    foreach ($options as $option) {
        $select .= "<option value=\"".replaceUML($option)."\">$option</option>\n";
    }

    $select .= "</select>\n";

    return $select;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta lang="de"/>
    <title>Einsendeaufgabe Nr.1 Dynauswahl</title>
</head>
<body>
<from method="post">
    <p>
        <?php echo dynAuswahl("dynamisch1","auswahl1",["Brot","Butter","Milch","Eier","K&auml;se","Wurst"],false); ?>
    </p>
    <p>
        <?php echo dynAuswahl("dynamisch2","auswahl2",["Schrauben","N&auml;gel","Harken","Nadeln","D&uuml;bel"],true); ?>
    </p>
    <p>
        <input type="submit" value="Abschicken" />
    </p>
</from>
</body>
</html>