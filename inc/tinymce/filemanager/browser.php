<?php
session_start();
## OUTPUT BUFFER START ##
include("../../buffer.php");
## INCLUDES ##
include(basePath."/inc/config.php");
include(basePath."/inc/bbcode.php");

## FILEMANAGER ##
if(!admin_perms($userid))
{
  echo '<b>Wrong permissions!</b>';
} else {
?>
<!--
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: browser.php
 * 	This page compose the File Browser dialog frameset.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title>DZCP - Filemanager</title>
		<link href="browser.css" type="text/css" rel="stylesheet">
		<script type="text/javascript" src="js/fckxml.js"></script>
		<script language="javascript">

function GetUrlParam( paramName )
{
	var oRegex = new RegExp( '[\?&]' + paramName + '=([^&]+)', 'i' ) ;
	var oMatch = oRegex.exec( window.top.location.search ) ;
  
	if ( oMatch && oMatch.length > 1 )
		return oMatch[1] ;
	else
		return '' ;
}

var oConnector = new Object() ;
oConnector.CurrentFolder	= '/' ;

var sConnUrl = GetUrlParam( 'Connector' ) ;

// Gecko has some problems when using relative URLs (not starting with slash).
if ( sConnUrl.substr(0,1) != '/' && sConnUrl.indexOf( '://' ) < 0 ){
	sConnUrl = window.location.href.replace( /browser.php.*$/, '' ) + sConnUrl ;
	sConnUrl = sConnUrl.replace(/\/\/filemanager/, '/filemanager');
	}

oConnector.ConnectorUrl = sConnUrl + ( sConnUrl.indexOf('?') != -1 ? '&' : '?' ) ;

var sServerPath = GetUrlParam( 'ServerPath' ) ;
if ( sServerPath.length > 0 )
	oConnector.ConnectorUrl += 'ServerPath=' + escape( sServerPath ) + '&' ;

oConnector.ResourceType		= GetUrlParam( 'Type' ) ;
oConnector.ShowAllTypes		= ( oConnector.ResourceType.length == 0 ) ;

if ( oConnector.ShowAllTypes )
	oConnector.ResourceType = 'File' ;

oConnector.SendCommand = function( command, params, callBackFunction )
{
	var sUrl = this.ConnectorUrl + 'Command=' + command ;
	sUrl += '&Type=' + this.ResourceType ;
	sUrl += '&CurrentFolder=' + escape( this.CurrentFolder ) ;
	
	if ( params ) sUrl += '&' + params ;

	var oXML = new FCKXml() ;
	
	if ( callBackFunction )
		oXML.LoadUrl( sUrl, callBackFunction ) ;	// Asynchronous load.
	else
		return oXML.LoadUrl( sUrl ) ;
}

oConnector.CheckError = function( responseXml )
{
	var iErrorNumber = 0
	var oErrorNode = responseXml.SelectSingleNode( 'Connector/Error' ) ;
	
	if ( oErrorNode )
	{
		iErrorNumber = parseInt( oErrorNode.attributes.getNamedItem('number').value ) ;
		
		switch ( iErrorNumber )
		{
			case 0 :
				break ;
			case 1 :	// Custom error. Message placed in the "text" attribute.
				alert( oErrorNode.attributes.getNamedItem('text').value ) ;
				break ;
			case 101 :
				alert( 'Dieser Ordner existiert bereits!' ) ;
				break ;
			case 102 :
				alert( 'Ungueltiger Ordnername!' ) ;
				break ;
			case 103 :
				alert( 'Du hast keine Rechte einen Ordner zu erstellen!' ) ;
				break ;
			case 110 :
				alert( 'Unbekannter Fehler!' ) ;
				break ;
			default :
				alert( 'Error on your request. Error number: ' + iErrorNumber ) ;
				break ;
		}
	}
	return iErrorNumber ;
}

var oIcons = new Object() ;

oIcons.AvailableIconsArray = [ 
	'ai','avi','bmp','cs','dll','doc','exe','fla','flv','gif','htm','html','jpg','js',
	'mdb','mp3','pdf','ppt','rdp','swf','swt','txt','vsd','xls','xml','zip' ] ;
	
oIcons.AvailableIcons = new Object() ;

for ( var i = 0 ; i < oIcons.AvailableIconsArray.length ; i++ )
	oIcons.AvailableIcons[ oIcons.AvailableIconsArray[i] ] = true ;

oIcons.GetIcon = function( fileName )
{
	var sExtension = fileName.substr( fileName.lastIndexOf('.') + 1 ).toLowerCase() ;

	if ( this.AvailableIcons[ sExtension ] == true )
		return sExtension ;
	else
		return 'default.icon' ;
}
		</script>
	</head>
	<frameset cols="0,150,*" class="browserFrame" frameborder="1" framespacing="0" border="1">

      <frame src="frmresourcetype.html" scrolling="no" frameborder="no">
			<frame name="frmFolders" src="frmfolders.html" scrolling="auto" frameborder="yes">

		<frameset rows="50,*,50" framespacing="0">
			<frame name="frmActualFolder" src="frmactualfolder.php" scrolling="no" frameborder="no">
			<frame name="frmResourcesList" src="frmresourceslist.html" scrolling="auto" frameborder="yes">
			<frameset cols="170,*,1" framespacing="0" frameborder="no">
				<frame name="frmCreateFolder" src="frmcreatefolder.html" scrolling="no" frameborder="no">
				<frame name="frmUpload" src="frmupload.html" scrolling="no" frameborder="no">
				<frame name="frmUploadWorker" src="../blank.html" scrolling="no" frameborder="no">
			</frameset>
		</frameset>
	</frameset><noframes></noframes>
</html>
<?php
  }
ob_end_flush();
?>