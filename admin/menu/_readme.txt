Um einen neuen Menupunkt im Adminmenu hinzuzuf�gen, m�sst ihr in diesem Ordner eine Datei erstellen, mit mindestens folgendem Inhalt:

<?php
/////////// ADMINNAVI \\\\\\\\\
// Typ:       settingsmenu
// Rechte:    permission('news')
///////////////////////////////
if(_adminMenu != 'true') exit;

    if(!permission("news"))
    {
      $show = error(_error_wrong_permissions, 1);
    } else {
// CODE
	echo 'Adminmenu';
    }
?>

In Zeile 3 und 4 wird jeweils die Typenkategorie (Adminmenu, Einstellungen, Content) und die ben�tigten Rechte festgelegt, welche dann automatisch ausgelesen werden.

In diesem Beispiel ben�tigt man Newsrechte, damit der Unterpunkt im Bereich "Einstellungen" angezeigt wird.
Je nach Dateiname wird dann auch die Definition f�r die Sprachdatei erstellt, welche dann noch definiert werden muss.


Als "Typ" kann man folgende Bereiche festlegen:

rootmenu	-> "Seitenadmin"
settingsmenu	-> "Einstellungen"
contentmenu	-> "Content"

Als Rechte k�nnen jegliche Rechte abgefragt werden, z.B.:

permission('news')
permission('news') || permission('artikel')
$chkMe == 4



Am besten mal durch die vorhandenen Dateien durchbl�ttern, dann sollte es eigentlich recht einfach zu verstehen sein.