<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="pragma" content="No-Cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>RoxyFile Manager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../inc/ajax.php?i=less&less=template.fileman&refresh=1" />
    {$js_config}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-ui-dist@1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-dateformat@1.0.4/dist/dateFormat.min.js"></script>
    <script type="text/javascript" src="{dir}/_js/fileman/filetypes.min.js"></script>
    <script type="text/javascript" src="{dir}/_js/fileman/custom.min.js"></script>
    <script type="text/javascript" src="{dir}/_js/fileman/main.min.js"></script>
    <script type="text/javascript" src="{dir}/_js/fileman/utils.min.js"></script>
    <script type="text/javascript" src="{dir}/_js/fileman/file.min.js"></script>
    <script type="text/javascript" src="{dir}/_js/fileman/directory.min.js"></script>
</head>
<body>
<table cellpadding="0" cellspacing="0" id="wraper">
    <tr>
        <td valign="top" class="pnlDirs" id="dirActions">
            <div class="actions">
                <input type="button" id="btnAddDir" value="Create" title="Create new directory" onclick="addDir()" data-lang-v="CreateDir" data-lang-t="T_CreateDir" />
                <input type="button" id="btnRenameDir" value="Rename" title="Rename directory" onclick="renameDir()" data-lang-v="RenameDir" data-lang-t="T_RenameDir" />
                <input type="button" id="btnDeleteDir" value="Delete" title="Delete selected directory" onclick="deleteDir()" data-lang-v="DeleteDir" data-lang-t="T_DeleteDir" />
            </div>
            <div id="pnlLoadingDirs">
                <span>Loading directories...</span><br>
                <img src="{idir}/fileman/loading.gif" title="Loading directory tree, please wait...">
            </div>
            <div class="scrollPane">
                <ul id="pnlDirList"></ul>
            </div>
        </td>
        <td valign="top" id="fileActions">
            <input type="hidden" id="hdViewType" value="list">
            <input type="hidden" id="hdOrder" value="asc">
            <div class="actions">
                <input type="button" id="btnAddFile" value="Add file" title="Upload files" onclick="addFileClick()" data-lang-v="AddFile" data-lang-t="T_AddFile" />
                <input type="button" id="btnPreviewFile" value="Preview" title="Preview selected file" onclick="previewFile()" data-lang-v="Preview" data-lang-t="T_Preview" />
                <input type="button" id="btnRenameFile" value="Rename" title="Rename selected file" onclick="renameFile()" data-lang-v="RenameFile" data-lang-t="T_RenameFile" />
                <input type="button" id="btnDownloadFile" value="Download" title="Download selected file" onclick="downloadFile()" data-lang-v="DownloadFile" data-lang-t="T_DownloadFile" />
                <input type="button" id="btnDeleteFile" value="Delete" title="Delete selected file" onclick="deleteFile()" data-lang-v="DeleteFile" data-lang-t="T_DeleteFile" />
                <input type="button" id="btnSelectFile" value="Select" title="Select highlighted file" onclick="setFile()" data-lang-v="SelectFile" data-lang-t="T_SelectFile" />
                <br>
                <span data-lang="OrderBy">Order by</span>:
                <select id="ddlOrder" onchange="sortFiles()">
                    <option value="name" data-lang="Name_asc">&uarr;&nbsp;&nbsp;Name</option>
                    <option value="size" data-lang="Size_asc">&uarr;&nbsp;&nbsp;Size</option>
                    <option value="time" data-lang="Date_asc">&uarr;&nbsp;&nbsp;Date</option>
                    <option value="name_desc" data-lang="Name_desc">&darr;&nbsp;&nbsp;Name</option>
                    <option value="size_desc" data-lang="Size_desc">&darr;&nbsp;&nbsp;Size</option>
                    <option value="time_desc" data-lang="Date_desc">&darr;&nbsp;&nbsp;Date</option>
                </select>&nbsp;&nbsp;
                <input type="button" id="btnListView" class="btnView" value=" " title="List view" onclick="switchView('list')" data-lang-t="T_ListView" />
                <input type="button" id="btnThumbView" class="btnView" value=" " title="Thumbnails view" onclick="switchView('thumb')" data-lang-t="T_ThumbsView" />&nbsp;&nbsp;
                <input type="text" id="txtSearch" onkeyup="filterFiles()" onchange="filterFiles()" />
            </div>
            <div class="pnlFiles">
                <div class="scrollPane">
                    <div id="pnlLoading">
                        <span data-lang="LoadingFiles">Loading files...</span><br>
                        <img src="{idir}/fileman/loading.gif" title="Loading files, please wait...">
                    </div>
                    <div id="pnlEmptyDir" data-lang="DirIsEmpty">
                        This folder is empty
                    </div>
                    <div id="pnlSearchNoFiles" data-lang="NoFilesFound">
                        No files found
                    </div>
                    <ul id="pnlFileList"></ul>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="bottomLine">
            &nbsp;&nbsp;&nbsp;<a href="http://www.roxyfileman.com" target="_blank">&copy; 2013 - <span id="copyYear"></span> RoxyFileman | Frontend</a>
        </td>
        <td class="bottomLine">
            <div id="pnlStatus">Status bar</div>
        </td>
    </tr>
