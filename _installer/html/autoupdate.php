<table width="100%" cellpadding="3" cellspacing="1">
    <tr>
        <td class="head">&raquo;Auto-Update für die Datenschutz-Grundverordnung (EU-DSGVO)</td>
    </tr>
    <tr>
        <td align="justify">
            <?php echo ($curl ? '<!--' : '') ?></br><span style='color:red'><b>Die PHP-Erweiterung (cURL) wurde nicht gefunden!<br>
                    PHP-CURL muss installiert sein um den AutoUpdater verwenden zu k&ouml;nnen!</b></span><br /><br /></br><?php echo ($curl ? '-->' : '') ?>
            Wir von DZCP.de m&ouml;chten dir eine M&ouml;glichkeit anbieten, deine EU-DSGVO-Vorlagen automatisch zu aktualisieren.<br><br>
            Hier werden wir einige Daten automatisch auf deinem Webspace ersetzen. <br><br><u>Hier ist eine Auflistung der Daten die ersetzt werden:</u><br><br>
            <b>"/inc/api.php"	 => "Die Schnittstelle zum DZCP.de Server um Aktualisierungen einspielen"</b><br>
            <b>"/inc/lang/languages/dsgvo/*.php"  => "Das sind die Vorlagen der EU-DSGVO, vorliegend in mehreren Sprachen"</b><br><br>
            <b>** Diese Funktion kann nachträglich in der "inc/config.php" aktiviert oder deaktiviert werden **</b><br>
            </div>
        </td>
    </tr>
    <tr>
        <td align="justify">
            <form action="?action=autoupdate&use=0" method="post" />
                <br><br />
                <b><u>** Es handelt sich hier um eine experimentelle Funktion **</b></u><br><br />
                <script>
                    <?php echo (!$curl ? '/*' : '') ?> document.writeln('<input type="button" value="Aktivieren" class="button" ' +
                        'onclick="document.forms[0].action=\'?action=autoupdate&use=1\';document.forms[0].submit()" tabindex="6">');<?php echo (!$curl ? '*/' : '') ?>
                </script>
                <input type="submit" value="Deaktivieren"></td>
            </form>
        </td>
    </tr>
</table>