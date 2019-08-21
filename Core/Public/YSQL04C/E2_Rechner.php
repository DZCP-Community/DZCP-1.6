<?php
/**
 * @author Lucas Brucksch
 * @vertrags-nr: 800427710
 * @date 09.08.2019
 */

/**
 * Eine Runde rechnen?!
 * @param int $zahl_1
 * @param int $zahl_2
 * @param string $calculationType
 * @return string
 */
function runCalculator(int $zahl_1, int $zahl_2, string $calculationType): string {
    switch ($calculationType) {
        case 'addition':
            $calculation = ($zahl_1 + $zahl_2);
            return strval($zahl_1)." + ".strval($zahl_2)." = ".strval($calculation);
            break;
        case 'subtraktion':
            $calculation = ($zahl_1 - $zahl_2);
            return strval($zahl_1)." - ".strval($zahl_2)." = ".strval($calculation);
            break;
        case 'multiplikation':
            $calculation = ($zahl_1 * $zahl_2);
            return strval($zahl_1)." * ".strval($zahl_2)." = ".strval($calculation);
            break;
        case 'division':
            $calculation = ($zahl_1 / $zahl_2);
            return strval($zahl_1)." / ".strval($zahl_2)." = ".strval($calculation);
            break;
    }

    return "Error";
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta lang="de"/>
    <title>Einsendeaufgabe Nr.2 Rechner</title>
</head>
<style>
    .niceStyle {
        margin-bottom: 15px;
        background-color: darkgray;
        padding-left: 15px;
        padding-right: 15px;
        max-width: 350px;
        border-width: 2px;
        border-color: black;
        border-style: dotted;
        border-radius: 6px;
    }
    h4 {
        margin-block-start: 0.33em;
    }
</style>
<body>
<?php
//Ausgabe Ergebnisse
if(isset($_POST['button'])) {
    echo "<div class='niceStyle'><h3>";
    echo runCalculator(intval($_POST['zahl_1']),intval($_POST['zahl_2']),$_POST['calculationType']);
    echo "</h3></div>";
}
?>
<div class='niceStyle' style="padding-top: 20px;">
    <form id="rechner" name="rechner" method="post" action="">
        <h4>Bitte geben Sie die beiden Zahlen in die Felder ein.</h4>
        <label>Zahl 1
            <input required="required" name="zahl_1" type="text" id="zahl_1" placeholder="Bitte die erste Zahl eingeben" size="30" />
        </label>
        <p>
            <label>Zahl 2
                <input required="required" name="zahl_2" type="text" id="zahl_2" placeholder="Bitte die zweite Zahl eingeben" size="30" />
            </label>
        </p>
        <table width="200" border="0">
            <tr>
                <td><label><input required="required" type="radio" name="calculationType" value="addition" id="calculationType_0" />+ ( Addition )</label></td>
            </tr>
            <tr>
                <td><label><input required="required" type="radio" name="calculationType" value="subtraktion" id="calculationType_1" />- ( Subtraktion )</label></td>
            </tr>
            <tr>
                <td><label><input required="required" type="radio" name="calculationType" value="multiplikation" id="calculationType_2" />* ( Multiplikation )</label></td>
            </tr>
            <tr>
                <td><label><input required="required" type="radio" name="calculationType" value="division" id="calculationType_3" />/ ( Division )</label></td>
            </tr>
        </table>
        <p>
            <label>
                <input type="submit" name="button" id="button" value="Rechnen" />
            </label>
        </p>
    </form>
</div>
</body>
</html>