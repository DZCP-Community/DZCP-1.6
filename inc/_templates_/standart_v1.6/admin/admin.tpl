<div style="margin-top:20px;"></div>
<div class="row">
  <div class="col">
    <table class="hperc mainContent" cellspacing="1">
      <tr>
        <td>
          <table class="hperc" cellspacing="0">
            <tr>
              <td class="adminBarOuter contentHead" style="width:100%;padding:10px;text-align:left;">{$version}</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <div style="position:relative">
            <script language="javascript" type="text/javascript" src="../inc/_templates_/standart_v1.6/_js/admin.js"></script>
            <script language="javascript" type="text/javascript">
              var ITEMS = [
                {$radmin1}
                ['{lang msgID="rootadmin"}',null,null,
                  {$rootmenu}
                ],
                {$radmin2}
                {$adminc1}
                ['{lang msgID="config_einst"}',null,null,
                  {$settingsmenu}
                ],
                {$adminc2}
                {$cdminc1}
                ['{lang msgID="content"}',null,null,
                  {$contentmenu}
                ],
                {$cdminc2}
                {$template1}
                ['{lang msgID="template"}',null,null,
                  {$templatemenu}
                ],
                {$template2}
                {$addons1}
                ['{lang msgID="addons"}',null,null,
                  {$addonsmenu}
                ]
                {$addons2}
              ];
              new menu (ITEMS, POS);
            </script>
            <noscript><p style="text-align:center">You have to enable Javascript for using the admin menu</p></noscript>
          </div>
        </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr>
        <td>
          <div id="admContent" style="padding-top:20px;padding-bottom:20px">
            <table class="hperc" cellspacing="0" >
              <tr style="height: 0%">
                <td>{$notification}</td>
              </tr>
            </table>
            <table class="mainContent" cellspacing="1">
              {$show}
            </table>
          </div>
        </td>
      </tr>
      <tr>
        <td style="height:20px"></td>
      </tr>
    </table>
  </div>
</div>