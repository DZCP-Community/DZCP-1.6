<?php
/**
 * @author Lucas Brucksch
 * @vertrags-nr: 800427710
 * @date 09.08.2019
 */

/**
 * @param int $breite
 * @param int $laenge
 * @param int $preis
 * @param int $provision
 * @param int $mwst
 * @return string
 */
function runCalculate(int $breite, int $laenge, int $preis, int $provision, int $mwst): string {

    return '';
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta lang="de"/>
    <title>Einsendeaufgabe Nr.4 Grundstückspreis Rechner</title>
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
//Create vars
$from_data = ['mwst' =>'', 'breite' => '', 'laenge' => '', 'preis' => '', 'provision' => ''];

//Ausgabe Ergebnisse
if(isset($_POST['button'])) {
    echo "<div class='niceStyle'>";
    $mwst = (isset($_POST['mwst']) ? 19 : 0); //19%
    $from_data['mwst'] = ($mwst != 0);
    $from_data['breite'] = isset($_POST['breite']) ? $_POST['breite'] : '';
    $from_data['laenge'] = isset($_POST['laenge']) ? $_POST['laenge'] : '';
    $from_data['preis'] = isset($_POST['preis']) ? $_POST['preis'] : '';
    $from_data['provision'] = isset($_POST['provision']) ? $_POST['provision'] : '';
    echo runCalculate(
            intval($from_data['breite']),
            intval($from_data['laenge']),
            intval($from_data['preis']),
            intval($from_data['provision']),
            $mwst);
    echo "</div>";
}
?>
<div class='niceStyle' style="padding-top: 20px;">
    <form id="rechner" name="rechner" method="post" action="">
        <label>Breite in Metern:
            <input required="required" name="breite" type="text" id="breite" placeholder="Breite" value="<?php echo $from_data['breite']; ?>" size="30" />
        </label>
        <p>
            <label>Länge in Metern:
                <input required="required" name="laenge" type="text" id="laenge" placeholder="Länge" value="<?php echo $from_data['laenge']; ?>" size="30" />
            </label>
        </p>
        <p>
            <label>
                Preis pro m²:
                <input required="required" type="text" name="preis" id="preis" placeholder="Preis / m²" value="<?php echo $from_data['preis']; ?>"/>
            </label>
        </p>
        <p>
            <label>
                Provision:
                <select required="required" name="provision" id="select">
                    <option <?php echo ($from_data['provision'] <= 3 ? 'selected="selected"' : ''); ?> value="3">3%</option>
                    <option <?php echo ($from_data['provision'] == 4 ? 'selected="selected"' : ''); ?> value="4">4%</option>
                    <option <?php echo ($from_data['provision'] == 5 ? 'selected="selected"' : ''); ?> value="5">5%</option>
                    <option <?php echo ($from_data['provision'] == 6 ? 'selected="selected"' : ''); ?> value="6">6%</option>
                    <option <?php echo ($from_data['provision'] >= 7 ? 'selected="selected"' : ''); ?> value="7">7%</option>
                </select>
            </label>
        </p>
        <p>
            <label>
                <input <?php echo ($from_data['mwst'] ? 'checked="checked"' : ''); ?> name="mwst" type="checkbox" id="mwst" value="1" />
            </label>
            Mehrwertsteuer 19%</p>
        <p>
            <label>
                <input type="submit" name="button" id="button" value="Berechnen" />
            </label>
        </p>
    </form>
</div>
</body>
</html>