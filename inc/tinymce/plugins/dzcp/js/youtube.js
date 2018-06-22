var DZCPDialog = {
    init: function (ed) {
        var f = document.forms[0];
    },

    insert: function () {
        if (document.forms[0].linkSource.value == '') {
            alert(ed.getLang('dzcp.youtube_missing_link'));
            return false;
        }

        tinyMCEPopup.editor.execCommand('mceInsertContent', false, '[youtube]' + document.forms[0].linkSource.value.replace(/^\s+/, '').replace(/\s+$/, '') + '[/youtube]');
        tinyMCEPopup.close();
    }
};

tinyMCEPopup.requireLangPack();
tinyMCEPopup.onInit.add(DZCPDialog.init, DZCPDialog);
