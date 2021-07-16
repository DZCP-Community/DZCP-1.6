<div style="margin-top:20px;"></div>
<div class="row">
    <div class="col">
        <table class="hperc" cellspacing="10">
            <tr>
                <td>
                    <table class="hperc" cellspacing="0">
                        <tr>
                            <td class="contentHead" colspan="2"><span class="fontBold">{lang msgID="downloads_top"}</span></td>
                        </tr>
                        <tr>
                            <td width="50%">
                                {$dl_top}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="hperc" cellspacing="0" style="margin-bottom: 15px;margin-top: 15px;">
                        <tr>
                            <td class="contentHead"><span class="fontBold">{lang msgID="downloads_most"}</span></td>
                            <td class="contentMainFirst" style="width: 31px;"></td>
                            <td class="contentHead"><span class="fontBold">{lang msgID="downloads_new"}</span></td>
                        </tr>
                        <tr>
                            <td class="contentMainFirst" style="vertical-align:top;">
                                <div class="card position-relative shadow">
                                    {$top_dl}
                                </div>
                            </td>
                            <td class="contentMainFirst"></td>
                            <td class="contentMainFirst" style="vertical-align:top;">
                                <div class="card position-relative shadow">
                                    {$new_dl}
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-4">
        <table class="hperc" cellspacing="0">
            <tr>
                <td class="contentHead" colspan="2"><span class="fontBold">{lang msgID="downloads_kats"}</span></td>
            </tr>
            <tr>
                <td>
                    <div style="margin-top: 4px;">
                        <div class="card position-relative shadow">
                            {$kats}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>