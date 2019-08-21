<?php
/**
 * @author Lucas Brucksch
 * @vertrags-nr: 800427710
 * @date 09.08.2019
 */

function eineFunktion(&$param)
{
    $param = $param * 2;
}

function nochEineFunktion($param)
{
    $param = $param / 2;
}

$var1 = 10;
$var2 = 20;
$var3 = 30;
$var4 = 0;
$var4 = &$var1;

/**
 * Die Variable $var4 wird erst als 0 initialisiert (Zeile 21)
 * anschließend wird die Variable $var1 per Referenz an $var4 übergeben. (Zeile 22)
 * Die Variable $var4 und $var1 sind jetzt (int)10
 */
echo "var4: $var4<br />"; // Ausgabe: 10

/**
 * Diese funktion setzt ebenfalls eine Referenz aber über ein Objekt ein.
 * Inerhalb der funktion wird $param durch 2 multipliziert und an $var4 und $var1 zurückgegeben.
 * (Referenz nach $var1 an Zeile 22)
 * Das Ergebniss ist (10 * 2) also $var4 und $var1 ist 20
 */
eineFunktion($var4);
echo "var1: $var1<br />"; // Ausgabe: 20

/**
 * $var4 ist nicht größer als $var2
 */
if ($var4 > $var2) {
    $var4 = $var2;
} else {
    /**
     * Referenz von $var3 auf $var4, $var4 entspricht jetzt 30
     */
    $var4 = &$var3;
}

/**
 * Diese funktion verändert keine Werte,
 * es wird nur eine division ausgeführt aber nichts verändert oder zurückgegeben.
 */
nochEineFunktion($var4);

/**
 * Nur das Echo für Zeile 49
 */
echo "var4: $var4<br />"; // Ausgabe: 30

/**
 * Diese funktion setzt eine Referenz über ein Objekt.
 * (Referenz nach $var3)
 * Das Ergebniss ist (30 * 2) also $var4 und $var3 ist 60
 */
eineFunktion($var3);

/**
 * $var1 ist immer noch 20, es gab ab Zeile 37 keine weiteren Änderungen.
 */
echo "var1: $var1<br />"; // Ausgabe: 20

/**
 * $var2 wurde bis jetzt nicht verändert, orginal wert von zeile 19
 */
echo "var2: $var2<br />"; // Ausgabe: 20
