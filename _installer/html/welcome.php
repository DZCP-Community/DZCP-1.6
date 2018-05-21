<table width="100%" cellpadding="3" cellspacing="1">
  <tr>
    <td class="head">&raquo; Willkommen</td>
  </tr>
  <tr>
    <td></td>
  </tr>
  <tr>
    <td>
    <div align="justify">
      Vielen Dank, das Sie sich f&uuml;r deV!L`z Clanportal entschieden haben. Die nachfolgenden Stationen werden Sie durch die Installation von deV!L`z Clanportal navigieren.
      Das Installationsscript ist so aufgebaut, das man nur Punkt f&uuml;r Punkt alles abarbeiten muss. Sollte ein Fehler vorliegen wird Ihnen dieser sofort mitgeteilt.<br /><br />
      Viel Spass mit deV!L`z Clanportal w&uuml;nscht Ihnen das gesamte Team von DZCP.de.<br /><br />
    </div>
        <span class="head">&raquo; Lizenzbestimmungen:</span>
    </td>
  </tr>
  <tr>
    <td align="justify">
<form action="install.php?action=prepare&agb=false" method="post">
<textarea name="lizenz" style="width:100%;height:400px;overflow:auto" readonly>
<?php echo utf8_encode(file_get_contents("conf/lizenz.txt")); ?>
</textarea><br /><br />
<script>
   document.writeln('<input type="button" value="Ich bin mit den Lizenzbestimmungen einverstanden" class="button" onclick="document.forms[0].action=\'install.php?action=require\';document.forms[0].submit()" tabindex="6">');
   </script></td>
      </form>
    </td>
  </tr>
</table>