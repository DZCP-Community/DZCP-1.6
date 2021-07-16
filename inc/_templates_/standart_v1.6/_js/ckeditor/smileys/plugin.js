/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
CKEDITOR.plugins.add( 'smileys', {
    requires: 'dialog,bbcode', //DZCP 1.7
    lang: 'de,uk', // %REMOVE_LINE_CORE%
    icons: 'smileys', // %REMOVE_LINE_CORE%
    hidpi: true, // %REMOVE_LINE_CORE%
    init: function( editor ) {
        editor.config.smiley_path = editor.config.smiley_path;
        editor.addCommand( 'smileys', new CKEDITOR.dialogCommand( 'smileys', {
            allowedContent: 'img[alt,height,!src,title,width]',
            requiredContent: 'img'
        } ) );

        editor.ui.addButton && editor.ui.addButton( 'Smileys', {
            label: editor.lang.smileys.toolbar,
            command: 'smileys',
            toolbar: 'insert,50'
        } );

        //Add Dialog
        CKEDITOR.dialog.add( 'smileys', this.path + 'dialogs/smileys.js' );
    }
} );

/*
Export to bbcode plugin:
CKEDITOR.config.smiley_images
CKEDITOR.config.smiley_descriptions
CKEDITOR.config.smiley_columns
CKEDITOR.config.smiley_path
 */