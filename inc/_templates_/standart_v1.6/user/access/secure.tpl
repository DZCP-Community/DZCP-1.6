<tr>
    <td class="contentMainTop" style="vertical-align:middle"><span class="fontBold">{lang msgID="register_confirm"}: </span><span class="fontRed">*</span></td>
    <td class="contentMainFirst" align="left">
        <table class="corner" style="width:204px;border: 1px solid #d4d4d4;">
            <tbody>
                <tr>
                    <td colspan="2" style="width:195"><img class="corner" onmouseover="DZCP.showInfo('{lang msgID="capcha_sound_info"}')" onmouseout="DZCP.hideInfo()" onClick="DZCP.EvalSound('../inc/ajax.php?i=securimage_audio')" src="{idir}/ajax_loading.gif" alt="CAPTCHA Image" name="siimage" hspace="0" vspace="0" id="siimage" style="border: 1px solid #d4d4d4; margin-right: 1px" />
                    <script language="javascript" type="text/javascript">DZCP.initDynCaptcha('siimage',0,0,0,'',0,'{sid}');</script></td>
                </tr>
                <tr>
                    <td><input type="text" name="secure" size="23" maxlength="25"/></td>
                    <td><a tabindex="-1" style="border-style: none;" href="#" title="Refresh Image" onclick="DZCP.initDynCaptcha('siimage',0,0,0,'',0,''); this.blur(); return false"><img src="{idir}/securimage/refresh.png" alt="Reload Image" height="16" width="16" onclick="this.blur()" border="0" /></a></td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>