</table>

<!-- Forms and other components -->
<div id="dlgAddFile">
    <form name="addfile" id="frmUpload" method="post" target="frmUploadFile" enctype="multipart/form-data">
        <input type="hidden" name="d" id="hdDir" />
        <div class="form"><br />
            <input type="file" name="files[]" id="fileUploads" onchange="listUploadFiles(this.files)" multiple="multiple" />
            <div id="uploadResult"></div>
            <div class="uploadFilesList">
                <div id="uploadFilesList"></div>
            </div>
        </div>
    </form>
</div>

<div id="menuFile" class="contextMenu">
    <a href="#" onclick="setFile()" data-lang="SelectFile" id="mnuSelectFile">Select</a><hr>
    <a href="#" onclick="previewFile()" data-lang="Preview" id="mnuPreview">Preview</a><hr>
    <a href="#" onclick="downloadFile()" data-lang="DownloadFile" id="mnuDownload">Download</a><hr>
    <a href="#" onclick="return pasteToFiles(event, this)" data-lang="Paste" class="paste pale" id="mnuFilePaste">Paste</a><hr>
    <a href="#" onclick="cutFile()" data-lang="Cut" id="mnuFileCut">Cut</a><hr>
    <a href="#" onclick="copyFile()" data-lang="Copy" id="mnuFileCopy">Copy</a><hr>
    <a href="#" onclick="renameFile()" data-lang="RenameFile" id="mnuRenameFile">Rename</a><hr>
    <a href="#" onclick="deleteFile()" data-lang="DeleteFile" id="mnuDeleteFile">Delete</a><hr>
</div>

<div id="menuDir" class="contextMenu">
    <a href="#" onclick="downloadDir()" data-lang="Download" id="mnuDownloadDir">Download</a><hr>
    <a href="#" onclick="addDir()" data-lang="T_CreateDir" id="mnuCreateDir">Create new</a><hr>
    <a href="#" onclick="return pasteToDirs(event, this)" data-lang="Paste" class="paste pale" id="mnuDirPaste">Paste</a><hr>
    <a href="#" onclick="cutDir()" data-lang="Cut" id="mnuDirCut">Cut</a><hr>
    <a href="#" onclick="copyDir()" data-lang="Copy" id="mnuDirCopy">Copy</a><hr>
    <a href="#" onclick="renameDir()" data-lang="RenameDir" id="mnuRenameDir">Rename</a><hr>
    <a href="#" onclick="deleteDir()" data-lang="DeleteDir" id="mnuDeleteDir">Delete</a>
</div>

<div id="pnlRenameFile" class="dialog">
    <span class="name"></span><br>
    <input type="text" id="txtFileName">
</div>

<div id="pnlDirName" class="dialog">
    <span class="name"></span><br>
    <input type="text" id="txtDirName">
</div>

<iframe name="frmUploadFile" id="frmUploadFile" style="display: none;" width="800" height="600"></iframe>
</body>
</html>