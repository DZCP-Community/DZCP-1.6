/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

// GLOBAL VARS
var doc = document, ie4 = document.all, opera = window.opera;
var innerLayer, layer, x, y, offsetX = 15, offsetY = 5;
var jQueryV = "3.2.1", $ = jQuery;
var dzcp_config = JSON&&JSON.parse(json)|| $.parseJSON(json);

function changeme(that) {document.location.href="index.php?kat=" + that.value;}

/*
 * CKEditor - WYSIWYG Options [bbcode]
 */
var config_ckeditor_bbcode_only = {
    toolbar: [
        ['Cut','PasteFromWord','Undo','Redo','-','RemoveFormat'],
        ['Bold','Italic','Underline','Strike'],
        ['NumberedList','BulletedList','-','Blockquote'],
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
        ['Link','Unlink','Image','FontSize','TextColor'],
        ['Maximize','SpellChecker', 'Scayt','BGColor', '-','Youtube','Smileys'],
    ],

    filebrowserBrowseUrl:'/inc/ajax.php?i=fileman&run',
    filebrowserImageBrowseUrl:'/inc/ajax.php?i=fileman&run&type=image',
    extraPlugins: 'bbcode,font,colorbutton,youtube', //smileys
    removePlugins: 'format,horizontalrule,pastetext,pastefromword,scayt,showborders,stylescombo,table,tabletools,tableselection,wsc,easyimage,cloudservices',
    removeButtons: 'Anchor,BGColor,Font,Strike,Subscript,Superscript',
    removeDialogTabs: 'link:upload;image:upload',
    fontSize_sizes: "80/80%;100/100%;120/120%;150/150%;200/200%;300/300%;400/400%;500/500%",
    disableObjectResizing: true,
    language: dzcp_config.lng,
};

