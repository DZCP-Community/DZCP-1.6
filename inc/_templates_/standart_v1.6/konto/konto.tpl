<div style="margin-top:20px;"></div>
{$notification_page}
<div class="row">
    <div class="col">
        <table class="mainContent" cellspacing="1">
            <tr>
                <td class="contentMainTop" align="center" colspan="7">{$seiten}</td>
            </tr>
            <tr>
                <td class="contentHead" align="center" colspan="7"><span class="fontBold">{lang msgID="konto_head"}</span></td>
            </tr>
            <tr>
                <td class="contentMainTop" align="center" width="15%"><span class="fontBold">{lang msgID="konto_datum"}<a
                                href="{$order_datum}"><img src="../inc/images/order.gif" alt="" class="icon"/></a></span></td>
                <td class="contentMainTop" align="center" width="15%"><span class="fontBold">{lang msgID="konto_action"}&nbsp;<a
                                href="{$order_action}"><img src="../inc/images/order.gif" alt="" class="icon"/></a></span></td>
                <td class="contentMainTop" align="center" width="20%"><span class="fontBold">{lang msgID="konto_transid"}&nbsp;<a
                                href="{$order_transid}"><img src="../inc/images/order.gif" alt="" class="icon"/></a></span></td>
                <td class="contentMainTop" align="center" width="10%"><span class="fontBold">{lang msgID="konto_to"}&nbsp;<a
                                href="{$order_to}"><img src="../inc/images/order.gif" alt="" class="icon"/></a></span></td>
                <td class="contentMainTop" align="center" width="10%"><span class="fontBold">{lang msgID="konto_from"}&nbsp;<a
                                href="{$order_from}"><img src="../inc/images/order.gif" alt="" class="icon"/></a></span></td>
                <td class="contentMainTop" align="center" width="15%"><span class="fontBold">{lang msgID="konto_balance"}&nbsp;<a
                                href="{$order_balance}"><img src="../inc/images/order.gif" alt="" class="icon"/></a></span></td>
                <td class="contentMainTop" align="center" width="1%"></td>
            </tr>
            {$show}
            <tr>
                <td class="contentHead" align="center" colspan="7"></td>
            </tr>
            <tr>
                <td class="contentBottom" colspan="7"><span class="fontBold">{lang msgID="konto_summe"}: {$summe} {lang msgID="konto_coins"}</span></td>
            </tr>
        </table>
    </div>
</div>