// DZCP JAVASCRIPT LIBARY FOR JQUERY >= V3.2.1
var DZCP = {
    jQueryCheck: function(error) {
        if (typeof jQuery !== 'undefined' && $().jquery >= jQueryV) {
            return true;
        } else { if(error){ alert("Update your jQuery Version to >= "+jQueryV); } return false; }
    },

    //init
    init: function() {
        DZCP.DebugLogger('Initiation DZCP-Libary');

        doc.body.id = 'dzcp-engine';
        DZCP.DebugLogger("jQuery Version: " + $().jquery + " is loaded!");
        DZCP.DebugLogger('Load DZCP-Engine V1.8.0');

        $('body').append('<div id="dialog"></div><div id="infoDiv"></div>');
        layer = $('#infoDiv')[0];
        doc.body.onmousemove = this.trackMouse;

        // init lightbox
        DZCP.initLightbox();

        // init jquery-ui
        DZCP.initJQueryUI();

        //init ckeditor
        DZCP.initCKEditor();

        // init colorpicker
        $("#colorpicker").colorpicker();

        // init Auto-Refresh
        if (dzcp_config.autoRefresh) {
            DZCP.initAutoRefresh();
        }

        DZCP.Barrating(false);

        //Conjob
        //var request = $.ajax({ url: "../inc/ajax.php?i=conjob"});
       // request.done();

        DZCP.initCodeHighlighting();

        //Bootstrap Pagination
        DZCP.Pagination();

        //Bootstrap Auto-Hiding Navbar
        $("nav.navbar.fixed-top").autoHidingNavbar();

        $("#content-slider").lightSlider({
            loop:true,
            keyPress:true
        });

        $('#image-gallery').lightSlider({
            gallery:true,
            item:1,
            thumbItem:9,
            slideMargin: 0,
            speed:500,
            auto:true,
            loop:true,
            onSliderLoad: function() {
                $('#image-gallery').removeClass('cS-hidden');
            }
        });
    },

    // init bar rating
    Barrating: function(userstyle) {
        if ($(".bar-rating").length) {
            var theme = dzcp_config.rating_by_user || userstyle ? 'bootstrap-stars-user' : 'bootstrap-stars';
            $(".bar-rating").barrating('show', {
                theme: theme,
                readonly: dzcp_config.rating_readonly,
                onSelect: function(value, text, event) {
                    if (typeof(event) !== 'undefined') {
                        var url = "../inc/ajax.php?i=rating&page=tutorials&rating="+value+"&id="+DZCP.getUrlParameters('id',true);
                        DZCP.DebugLogger('Rating by User: \'' + value + '\' / URL: \'' + url + '\'');
                        var request = $.ajax({ url: url});
                        request.done(function(msg) {
                            msg = '<select class="bar-rating">'+msg+'</select>';
                            $('#barrating').html( msg );
                            DZCP.Barrating(true);
                        });
                    }
                }
            });
        }
    },

    Pagination: function() {
        $.each(dzcp_config.pagination, function( id, data ) {
            window.pagObj = $('#'+id).twbsPagination({
                totalPages: data.total_pages,
                visiblePages: data.visible_pages,
                startPage: data.currentPage,
                first: data.first,
                next: data.next,
                prev: data.previous,
                last: data.last,
                onPageClick: function (event, page) {
                    if(data.currentPage != page) {
                        $(window).attr('location',data.url + page)
                    }
                   // console.info(page + ' (from options)');
                }
            }).on('page', function (event, page) {
                //console.info(page + ' (from event listening)');
            });
        });
    },

    // init jquery-ui
    initJQueryUI: function() {
        //$(".tabs").tabs(" > .switchs", { effect: 'fade' }); //Fucked CODE!!! Replace !!!!

        $(".nav" ).button({ text: true });
        $( "#rerun" ).button().click(function() { return false; }).next().button({ text: false, icons: { primary: "ui-icon-triangle-1-s" } }).click(function() {
            var menu = $( this ).parent().next().show().position({ my: "left top", at: "left bottom", of: this });
            $( document ).one( "click", function() { menu.hide(); });
            return false;
        }).parent().buttonset().next().hide().menu();

        // $("[title]").tooltip({ track: true, delay: 2, fade: 250 });
        $("#dialog").dialog({ modal: true, bgiframe: true, width: 'auto', height: 'auto', autoOpen: false, title: 'Info' });

        $("#newskat").change(function () {
            var id = this.value;
            location.href = "index.php?kat="+id;
        });

        $("#dialog-confirm").hide();
        if(dzcp_config.dsgvo == 1) {
            $("#dialog-confirm").show();
            $("#dialog-confirm").dialog({
                resizable: false,
                height: "auto",
                width: 800,
                modal: true,
                buttons: {
                    "Akzeptieren": function () {
                        var url = "?dsgvo=1";
                        $(location).attr('href',url);
                    },
                    "Ablehnen": function () {
                        var url = "?dsgvo=0";
                        $(location).attr('href',url);
                    }
                }
            });
        }

        DZCP.UpdateJQueryUI();
    },

    // init jquery-ui
    dsgvo: function() {
        $("#dialog-confirm").show();
        $("#dialog-confirm").dialog({
            resizable: false,
            height: "auto",
            width: 800,
            modal: true,
            buttons: {
                "Akzeptieren": function () {
                    var url = "?dsgvo=1";
                    $(location).attr('href',url);
                },
                "Ablehnen": function () {
                    var url = "?dsgvo=0";
                    $(location).attr('href',url);
                }
            }
        });
    },

    //CKEditor - WYSIWYG
    initCKEditor: function() {
        var basePath = document.location.origin+'/inc/_templates_/standart_v1.6/_js/ckeditor';
        CKEDITOR.plugins.addExternal('bbcode',basePath+'/bbcode/', 'plugin.js');
        CKEDITOR.plugins.addExternal('smileys',basePath+'/smileys/', 'plugin.js');
        CKEDITOR.plugins.addExternal('youtube',basePath+'/youtube/', 'plugin.js');

        $(".editorStyleWord").ckeditor(config_ckeditor_bbcode_only);
        $(".editorStyle").ckeditor(config_ckeditor_bbcode_only);
    },

    // update jquery-ui
    UpdateJQueryUI: function() {
        $("input[type=submit]" ).button().click(function( ) { $(this).find("form").submit(); });
        $("input[type=button]" ).button().click(function( ) { $(this).find("form").submit(); });
        $("a.confirm").click(function(link) {
            link.preventDefault();
            var default_message_for_dialog = ''
            var theHREF = $(this).attr("href");
            var theREL = $(this).attr("rel");
            var theMESSAGE = (theREL == undefined || theREL == '') ? default_message_for_dialog : theREL;
            var theICON = '<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 0 0;"></span>';

            var btns = {};
            btns[decodeURIComponent(dzcp_config.dialog_button_00)] = function() { window.location.href = theHREF; };
            btns[decodeURIComponent(dzcp_config.dialog_button_01)] = function() { $(this).dialog("close"); };

            // set windows content
            $('#dialog').html('<P>' + theICON + theMESSAGE + '</P>');
            $("#dialog").dialog('option', 'buttons', btns);
            $('#dialog').dialog('option', 'position', ['center', (document.body.clientHeight / 3)]);
            $("#dialog").dialog("open");
        });
    },

    // init Auto-Refresh
    initAutoRefresh: function() {
        if(!DZCP.jQueryCheck(false)) return false;
        DZCP.DebugLogger('Initiation Auto-Refresh');
    },

    // init code highlighting
    initCodeHighlighting: function() {
        if(!DZCP.jQueryCheck(false)) return false;
        DZCP.DebugLogger('Initiation Code Highlighting');
        $('pre code').each(function(i, block) {
            hljs.configure({ tabReplace: '    '});
            hljs.highlightBlock(block);
        });
    },

    // init lightbox
    initLightbox: function() {
        /*
        if(!DZCP.jQueryCheck(false)) return false;
        DZCP.DebugLogger('Initiation Lightbox');
        $('a[rel^=lightbox]').magnificPopup({
            type:'image',
            gallery:{enabled:true},
            mainClass: 'mfp-with-zoom',
            zoom: {
                enabled: true,
                duration: 300,
                easing: 'ease-in-out',
                opener: function(openerElement) {
                    return openerElement.is('img') ? openerElement : openerElement.find('img');
                }
            }
        });*/
    },

    // handle events
    addEvent : function(obj, evType, fn) {
        if(!DZCP.jQueryCheck(false)) return false;
        if(obj.addEventListener)
        {
            obj.addEventListener(evType, fn, false);
            return true;
        } else if (obj.attachEvent) {
            var r = obj.attachEvent('on' + evType, fn);
            return r;
        } else return false;
    },

    // track mouse
    trackMouse: function(e) {
        if(!DZCP.jQueryCheck(false)) return false;
        innerLayer = $('#infoInnerLayer')[0];
        if(typeof(layer) === 'object') {
            var ie4 = doc.all;
            var ns6 = doc.getElementById && !doc.all;
            var mLeft = 5;
            var mTop = -15;

            x = (ns6) ? e.pageX-mLeft : window.event.clientX+doc.documentElement.scrollLeft - mLeft;
            y = (ns6) ? e.pageY-mTop  : window.event.clientY+doc.documentElement.scrollTop  - mTop;

            if(innerLayer) {
                var layerW = ((ie4) ? innerLayer.offsetWidth : innerLayer.clientWidth) - 3;
            } else {
                var layerW = ((ie4) ? layer.clientWidth : layer.offsetWidth) - 3;
            }
            var winW   = (ns6) ? (window.innerWidth) + window.pageXOffset - 12
                : doc.documentElement.clientWidth + doc.documentElement.scrollLeft;

            layer.style.left = ((x + offsetX + layerW >= winW - offsetX) ? x - (layerW + offsetX) : x + offsetX) + 'px';
            layer.style.top  = (y + offsetY) + 'px';
        }
        return true;
    },

    // handle popups
    popup: function(url, x, y) {
        x = parseInt(x); y = parseInt(y) + 50;
        popup = window.open(url, 'Popup', "width=1,height=1,location=0,scrollbars=0,resizable=1,status=0");
        popup.resizeTo(x, y);
        popup.moveTo((screen.width - x) / 2, (screen.height-y) / 2);
        popup.focus();
    },

    // init Ajax DynLoader
    initDynLoader: function(tag,menu,options,fade) {
        if(!DZCP.jQueryCheck(false)) return false;
        DZCP.DebugLogger('DynLoader -> Tag: \'' + tag + '\' / URL: \'' + "../inc/ajax.php?i=" + menu + options + '\'');
        var request = $.ajax({ url: "../inc/ajax.php?i=" + menu + options });
        request.done(function(msg) { if(fade) { $('#' + tag).html( msg ).hide().fadeIn("normal"); DZCP.UpdateJQueryUI(); } else { $('#' + tag).html( msg ); DZCP.UpdateJQueryUI(); } });
    },

    // init Ajax DynLoader Sides via Ajax
    initPageDynLoader: function(tag,url) {
        if(!DZCP.jQueryCheck(false)) return false;
        DZCP.DebugLogger('PageDynLoader -> Tag: \'' + tag + '\' / URL: \'' + url + '\'');
        var request = $.ajax({ url: url });
        request.done(function(msg) { $('#' + tag).html(msg).hide().fadeIn("normal"); DZCP.initLightbox(); DZCP.UpdateJQueryUI(); });
    },

    // init Ajax DynCaptcha
    initDynCaptcha: function(tag,height,width,lines,namespace,length,sid) {
        if(!DZCP.jQueryCheck(false)) return false;
        var url_input = "../inc/ajax.php?i=securimage";
        if(height >  1) { url_input = url_input + "&height="+height; }
        if(width > 1) { url_input = url_input + "&width="+width; }
        if(lines >= 1) { url_input = url_input + "&lines="+lines; }
        if(namespace.length > 1) { url_input = url_input + "&namespace="+namespace; }
        if(length >= 1) { url_input = url_input + "&length="+length; }
        if(sid > 0) { url_input = url_input + "&sid="+sid; } else { url_input = url_input + "&sid="+Math.random(); }
        DZCP.DebugLogger('DynCaptcha -> Tag: \'' + tag + '\' / URL: \'' + url_input + '\'');
        var request = $.ajax({ url: url_input });
        request.done(function(msg) { $('#' + tag).attr("src",msg).hide().fadeIn("normal"); DZCP.UpdateJQueryUI(); });
    },

    // Play Sound per JS-Audio
    EvalSound: function(url) {
        if(!DZCP.jQueryCheck(false)) return false;
        DZCP.DebugLogger('EvalSound -> URL: \'' + url + '\'');
        var audio = new Audio(url);
        audio.play();
    },

    // switch userlist
    switchuser: function() {
        var url = doc.formChange.changeme.options[doc.formChange.changeme.selectedIndex].value;
        window.location.href = url;
    },

    // Roxy Fileman
    openCustomRoxy: function(){
        $('#roxyCustomPanel').dialog({
            modal:true,
            width:875,
            height:600,
            resizable: false,
            position: { my: 'top', at: 'top+100' },
            closeOnEscape: false
        });
    },

    closeCustomRoxy: function(){
        $('#roxyCustomPanel').dialog('close');
    },

    // Templateswitch
    tempswitch: function() {
        let url = doc.form.tempswitch.options[doc.form.tempswitch.selectedIndex].value;
        if(url !== 'lazy') DZCP.goTo(url);
    },

    // go to defined url
    goTo: function(url, n) {
        if(n === 1) window.open(url);
        else window.location.href = url;
    },

    // limit text lenthn
    maxlength: function(field, countfield, max) {
        if(field.value.length > max) field.value = field.value.substring(0, max);
        else countfield.value = max - field.value.length;
    },

    // handle info layer
    showInfo: function(info, kats, text, img, width, height) {
        if(typeof(layer) === 'object') {
            var output = '';
            if(kats && text){
                var kat=kats.split(";");
                var texts=text.split(";");
                var katout = "";
                for(var i=0; i<kat.length; ++i) {
                    katout = katout + '<tr><td>'+kat[i]+'</td><td>'+texts[i]+'</td></tr>';
                }
                output = '<tr><td class="infoTop" colspan="2">'+info+'</td></tr>'+katout+'';
            }else if(kats && typeof(text)==="undefined"){
                output = '<tr><td class="infoTop" colspan="2">'+info+'</td></tr><tr><td>'+kats+'</td></tr>';
            }else{
                output = '<tr><td>'+info+'</td></tr>';
            }

            var userimg = "";
            if(img){
                userimg = '<tr><td colspan=2 align=center><img src="'+img+'" width="'+width+'" height="'+height+'" alt="" /></td></tr>';
            }else{
                userimg = '';
            }

            layer.innerHTML =
                '<div id="hDiv">' +
                '  <table class="hperc" cellspacing="0" style="height:100%">' +
                '    <tr>' +
                '      <td style="vertical-align:middle">' +
                '        <div id="infoInnerLayer">' +
                '          <table class="hperc" cellspacing="0">' +
                '              '+output+'' +
                '              '+userimg+'' +
                '          </table>' +
                '        </div>' +
                '      </td>' +
                '    </tr>' +
                '  </table>' +
                '</div>';

            //IE Fix
            if(ie4 && !opera) {
                layer.innerHTML += '<iframe id="ieFix" frameborder="0" width="' + $('#hDiv')[0].offsetWidth + '" height="' + $('#hDiv')[0].offsetHeight + '"></iframe>';
                layer.style.display = 'block';
            } else
                layer.style.display = 'block';
        }
    },

    hideInfo: function() {
        if(typeof(layer) === 'object') {
            layer.innerHTML = '';
            layer.style.display = 'none';
        }
    },

    // toggle object
    toggle: function(id) {
        if(!DZCP.jQueryCheck(false)) return false;
        if(id === 0) return;
        if($('#more' + id).css('display') === 'none') {
            $("#more" + id).fadeIn("normal");
            $('#img' + id).prop('src', '../inc/images/collapse.gif');
        } else {
            $("#more" + id).fadeOut("normal");
            $('#img' + id).prop('src', '../inc/images/expand.gif');
        }
    },

    // toggle with effect *TS3
    fadetoggle: function(id) {
        if(!DZCP.jQueryCheck(false)) return false;
        if(id === 0) return;
        $("#more_"+id).fadeToggle("slow", "swing");
        if($('#img_'+id).prop('alt') === "hidden") {
            $('#img_'+id).prop({alt: 'normal', src: '../inc/images/toggle_normal.png'});
        } else {
            $('#img_'+id).prop({alt: 'hidden', src: '../inc/images/toggle_hidden.png'});
        }
    },

    // resize images
    resizeImages: function() {
        if(!DZCP.jQueryCheck(false)) return false;
        for(var i=0;i<doc.images.length;i++) {
            var d = doc.images[i];
            if(d.className === 'content') {
                var imgW = d.width;
                var imgH = d.height;

                if(dzcp_config.maxW !== 0 && imgW > dzcp_config.maxW) {
                    d.width = dzcp_config.maxW;
                    d.height = Math.round(imgH * (dzcp_config.maxW / imgW));

                    if(!DZCP.linkedImage(d)) {
                        var textLink = doc.createElement("span");
                        var popupLink = doc.createElement("a");

                        textLink.appendChild(doc.createElement("br"));
                        textLink.setAttribute('class', 'resized');
                        textLink.appendChild(doc.createTextNode('auto resized to '+d.width+'x'+d.height+' px'));

                        popupLink.setAttribute('href', d.src);
                        popupLink.setAttribute('rel', 'lightbox');
                        popupLink.appendChild(d.cloneNode(true));

                        d.parentNode.appendChild(textLink);
                        d.parentNode.replaceChild(popupLink, d);

                        DZCP.initLightbox();
                    }
                }
            }
        }
    },

    linkedImage: function(node) {
        do {
            node = node.parentNode;
            if (node.nodeName === 'A') return true;
        }
        while(node.nodeName !== 'TD' && node.nodeName !== 'BODY');
        return false;
    },

    // ajax calendar switch
    calSwitch: function(m, y) {
        if(!DZCP.jQueryCheck(false)) return false;
        var request = $.ajax({ url: '../inc/ajax.php?i=kalender&month=' + m + '&year=' + y, type: "GET", data: {}, cache:false, dataType: "html", contentType: "application/x-www-form-urlencoded;" });
        request.done(function(msg) { $('#navKalender').html( msg ).hide().fadeIn("normal"); });
    },

    // ajax vote
    ajaxVote: function(id) {
        if(!DZCP.jQueryCheck(false)) return false;
        DZCP.submitButton('contentSubmitVote');
        $.post('../votes/index.php?action=do&ajax=1&what=vote&id=' + id, $('#navAjaxVote').serialize(), function(req) {
            $('#navVote').html(req);
        });

        return false;
    },

    // ajax forum vote
    ajaxFVote: function(id) {
        if(!DZCP.jQueryCheck(false)) return false;
        DZCP.submitButton('contentSubmitFVote');
        $.post('../votes/index.php?action=do&fajax=1&what=fvote&id=' + id, $('#navAjaxFVote').serialize(), function(req) {
            $('#navFVote').html(req);
        });

        return false;
    },

    // ajax preview
    ajaxPreview: function(form) {
        if(!DZCP.jQueryCheck(false)) return false;
        DZCP.DebugLogger('Ajax Preview -> Tag: \'' + form + '\'');
        $('#previewDIV').html('<div style="width:100%;text-align:center">'
            + ' <img src="../inc/images/ajax-loader-bar.gif" alt="" />'
            + '</div>');

        var url = prevURL;
        $.post(url, $('#' + form).serialize(), function(req) {
            $('#previewDIV').html(req).hide().fadeIn("fast");
            DZCP.resizeImages(); DZCP.initCodeHighlighting();
        });
    },

    // forum search
    hideForumFirst: function() {
        if(!DZCP.jQueryCheck(false)) return false;
        $('#allkat').prop('checked', false);
    },

    hideForumAll: function() {
        if(!DZCP.jQueryCheck(false)) return false;
        for(var i = 0; i < doc.forms['search'].elements.length; i++)
        {
            var box = doc.forms['search'].elements[i];

            if(box.id.match(/k_/g))
                box.checked = false;
        }
    },

    // disable submit button
    submitButton: function(id) {
        if(!DZCP.jQueryCheck(false)) return false;
        submitID = (id) ? id : 'contentSubmit';
        $('#' + submitID).prop("disabled", true);
        $('#' + submitID).css('color', '#909090');
        $('#' + submitID).css('cursor', 'default');
        return true;
    },

    moveDiv: function(obj, width, subID) {
        if(!DZCP.jQueryCheck(false)) return false;
        var thisObj = $('#' + obj)[0];
        if(tickerTo[subID] === 'h') thisObj.style.left = (parseInt(thisObj.style.left) <= (0-(width/2)+2)) ? 0 : parseInt(thisObj.style.left)-1 + 'px';
        else thisObj.style.top = (thisObj.style.top === '' || (parseInt(thisObj.style.top)<(0-(width/2)+6))) ? 0 : parseInt(thisObj.style.top)-1 + 'px';
    },

    GoToAnchor: function() {
        if(!DZCP.jQueryCheck(false)) return false;
        if(!DZCP.empty(dzcp_config.AnchorMove)) {
            DZCP.DebugLogger('GoToAnchor -> Tag: \'' + dzcp_config.AnchorMove + '\'');
            $('html, body').animate({ scrollTop: $("#" + dzcp_config.AnchorMove).offset().top - 12 }, 'slow');
        }
    },

    empty: function(value) {
        if(!DZCP.jQueryCheck(false)) return false;
        return (value === null || $.noop(value) || !/\S/.test(value));
    },

    DebugLogger: function(message) {
        if(dzcp_config.debug) {
            console.info("DZCP Debug: " + message);
        }
    },

    checkbox_switch: function(obj,tag){
        if(!DZCP.jQueryCheck(false)) return false;
        DZCP.DebugLogger('Change all Checkboxes with ID:'+tag+'_*');
        $('input:checkbox[id^="'+tag+'_"]').not(obj).prop('checked', obj.checked);
    },

    check_all: function(name, obj) {
        if(!obj || !obj.form) return false;
        var box = obj.form.elements[name];
        if(!box) return false;
        if(!box.length) box.checked = obj.checked; else
            for(var i = 0; i < box.length; i++)  box[i].checked = obj.checked;
    },

    sendFrom: function(do_obj,do_a,formId) {
        $('input[name='+ do_obj +']').val(do_a);
        $("#" + formId).submit();
    },

    getUrlParameters: function (parameter, decode){
        var currLocation = window.location.search,
            parArr = currLocation.split("?")[1].split("&");
        for(var i = 0; i < parArr.length; i++){
            parr = parArr[i].split("=");
            if(parr[0] == parameter){
                return (decode) ? decodeURIComponent(parr[1]) : parr[1];
            }
        }

        return false;
    },
};

// load global events
$( document ).ready(function () {
    if(DZCP.jQueryCheck(true)) {
        DZCP.init();
    }
});

$(window).on('load', function () {
    if(DZCP.jQueryCheck(true)) {
        DZCP.resizeImages();
        DZCP.GoToAnchor();
    }
});

$( window ).resize(function () {
        DZCP.resizeImages();
        DZCP.UpdateJQueryUI();
});