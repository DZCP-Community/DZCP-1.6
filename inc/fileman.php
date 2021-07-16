<?php

/**
 * Class fileman
 * Roxy Fileman PHP Backend for DZCP
 */
class fileman extends common {
    private $buffer = array();
    private $upload_dir = '';
    private $is_user_dir = false;
    private $is_group_dir = false;
    private $input = [];
    private $zip = null;

    public static function getInstance(bool $no_init=false) {
        $fileman = new fileman();
        if(!$no_init)
            $fileman->init();

        return $fileman;
    }

    public function init() {
        //Set JS Config
        javascript::set('THUMBS_VIEW_WIDTH',140);
        javascript::set('THUMBS_VIEW_HEIGHT',120);
        javascript::set('PREVIEW_THUMB_WIDTH',100);
        javascript::set('PREVIEW_THUMB_HEIGHT',100);
        javascript::set('MAX_IMAGE_WIDTH',1000);
        javascript::set('MAX_IMAGE_HEIGHT',1000);
        javascript::set('INTEGRATION','ckeditor');
        javascript::set('DIRLIST',"../inc/ajax.php?i=fileman&call=dirtree");
        javascript::set('CREATEDIR',"../inc/ajax.php?i=fileman&call=createdir");
        javascript::set('DELETEDIR',"../inc/ajax.php?i=fileman&call=deletedir");
        javascript::set('MOVEDIR','../inc/ajax.php?i=fileman&call=movedir');
        javascript::set('COPYDIR','../inc/ajax.php?i=fileman&call=copydir');
        javascript::set('RENAMEDIR','../inc/ajax.php?i=fileman&call=renamedir');
        javascript::set('FILESLIST','../inc/ajax.php?i=fileman&call=fileslist');
        javascript::set('UPLOAD','../inc/ajax.php?i=fileman&call=upload');
        javascript::set('DOWNLOAD','../inc/ajax.php?i=fileman&call=download');
        javascript::set('DOWNLOADDIR','../inc/ajax.php?i=fileman&call=downloaddir');
        javascript::set('DELETEFILE','../inc/ajax.php?i=fileman&call=deletefile');
        javascript::set('MOVEFILE','../inc/ajax.php?i=fileman&call=movefile');
        javascript::set('COPYFILE','../inc/ajax.php?i=fileman&call=copyfile');
        javascript::set('RENAMEFILE','../inc/ajax.php?i=fileman&call=renamefile');
        javascript::set('GENERATETHUMB','../inc/ajax.php?i=fileman&call=thumb');
        javascript::set('FORBIDDEN_UPLOADS',config::$upload_forbidden_uploads);
        javascript::set('ALLOWED_UPLOADS',config::$upload_allowed_uploads);

        switch ($_SESSION['language']) {
            case 'uk': javascript::set('LANG','en'); break;
            default:
                if(file_exists(basePath.'/inc/lang/fileman/'.
                    strtolower($_SESSION['language']).'.json')) {
                    javascript::set('LANG',$_SESSION['language']);
                } else {
                    javascript::set('LANG','auto');
                }
                break;
        }

        javascript::set('DATEFORMAT','dd.MM.yyyy - HH:mm');
        javascript::set('OPEN_LAST_DIR','yes');

        //Settings
        $this->is_user_dir = (self::$userid >= 1); // is a user
        $this->is_group_dir = (self::$sql['default']->rows("SELECT `id` FROM `{prefix_group_user}` WHERE `user` = ?;",
                [self::$userid]) >= 1); // Check is user in a group

        $this->upload_dir = $this->getFilesPath(); //BasePath to upload dir
    }

    public function run() {
        $output = [];
        if ((self::$chkMe >= 1 && self::$userid >= 1) || self::permission('fileman')) {
            $this->input = self::$gump->sanitize($_REQUEST);
            foreach ($this->input as $key => $var) {
                $this->input[$key] = trim($var); //Global Trim
            }

            switch ((isset($_GET['call']) ? strtolower($_GET['call']) : '')) {
                case 'fileslist':
                    $output += $this->fileslist();
                    break;
                case 'dirtree':
                    $output += $this->dirtree();
                    break;
                case 'createdir':
                    @ignore_user_abort(true);
                    @set_time_limit(0);
                    $output += $this->createdir();
                    break;
                case 'deletedir':
                    @ignore_user_abort(true);
                    @set_time_limit(0);
                    $output += $this->deletedir();
                    break;
                case 'movedir':
                    @ignore_user_abort(true);
                    @set_time_limit(0);
                    $output += $this->movedir();
                    break;
                case 'copydir':
                    @ignore_user_abort(true);
                    @set_time_limit(0);
                    $output += $this->copydir();
                    break;
                case 'renamedir':
                    @ignore_user_abort(true);
                    @set_time_limit(0);
                    $output += $this->renamedir();
                    break;
                case 'upload':
                    @ini_set('memory_limit', -1);
                    @set_time_limit(0);
                    $output += $this->upload();
                    break;
                case 'download':
                    @ini_set('memory_limit', -1);
                    @ignore_user_abort(true);
                    @set_time_limit(0);
                    $this->download(); //EXIT
                    break;
                case 'downloaddir':
                    @ini_set('memory_limit', -1);
                    @ignore_user_abort(true);
                    @set_time_limit(0);
                    $this->downloaddir(); //EXIT
                    break;
                case 'deletefile':
                    @ignore_user_abort(true);
                    @set_time_limit(0);
                    $output += $this->deletefile();
                    break;
                case 'movefile':
                    @ignore_user_abort(true);
                    @set_time_limit(0);
                    $output += $this->movefile();
                    break;
                case 'copyfile':
                    @ignore_user_abort(true);
                    @set_time_limit(0);
                    $output += $this->copyfile();
                    break;
                case 'renamefile':
                    @ignore_user_abort(true);
                    @set_time_limit(0);
                    $output += $this->renamefile();
                    break;
                case 'thumb':
                    @ignore_user_abort(true);
                    @set_time_limit(0);
                    $this->thumb();
                    break;
            }
        }

        //Headers
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header("Expires: " . gmdate("D, d M Y H:i:s", time() - 1) . " GMT");
        header('Content-Type: application/json');
        exit(utf8_encode(json_encode($output)));
    }

    /**
     * ###################################################
     *  FILEMAN FUNCTIONS
     * ###################################################
     */

    /**
     * Gibt die Ordnerstruktur aus.
     * @return array
     */
    private function dirtree()
    {
        $type = (!empty($this->input['type']) ? strtolower($this->input['type']) : 'file');
        $tmp = $this->getFilesNumber($this->upload_dir, $type);

        if (self::$chkMe == 4 || self::permission('fileman')) {
            $name = null;
            if (mb_strrpos($this->upload_dir, '/') !== false) {
                $ext = mb_substr($this->upload_dir, mb_strrpos($this->upload_dir, '/') + 1);
                if (preg_match("/\_(.*?)\_/", $ext) && defined('_fileman' . $ext)) {
                    $name = constant('_fileman' . $ext);
                }
            }

            $this->buffer[] = ['p' => $this->upload_dir, 'n' => $name, 'f' => $tmp['files'], 'd' => $tmp['dirs']];
        }

        if (self::$chkMe == 4 || self::permission('fileman')) {
            $this->upload_dir = $this->getFilesPath();
            $this->GetDirs($this->upload_dir, $type);
        } else {
            if ($this->is_user_dir) {
                $this->upload_dir = $this->getFilesPath('/_users_');
                if(!is_dir(basePath .$this->upload_dir.'/_user'.self::$userid.'_/')) {
                    mkdir(self::FixPath(basePath .$this->upload_dir.'/_user'.self::$userid.'_'), octdec(config::$upload_dir_permissions), true);
                    mkdir(self::FixPath(basePath .$this->upload_dir.'/_user'.self::$userid.'_/Documents'), octdec(config::$upload_dir_permissions), true);
                    mkdir(self::FixPath(basePath .$this->upload_dir.'/_user'.self::$userid.'_/Images'), octdec(config::$upload_dir_permissions), true);
                }

                $this->GetDirs($this->upload_dir, $type);
            }

            if ($this->is_group_dir) {
                $this->upload_dir = $this->getFilesPath('/_group_');
                $this->GetDirs($this->upload_dir, $type);
            }
        }

        return $this->buffer;
    }

    /**
     * Gibt eine Liste mit den Dateien aus.
     * @return array
     */
    private function fileslist()
    {
        $path = (!empty($this->input['d']) ? $this->input['d'] : $this->upload_dir);
        $type = (!empty($this->input['type']) ? strtolower($this->input['type']) : 'file');
        $path = str_replace('../', '/', $path);

        //Protect ROOT-System
        if (!preg_match("/\_uploads_/", strtolower(self::FixPath($path)))) {
            return []; //empty
        }

        //Lock Root-Folder
        if (strtolower($dir=self::FixPath(basePath . $path)) == strtolower(self::FixPath(basePath . $this->upload_dir))) {
            return []; //empty
        }

        //Lock Root-Folders for User
        if(self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_')) ||
                strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_')) ||
                strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_'))) {
                return []; //empty
            }
        }

        $filelist = [];
        $files = self::get_files(basePath . $path, false, true, [], true);
        natcasesort($files);
        foreach ($files as $file) {
            $width = 0; $height = 0;
            $fullPath = self::FixPath($path . '/' . $file);
            if (!is_file(self::FixPath(basePath . $fullPath)) || ($type == 'image' && !$this->IsImage($fullPath))) {
                continue;
            }

            $size = filesize(self::FixPath(basePath . $fullPath)); //Filesize
            $time = filemtime(self::FixPath(basePath . $fullPath)); //Filemtime

            //Is a Image get getimagesize
            if ($type == 'image' || $this->IsImage($fullPath)) {
                $hash = md5($fullPath);
                if (!self::$cache->AutoMemExists($hash) || !config::$use_system_cache) {
                    $tmp = getimagesize((basePath . $fullPath));
                    if (is_array($tmp)) {
                        $width = $tmp[0];
                        $height = $tmp[1];
                        if (config::$use_system_cache) {
                            self::$cache->AutoMemSet($hash, $tmp, cache::TIME_FILEMAN_IMG_STATS);
                        }
                    }
                } else {
                    $tmp = self::$cache->AutoMemGet($hash);
                    $width = $tmp[0];
                    $height = $tmp[1];
                }
            }

            $filelist[] = ['p' => /* TODO: PHP Load */ (string)'..'.$fullPath, 's' => $size, 't' => $time, 'w' => $width, 'h' => $height];
        }

        return $filelist;
    }

    /**
     * Erstellt einen neuen Ordner.
     * @return array
     */
    private function createdir()
    {
        if(empty($this->input['d']) || empty($this->input['n'])) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_unknown)];
        }

        //FILTER
        $path = str_replace('../', '/', $this->input['d']);
        $name = str_replace([' ',], ['_'], preg_replace('/[^a-zA-Z0-9-äöüÄÜÖß\/]/i','', $this->input['n']));
        $name = str_replace(["ä","ü","ö","Ä","Ü","Ö","´"],["ae","ue","oe","Ae","ue","oe",""], $name);

        //Protect ROOT-System
        if (!preg_match("/\_uploads_/", strtolower(self::FixPath($path)))) {
            exit();
        }

        //Lock Root-Folder
        if (strtolower($dir=self::FixPath(basePath . $path)) == strtolower(self::FixPath(basePath . $this->upload_dir))) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons_root)]; //error
        }

        //Lock Root-Folders for User
        if(self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_')) ||
                strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_'))) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        }

        $smarty = self::getSmarty(true);
        //Permissions for Admin / User with ID or permission => 'fileman'
        if (preg_match("/\_user([1-9][0-9]*?)\_/", $path, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if ($matches[1] != self::$userid) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        } unset($matches);

        //Group Permissions
        if(preg_match("/\_group([1-9][0-9]*?)\_/", $path, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            //Permissions for Admin / User with ID or permission => 'fileman'
            if (!self::$sql['default']->rows("SELECT `id` FROM `{prefix_group_user}` WHERE `user` = ? AND `group` = ?;",
                   [self::$userid, $matches[1]])) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        } unset($matches);

        if (is_dir($dir)) { //Check base path folder, if error
            if(is_dir(self::FixPath($dir . '/' . $name))) { //Is exists? if error
                $smarty->caching = false;
                $smarty->assign('dir', $name);
                $msg = $smarty->fetch('string:' . _fileman_error_create_dir_exists_failed);
                return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error is exists, if error
            }

            //Make new folder
            if (mkdir(self::FixPath($dir . '/' . $name),
                octdec(config::$upload_dir_permissions), true)) {
                if(is_dir($dir . '/' . $name)) { //check is new folder exists?
                    return ['res' => 'ok', 'msg' => '']; //success
                }
            }

            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_create_dir_failed)]; //error
        }

        return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_create_dir_invalid_path)]; //error
    }

    /**
     * Loscht einen Ordner mit Inhalt (recursive)
     * @return array
     */
    private function deletedir()
    {
        if(empty($this->input['d'])) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_unknown)];
        }

        $path = str_replace('../', '/', $this->input['d']);

        //Protect ROOT-System
        if (!preg_match("/\_uploads_/", strtolower(self::FixPath($path)))) {
            exit();
        }

        //Lock Root-Folder
        if (strtolower($dir=self::FixPath(basePath . $path)) == strtolower(self::FixPath(basePath . $this->upload_dir)) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_')) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_')) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_'))) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons_root)]; //error
        }

        //Cannot delete Users / only the admin can delete
        if (preg_match("/\_user([1-9][0-9]*?)\_/", $path, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_' . '/_user' . $matches[1] . '_'))) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_cannot_delete_root)]; //error
            }
        } unset($matches);

        //Cannot delete Groups / only the admin can delete
        if (preg_match("/\_group([1-9][0-9]*?)\_/", $path, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_' . '/_group' . $matches[1] . '_'))) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_cannot_delete_root)]; //error
            }
        } unset($matches);

        $smarty = self::getSmarty(true);
        if (is_dir($dir)) { //check is folder exists?
            if ($this->deleteFolder($path)) { //delete this folder (*recursive*)
                return ['res' => 'ok', 'msg' => '']; //success
            }

            $smarty->caching = false;
            $smarty->assign('dir', basename($path));
            $msg = $smarty->fetch('string:' . _fileman_error_cannot_delete_dir);
            return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
        }

        $smarty->caching = false;
        $smarty->assign('dir', basename($path));
        $msg = $smarty->fetch('string:' . _fileman_error_delete_dir_invalid_path);
        return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
    }

    /**
     * Bennent eine Datei um.
     * @return array
     */
    private function renamefile()
    {
        if(empty($this->input['f']) || empty($this->input['n'])) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_unknown)];
        }

        $path = str_replace('../', '/', $this->input['f']);
        $name = str_replace([' ',], ['_'], preg_replace('/[^a-zA-Z0-9-äöüÄÜÖß.\/]/i','', $this->input['n'])); //for files
        $name = str_replace(["ä","ü","ö","Ä","Ü","Ö","´"],["ae","ue","oe","Ae","ue","oe",""], $name);

        //Protect ROOT-System
        if (!preg_match("/\_uploads_/", strtolower(self::FixPath($path)))) {
            exit();
        }

        //Lock Root-Folder
        if (strtolower($dir=self::FixPath(basePath . $path)) == strtolower(self::FixPath(basePath . $this->upload_dir)) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_')) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_'))) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons_root)]; //error
        }

        //Lock Public-Folder for Users
        if(self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_'))) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        }

        //Rename by Owner User or admin
        if (preg_match("/\_user([1-9][0-9]*?)\_/", $path, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if ($matches[1] != self::$userid) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_cannot_delete_root)]; //error
            }
        } unset($matches);

        //Rename by Owner Groups or admin
        if(preg_match("/\_group([1-9][0-9]*?)\_/", $path, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            //Permissions for Admin / User with ID or permission => 'fileman'
            if (!self::$sql['default']->rows("SELECT `id` FROM `{prefix_group_user}` WHERE `user` = ? AND `group` = ?;",
                [self::$userid, $matches[1]])) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        } unset($matches);

        $smarty = self::getSmarty(true);
        if (is_file(($file = self::FixPath(basePath . $path)))) {
            if(rename($file, dirname(basePath . self::FixPath($path)) . '/' . $name)) {
                if(file_exists(dirname(basePath . self::FixPath($path)) . '/' . $name)) {
                    return ['res' => 'ok', 'msg' => '']; //success
                }
            }

            $smarty->caching = false;
            $smarty->assign('dir', basename($path).' -> '.basename($name));
            $msg = $smarty->fetch('string:' . _fileman_error_rename_file);
            return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
        }

        $smarty->caching = false;
        $smarty->assign('dir', basename($path));
        $msg = $smarty->fetch('string:' . _fileman_error_rename_file_invalid_path);
        return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
    }

    /**
     * Bennent eine Datei um.
     * @return array
     */
    private function renamedir()
    {
        if(empty($this->input['d']) || empty($this->input['n'])) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_unknown)];
        }

        $path = str_replace('../', '/', $this->input['d']);
        $name = str_replace([' ',], ['_'], preg_replace('/[^a-zA-Z0-9-äöüÄÜÖß\/]/i','', $this->input['n']));
        $name = str_replace(["ä","ü","ö","Ä","Ü","Ö","´"],["ae","ue","oe","Ae","ue","oe",""], $name);

        //Protect ROOT-System
        if (!preg_match("/\_uploads_/", strtolower(self::FixPath($path)))) {
            exit();
        }

        //Lock Root-Folder
        if (strtolower($dir=self::FixPath(basePath . $path)) == strtolower(self::FixPath(basePath . $this->upload_dir)) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_')) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_')) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_'))) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons_root)]; //error
        }

        //Cannot rename Users / only the admin can rename
        if (preg_match("/\_user([1-9][0-9]*?)\_/", $path, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_' . '/_user' . $matches[1] . '_'))) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        } unset($matches);

        //Cannot rename Groups / only the admin can rename
        if (preg_match("/\_group([1-9][0-9]*?)\_/", $path, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_' . '/_group' . $matches[1] . '_'))) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        } unset($matches);

        $smarty = self::getSmarty(true);
        if (is_dir($dir)) {
            if (rename($dir, dirname(basePath . self::FixPath($path)) . '/' . $name)) {
                if(is_dir(dirname(basePath . self::FixPath($path)) . '/' . $name)) {
                    return ['res' => 'ok', 'msg' => '']; //success
                }
            }

            $smarty->caching = false;
            $smarty->assign('dir', basename($path).' -> '.$name);
            $msg = $smarty->fetch('string:' . _fileman_error_rename_dir);
            return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
        }

        $smarty->caching = false;
        $smarty->assign('dir', basename($path));
        $msg = $smarty->fetch('string:' . _fileman_error_rename_dir_invalid_path);
        return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
    }

    /**
     * Kopiert einen Ordner samt Inhalt.
     * @return array
     */
    private function copydir()
    {
        if(empty($this->input['d'])) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_unknown)];
        }

        $path = str_replace('../', '/', $this->input['d']);

        //Protect ROOT-System
        if (!preg_match("/\_uploads_/", strtolower(self::FixPath($path)))) {
            exit();
        }

        if(empty($this->input['n'])) {
            $newPath = $this->upload_dir . '/_users_/_user'.common::$userid;
        } else {
            $newPath = str_replace('../', '/', $this->input['n']);
        }

        //Lock Root-Folder
        if (strtolower($dir=self::FixPath(basePath . $path)) == strtolower(self::FixPath(basePath . $this->upload_dir)) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_')) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_')) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_'))) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons_root)]; //error
        }

        //Lock Public-Folder for Users
        if(self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower(self::FixPath(basePath . $newPath)) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_'))) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        }

        //Only the admin can copy
        if ((preg_match("/\_user([1-9][0-9]*?)\_/", $path, $matches) || preg_match("/\_user([1-9][0-9]*?)\_/", $newPath, $matches))
            && self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_' . '/_user' . $matches[1] . '_'))) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        } unset($matches);

        //Only the admin can copy
        if ((preg_match("/\_group([1-9][0-9]*?)\_/", $path, $matches) || preg_match("/\_group([1-9][0-9]*?)\_/", $newPath, $matches))
            && self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_' . '/_group' . $matches[1] . '_'))) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        } unset($matches);

        $smarty = self::getSmarty(true);
        if (is_dir($dir)) {
            if ($this->copyDirAndFiles($path . '/', $newPath . '/' . basename($path))) {
                if(is_dir(basePath.$newPath . '/' . basename($path))) {
                    return ['res' => 'ok', 'msg' => '']; //success
                }
            }

            $smarty->caching = false;
            $smarty->assign('dir', basename($path).' -> '.$newPath);
            $msg = $smarty->fetch('string:' . _fileman_error_copy_dir);
            return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
        }

        $smarty->caching = false;
        if (!is_dir(self::FixPath(basePath . $path)))
            $smarty->assign('dir', basename($path));
        else
            $smarty->assign('dir', basename($newPath));

        $msg = $smarty->fetch('string:' . _fileman_error_copy_dir_invalid_path);
        return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
    }

    /**
     * Kopiert eine Datei.
     * @return array
     */
    private function copyfile()
    {
        if(empty($this->input['f']) || empty($this->input['n'])) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_unknown)];
        }

        $path = str_replace('../', '/', $this->input['f']);
        $newPath = str_replace('../', '/', $this->input['n']);
        $dir=self::FixPath(basePath . $path);

        //Protect ROOT-System
        if (!preg_match("/\_uploads_/", strtolower(self::FixPath($path))) ||
            !preg_match("/\_uploads_/", strtolower(self::FixPath($newPath)))) {
            exit();
        }

        //Lock Root-Folder
        if (strtolower($dir_new=self::FixPath(basePath . $newPath)) == strtolower(self::FixPath(basePath . $this->upload_dir))) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons_root)]; //error
        }

        //No Files in ROOT
        if (strtolower($dir_new) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_')) ||
            strtolower($dir_new) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_'))) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
        }

        //Lock Public-Folder for Users
        if(self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir_new) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_'))) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        }

        //Rename by Owner User or admin
        if (preg_match("/\_user([1-9][0-9]*?)\_/", $dir_new, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if ($matches[1] != self::$userid) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_cannot_delete_root)]; //error
            }
        } unset($matches);

        //Rename by Owner Groups or admin
        if(preg_match("/\_group([1-9][0-9]*?)\_/", $dir_new, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            //Permissions for Admin / User with ID or permission => 'fileman'
            if (!self::$sql['default']->rows("SELECT `id` FROM `{prefix_group_user}` WHERE `user` = ? AND `group` = ?;",
                [self::$userid, $matches[1]])) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        } unset($matches);

        $smarty = self::getSmarty(true);
        if (is_file($dir)) {
            if (!is_file(self::FixPath($dir_new. '/' .basename($path)))) {
                if (copy($dir, self::FixPath($dir_new. '/' .basename($path)))) {
                    if(is_file($dir_new. '/' .basename($path))) {
                        return ['res' => 'ok', 'msg' => '']; //success
                    }
                }

                $smarty->caching = false;
                $smarty->assign('dir', basename($path));
                $msg = $smarty->fetch('string:' . _fileman_error_copy_file);
                return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
            } else if(is_file(self::FixPath($dir_new. '/' .basename($path)))) {
                if($this->copyAndRename($dir, self::FixPath($dir_new. '/'), basename($path))) {
                    return ['res' => 'ok', 'msg' => '']; //success
                }

                $smarty->caching = false;
                $smarty->assign('dir', basename($path));
                $msg = $smarty->fetch('string:' . _fileman_error_copy_file);
                return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
            } else {
                $smarty->caching = false;
                if (!is_dir(self::FixPath(basePath . $path)))
                    $smarty->assign('dir', basename($path));
                else
                    $smarty->assign('dir', basename($newPath));

                $msg = $smarty->fetch('string:' . _fileman_error_copy_file_invalid_path);
                return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
            }
        }
    }

    /**
     * Löscht eine Datei
     * @return array
     */
    private function deletefile() {
        if(empty($this->input['f'])) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_unknown)];
        }

        $path = str_replace('../', '/', $this->input['f']);

        //Protect ROOT-System
        if (!preg_match("/\_uploads_/", strtolower(self::FixPath($path)))) {
            exit();
        }

        //Lock Root-Folder
        if (strtolower($dir=self::FixPath(basePath . $path)) == strtolower(self::FixPath(basePath . $this->upload_dir)) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_')) ||
            strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_'))) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons_root)]; //error
        }

        //Lock Public-Folder for Users
        if(self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_'))) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        }

        if (preg_match("/\_user([1-9][0-9]*?)\_/", $path, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_' . '/_user' . $matches[1] . '_')) &&
                $matches[1] != self::$userid) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        } unset($matches);

        if (preg_match("/\_group([1-9][0-9]*?)\_/", $path, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_' . '/_group' . $matches[1] . '_')) &&
                !self::$sql['default']->rows("SELECT `id` FROM `{prefix_group_user}` WHERE `user` = ? AND `group` = ?;",[self::$userid, $matches[1]])) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        } unset($matches);

        $smarty = self::getSmarty(true);
        if (is_file($dir)) {
            if(unlink($dir)) {
                if(!file_exists($dir)) {
                    return ['res' => 'ok', 'msg' => ''];
                }
            }

            $smarty->caching = false;
            $smarty->assign('dir', basename($path));
            $msg = $smarty->fetch('string:' . _fileman_error_cannot_delete_dir);
            return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
        }

        $smarty->caching = false;
        $smarty->assign('dir', basename($path));
        $msg = $smarty->fetch('string:' . _fileman_error_delete_dir_invalid_path);
        return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
    }

    /**
     * Verschiebt einen Ordner in ein anderes Verzeichnis.
     * @return array
     */
    private function movedir() {
        if(empty($this->input['d']) || empty($this->input['n'])) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_unknown)];
        }

        $path = str_replace('../', '/', $this->input['d']);
        $newPath = str_replace('../', '/', $this->input['n']);

        //Protect ROOT-System
        if (!preg_match("/\_uploads_/", strtolower(self::FixPath($path))) ||
            !preg_match("/\_uploads_/", strtolower(self::FixPath($newPath)))) {
            exit();
        }

        //Lock Root-Folder
        if ((strtolower($dir=self::FixPath(basePath . $path)) == strtolower(self::FixPath(basePath . $this->upload_dir))) ||
            (strtolower($new_dir=self::FixPath(basePath . $newPath)) == strtolower(self::FixPath(basePath . $this->upload_dir)) ||
            (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_'))) ||
            (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_'))) ||
            (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_'))) ||
            (strtolower($new_dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_'))) ||
            (strtolower($new_dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_'))) ||
            (strtolower($new_dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_'))))) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons_root)]; //error
        }

        //Cannot delete Users / only the admin can delete
        if (preg_match("/\_user([1-9][0-9]*?)\_/", $newPath, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($new_dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_' . '/_user' . $matches[1] . '_')) &&
                $matches[1] != self::$userid) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        } unset($matches);

        //Cannot delete Groups / only the admin can delete
        if (preg_match("/\_group([1-9][0-9]*?)\_/", $newPath, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($new_dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_' . '/_group' . $matches[1] . '_')) &&
                !self::$sql['default']->rows("SELECT `id` FROM `{prefix_group_user}` WHERE `user` = ? AND `group` = ?;",[self::$userid, $matches[1]])) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        } unset($matches);

        $smarty = self::getSmarty(true);
        if (is_dir($dir)) {
            if (is_dir($new_dir)) {
                if ($this->copyDirAndFiles($path . '/', $newPath . '/' . basename($path))) {
                    $this->deleteFolder($path . '/');
                    return ['res' => 'ok', 'msg' => '']; //success
                } else {
                    $smarty->caching = false;
                    $smarty->assign('dir', basename($path));
                    $msg = $smarty->fetch('string:' . _fileman_error_copy_dir);
                    return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
                }
            }

            $smarty->caching = false;
            if (!is_dir($dir))
                $smarty->assign('dir', basename($path));
            else
                $smarty->assign('dir', basename($newPath));

            $msg = $smarty->fetch('string:' . _fileman_error_copy_dir_invalid_path);
            return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
        }

        $smarty->caching = false;
        $smarty->assign('dir', basename($path));
        $msg = $smarty->fetch('string:' . _fileman_error_rename_file_invalid_path);
        return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
    }

    /**
     * Verschiebt eine Datei.
     * @return array
     */
    private function movefile() {
        if(empty($this->input['f']) || empty($this->input['n'])) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_unknown)];
        }

        $path = str_replace('../', '/', $this->input['f']);
        $newPath = str_replace('../', '/', $this->input['n']);
        $newPath = str_replace(basename($path),'',$newPath);

        //Protect ROOT-System
        if (!preg_match("/\_uploads_/", strtolower(self::FixPath($path))) ||
            !preg_match("/\_uploads_/", strtolower(self::FixPath($newPath)))) {
            exit();
        }

        //Lock Root-Folder
        if (strtolower($new_dir=self::FixPath(basePath . $newPath)) == strtolower(self::FixPath(basePath . $this->upload_dir).'/')) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons_root)]; //error
        }

        //No Files in ROOT
        if (strtolower($new_dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_/')) ||
            strtolower($new_dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_/'))) {
            return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
        }

        //Lock Root-Folders for User
        if(self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($new_dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_/'))) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
            }
        }

        //Cannot delete Users / only the admin can delete
        if (preg_match("/\_user([1-9][0-9]*?)\_/", $newPath, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if (self::FixPath(basePath . $newPath) == self::FixPath(basePath . $this->upload_dir . '/_users_' . '/_user' . $matches[1] . '_') &&
                $matches[1] != self::$userid) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_cannot_delete_root)]; //error
            }
        }

        //Cannot delete Groups / only the admin can delete
        if (preg_match("/\_group([1-9][0-9]*?)\_/", $newPath, $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if (self::FixPath(basePath . $newPath) == self::FixPath(basePath . $this->upload_dir . '/_group_' . '/_group' . $matches[1] . '_') &&
                !self::$sql['default']->rows("SELECT `id` FROM `{prefix_group_user}` WHERE `user` = ? AND `group` = ?;",[self::$userid, $matches[1]])) {
                return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_cannot_delete_root)]; //error
            }
        }

        $smarty = self::getSmarty(true);
        if (is_file(($file = self::FixPath(basePath . $path)))) {
            if (is_file(self::FixPath(basePath . $path))) {
                if (file_exists(basePath.$newPath.basename($path))) {
                    if($this->copyAndRename(basePath.$path, basePath.$newPath, basename($path)) && unlink(basePath.$path)) {
                        return ['res' => 'ok', 'msg' => '']; //success
                    }
                } else if(!file_exists(self::FixPath(basePath.$newPath.basename($path)))) {
                    if(copy(basePath . $path,basePath.$newPath.basename($path)) && unlink(basePath.$path)) {
                        return ['res' => 'ok', 'msg' => '']; //success
                    }
                }

                $smarty->caching = false;
                $smarty->assign('dir', basename($path));
                $msg = $smarty->fetch('string:' . _fileman_error_copy_dir);
                return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
            } else {
                $smarty->caching = false;
                $smarty->assign('dir', basename($path));
                $msg = $smarty->fetch('string:' . _fileman_error_copy_dir_invalid_path);
                return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
            }
        } else {
            $smarty->caching = false;
            $smarty->assign('dir', basename($path));
            $msg = $smarty->fetch('string:' . _fileman_error_rename_file_invalid_path);
            return ['res' => 'error', 'msg' => html_entity_decode($msg)]; //error
        }

        return ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_unknown)]; //error
    }

    /**
     * Downolad eines Ordners
     */
    private function downloaddir() {
        if(!class_exists('ZipArchive')){
            echo '<script>alert("Cannot create zip archive - ZipArchive class is missing. Check your PHP version and configuration");</script>';
        }

        if(empty($this->input['d'])) {
            echo '<script>alert("'.addslashes(_fileman_error_zip_creating).'");</script>';
        }

        //Lock Root-Folders for User
        if(self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir=self::FixPath(basePath.$this->input['d'])) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_')) ||
                strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/' ||
                strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_/')) ||
                strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_/'))))) {
                exit();
            }
        }

        //Protect ROOT-System
        if (!preg_match("/\_uploads_/", strtolower(self::FixPath($dir)))) {
            exit();
        }

        if (preg_match("/\_user([1-9][0-9]*?)\_/", $this->input['d'], $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if ($dir == self::FixPath(basePath . $this->upload_dir . '/_users_' . '/_user' . $matches[1] . '_') &&
                $matches[1] != self::$userid) {
                exit();
            }
        }

        if (preg_match("/\_group([1-9][0-9]*?)\_/", $this->input['d'], $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if ($dir == self::FixPath(basePath . $this->upload_dir . '/_group_' . '/_group' . $matches[1] . '_') &&
                !self::$sql['default']->rows("SELECT `id` FROM `{prefix_group_user}` WHERE `user` = ? AND `group` = ?;",[self::$userid, $matches[1]])) {
                exit();
            }
        }

        try{
            $filename = basename(self::FixPath($this->input['d']));
            $zipFile = $filename.'.zip';
            $zipPath = basePath.'/inc/_cache_/_fileman_/'.$zipFile;
            $this->ZipDir(self::FixPath($this->input['d']), $zipPath);

            header('Content-Disposition: attachment; filename="'.$zipFile.'"');
            header('Content-Type: application/force-download');
            readfile($zipPath);

            register_shutdown_function(array("fileman", "deleteTmp"),$zipPath);
        } catch(Exception $ex){
            echo '<script>alert("'.addslashes(_fileman_error_zip_creating).'");</script>';
        }

        exit();
    }

    /**
     * Downolad einer Datei
     */
    private function download() {
        if(empty($this->input['f'])) {
            exit();
        }

        $this->input['f'] = str_replace('../', '/', $this->input['f']);
        if(is_file(self::FixPath(basePath.$this->input['f']))){
            $file = urldecode(basename($this->input['f']));
            header('Content-Disposition: attachment; filename="'.$file.'"');
            header('Content-Type: application/force-download');
            readfile(basePath.self::FixPath($this->input['f']));
        }

        exit();
    }

    /**
     * Upload einer Datei
     */
    private function upload() {
        $isAjax = (array_key_exists('method',$this->input) && $this->input['method'] == 'ajax');
        $path = array_key_exists('d',$this->input) ? $this->input['d'] : $this->upload_dir . '/_public_';

        //Protect ROOT-System
        if (!preg_match("/\_uploads_/", strtolower(self::FixPath($path)))) {
            exit();
        }

        //Lock Root-Folder
        if ((strtolower($dir=self::FixPath(basePath . $path)) == strtolower(self::FixPath(basePath . $this->upload_dir))) ||
            (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_group_'))) ||
            (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_users_')))) {
            $res = ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons_root)]; //error
            if($isAjax){
                return $res;
            } else {
                exit('<script>parent.fileUploaded('.json_encode($res).');</script>');
            }
        }

        //Lock Root-Folders for User
        if(self::$chkMe != 4 && !self::permission('fileman')) {
            if (strtolower($dir) == strtolower(self::FixPath(basePath . $this->upload_dir . '/_public_'))) {
                $res = ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
                if($isAjax){
                    return $res;
                } else {
                    exit('<script>parent.fileUploaded('.json_encode($res).');</script>');
                }
            }
        }

        if (preg_match("/\_user([1-9][0-9]*?)\_/", $this->input['d'], $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if ($dir == self::FixPath(basePath . $this->upload_dir . '/_users_' . '/_user' . $matches[1] . '_') &&
                $matches[1] != self::$userid) {
                $res = ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
                if($isAjax){
                    return $res;
                } else {
                    exit('<script>parent.fileUploaded('.json_encode($res).');</script>');
                }
            }
        }

        if (preg_match("/\_group([1-9][0-9]*?)\_/", $this->input['d'], $matches) && self::$chkMe != 4 && !self::permission('fileman')) {
            if ($dir == self::FixPath(basePath . $this->upload_dir . '/_group_' . '/_group' . $matches[1] . '_') &&
                !self::$sql['default']->rows("SELECT `id` FROM `{prefix_group_user}` WHERE `user` = ? AND `group` = ?;",[self::$userid, $matches[1]])) {
                $res = ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_permissons)]; //error
                if($isAjax){
                    return $res;
                } else {
                    exit('<script>parent.fileUploaded('.json_encode($res).');</script>');
                }
            }
        }

        if(is_dir($dir)){
            if(!empty($_FILES['files']) && is_array($_FILES['files']['tmp_name'])) {
                $errors = $errorsExt = array();
                foreach($_FILES['files']['tmp_name'] as $key => $temp_file){
                    $filename = $_FILES['files']['name'][$key];
                    $filename = $this->MakeUniqueFilename(self::FixPath($path), $filename);
                    $filePath = self::fixPath(basePath.$path.'/'.$filename);

                    if(!$this->CanUploadFile($filename)){
                        $errorsExt[] = $filename;
                    } else if(!move_uploaded_file($temp_file, $filePath)){
                        $errors[] = $filename;
                    }

                    if(is_file($filePath)){
                        chmod($filePath, octdec(config::$upload_file_permissions));
                    }
                }

                if($errors && $errorsExt)
                    $res =  ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_upload_all).' '.html_entity_decode(_fileman_error_upload_extension)]; //error
                elseif($errorsExt)
                    $res =  ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_upload_extension)]; //error
                elseif($errors)
                    $res =  ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_upload_all)]; //error
                else
                    $res =  ['res' => 'ok', 'msg' => ''];
            }
        }
        else
            $res =  ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_upload_no_files)]; //error

        if($isAjax){
            if($errors && $errorsExt) {
                $res =  ['res' => 'error', 'msg' => html_entity_decode(_fileman_error_upload_all)]; //error
            }

            return $res;
        } else {
            exit('<script>parent.fileUploaded('.json_encode($res).');</script>');
        }
    }

    /**
     * Generate thumb
     */
    private function thumb() {
        $path = str_replace('../', '/',
            array_key_exists('f',$this->input) ? $this->input['f'] : '');

        if(!extension_loaded('gd'))
            die('"gd" extension not loaded');

        if(file_exists(basePath.$path) && $this->IsImage($path)) {
            $size = getimagesize(basePath . $path);
            $file_exp = explode('.', $file = urldecode(basename($path)));
            $breite = $size[0]; $hoehe = $size[1];

            $neueBreite = array_key_exists('width', $this->input) ? $this->input['width'] : 100;
            $neueHoehe = ((int)($hoehe * $neueBreite / $breite));

            @chmod(common::FixPath(basePath . dirname($path)), octdec(config::$upload_dir_permissions));
            @chmod(common::FixPath(basePath . $path), octdec(config::$upload_file_permissions));

            $file_cache = basePath . '/' . $file_exp[0] . '_fileman_minimize_' . $neueBreite . 'x' . $neueHoehe;
            $picture_build = false;

            switch ($size[2]) {
                case 1: ## GIF ##
                    header("Content-Type: image/gif");
                    $file_cache = $file_cache . '.gif';
                    if (!thumbgen_cache || !file_exists($file_cache) || time() - filemtime($file_cache) > thumbgen_cache_time) {
                        $altesBild = imagecreatefromgif(basePath . $path);
                        $neuesBild = imagecreatetruecolor($neueBreite, $neueHoehe);
                        $CT = imagecolortransparent($altesBild);
                        imagepalettecopy($neuesBild, $altesBild);
                        imagefill($neuesBild, 0, 0, $CT);
                        imagecolortransparent($neuesBild, $CT);
                        imageantialias($neuesBild, true);
                        imagecopyresampled($neuesBild, $altesBild, 0, 0, 0, 0, $neueBreite, $neueHoehe, $breite, $hoehe);
                        thumbgen_cache ? imagegif($neuesBild, $file_cache) : imagegif($neuesBild);
                        $picture_build = true;
                    }
                    break;
                default:
                case 2: ## JPEG ##
                    header("Content-Type: image/jpeg");
                    $file_cache = $file_cache . '.jpg';
                    if (!thumbgen_cache || !file_exists($file_cache) || time() - @filemtime($file_cache) > thumbgen_cache_time) {
                        $altesBild = imagecreatefromjpeg(basePath . $path);
                        $neuesBild = imagecreatetruecolor($neueBreite, $neueHoehe);
                        imageantialias($neuesBild, true);
                        imagecopyresampled($neuesBild, $altesBild, 0, 0, 0, 0, $neueBreite, $neueHoehe, $breite, $hoehe);
                        thumbgen_cache ? imagejpeg($neuesBild, $file_cache, 100) : imagejpeg($neuesBild, null, 100);
                        $picture_build = true;
                    }
                    break;
                case 3: ## PNG ##
                    header("Content-Type: image/png");
                    $file_cache = $file_cache . '.png';
                    if (!thumbgen_cache || !file_exists($file_cache) || time() - @filemtime($file_cache) > thumbgen_cache_time) {
                        header("Content-Type: image/png");
                        $altesBild = imagecreatefrompng(basePath . $path);
                        $neuesBild = imagecreatetruecolor($neueBreite, $neueHoehe);
                        imagealphablending($neuesBild, false);
                        imagesavealpha($neuesBild, true);
                        imageantialias($neuesBild, true);
                        imagecopyresampled($neuesBild, $altesBild, 0, 0, 0, 0, $neueBreite, $neueHoehe, $breite, $hoehe);
                        thumbgen_cache ? imagepng($neuesBild, $file_cache) : imagepng($neuesBild);
                        $picture_build = true;
                    }
                    break;
            }

            if ($picture_build && is_resource($altesBild)) {
                imagedestroy($altesBild);
            }

            if ($picture_build && is_resource($neuesBild)) {
                imagedestroy($neuesBild);
            }

            if (thumbgen_cache && file_exists($file_cache)) {
                echo file_get_contents($file_cache);
            }
        }

        exit();
    }

    /**
     * ###################################################
     *  CORE FUNCTIONS
     * ###################################################
     */

    /**
     * @param string $fileName
     * @return bool
     */
    private function IsImage(string $fileName) {
        $ext = $this->GetExtension($fileName);
        if (in_array($ext, self::SUPPORTED_PICTURE)) {
            $hash = md5($fileName);
            if (!self::$cache->AutoMemExists($hash) && config::$use_system_cache) {
                $file_info = getimagesize(basePath . $fileName);
                if (!self::$cache->AutoMemExists($hash) && config::$use_system_cache) {
                    self::$cache->AutoMemSet($hash, $file_info, cache::TIME_FILEMAN_IMG_STATS);
                }
            } else {
                $file_info = self::$cache->AutoMemGet($hash);
            }

            return (is_array($file_info) && in_array($file_info['mime'], config::$extensions));
        }

        return false;
    }

    /**
     * Returns file extension without dot
     * @param string $filename
     * @return string
     */
    private function GetExtension(string $filename) {
        $ext = '';
        if (mb_strrpos($filename, '.') !== false) {
            $ext = mb_substr($filename, mb_strrpos($filename, '.') + 1);
        }

        return strtolower($ext);
    }

    /**
     * @param string $ext_path
     * @return mixed|string
     */
    private function getFilesPath(string $ext_path = '') {
        $ret = (isset($_SESSION['fileman_path']) && !empty($_SESSION['fileman_path']) ? $_SESSION['fileman_path'] : '');
        if (empty($ret)) {
            $ret = self::FixPath(basePath . '/inc/_uploads_' . $ext_path);
            $tmp = self::GetServerVars('DOCUMENT_ROOT');

            if (substr($tmp, -1) == '/' || substr($tmp, -1) == '\\')
                $tmp = substr($tmp, 0, -1);

            $ret = str_replace(self::FixPath($tmp), '', $ret);
        }

        return $ret;
    }

    /**
     * @param string $fullPath
     * @param string $type
     * @return array
     */
    private function getFilesNumber(string $fullPath, string $type) {
        $files_c = 0;
        $dirs_c = 0;
        $files = self::get_files(basePath . $fullPath, false, false, [], true);
        foreach ($files as $file) {
            if (is_file(basePath . $fullPath . '/' . $file) &&
                ($type == 'file' || ($type == 'image' && $this->IsImage($file)))) {
                $files_c++;
            }

            if (is_dir(basePath . $fullPath . '/' . $file)) {
                $dirs_c++;
            }
        }

        return array('files' => $files_c, 'dirs' => $dirs_c);
    }

    /**
     * @param string $path
     * @param string $type
     */
    private function GetDirs(string $path, string $type)
    {
        $ret = $sort = array();
        $files = self::get_files(basePath . $path, true, false, [], true);
        foreach ($files as $file) {
            $fullPath = $path . '/' . $file;
            if (!is_dir(self::FixPath(basePath . $fullPath)))
                continue;

            if (self::$chkMe != 4 && !$this->is_user_dir && !$this->is_group_dir) {
                if (preg_match("/\_(.*?)\_/", $fullPath)) {
                    continue;
                }
            }

            //Show only user dir
            if ($this->is_user_dir && self::$chkMe != 4 && !self::permission('fileman')) {
                if (preg_match("/\_user([1-9][0-9]*?)\_/", $fullPath, $matches)) {
                    if ($matches[1] != self::$userid)
                        continue;
                }
            }

            //Show only group dir
            if ($this->is_group_dir && self::$chkMe != 4 && !self::permission('fileman')) {
                if (preg_match("/\_group([1-9][0-9]*?)\_/", $fullPath, $matches)) {
                    if (!self::$sql['default']->rows("SELECT `id` FROM `{prefix_group_user}` WHERE `user` = ? AND `group` = ?;",
                        [self::$userid, $matches[1]])
                    )
                        continue;
                }
            }

            $tmp = $this->getFilesNumber($fullPath, $type);
            $ret[$fullPath] = array('path' => $fullPath, 'files' => $tmp['files'], 'dirs' => $tmp['dirs']);
            $sort[$fullPath] = $file;
        }

        natcasesort($sort);
        foreach ($sort as $k => $v) {
            $tmp = $ret[$k];
            $name = null;
            if (mb_strrpos($tmp['path'], '/') !== false) {
                $ext = mb_substr($tmp['path'], mb_strrpos($tmp['path'], '/') + 1);
                if ($ext != '_group_' && preg_match("/\_group(.*?)\_/", $ext, $matches)) {
                    $get = self::$sql['default']->fetch("SELECT `name` FROM `{prefix_groups}` WHERE `id` = ?;", [$matches[1]]);
                    if (self::$sql['default']->rowCount()) {
                        $name = stringParser::decode($get['name']) . ' [ ' . (self::$chkMe == 4 ? 'GID:' . $matches[1] : _group) . ' ]';
                    }
                } else if ($ext != '_users_' && preg_match("/\_user(.*?)\_/", $ext, $matches)) {
                    $name = stringParser::decode(self::data('nick', (int)$matches[1])) . ' [ ' . (self::$chkMe == 4 ? 'UID:' . $matches[1] : _private) . ' ]';
                } else if (preg_match("/\_(.*?)\_/", $ext) && defined('_fileman' . $ext)) {
                    $name = constant('_fileman' . $ext);
                }
            }

            $this->buffer[] = ['p' => self::FixPath($tmp['path']), 'n' => $name, 'f' => $tmp['files'], 'd' => $tmp['dirs']];
            $this->GetDirs($tmp['path'], $type);
        }
    }

    /**
     * Loescht Dateien und Ordner innerhalb eines Ordners
     * @param string $file Pfad zum Ordner, welcher geloescht werden soll
     */
    public function deleteFolder(string $file)
    {
        @chmod(self::FixPath(basePath . $file), config::$upload_dir_permissions);
        if (is_dir(basePath . $file)) {
            $files = self::get_files(self::FixPath(basePath . $file));
            foreach ($files as $filename) {
                if(!$this->deletefolder($file . "/" . $filename)) {
                    return false;
                }
            }

            if(!rmdir(self::FixPath(basePath . $file))) {
                return false;
            }
        } else if (is_file(self::FixPath(basePath . $file))) {
            if(!unlink(self::FixPath(basePath . $file))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $path
     * @param string $newPath
     * @return bool
     */
    private function copyDirAndFiles(string $path, string $newPath)
    {
        $items = self::get_files(basePath . $path);
        if (empty($items) || !count($items))
            return false;

        if (!is_dir(basePath . $newPath)) {
            if (!mkdir(basePath . $newPath, octdec(config::$upload_dir_permissions))) {
                return false;
            }
        }

        foreach ($items as $item) {
            $oldPath = self::FixPath($path . '/' . $item);
            $tmpNewPath = self::FixPath($newPath . '/' . $item);
            if (is_file(basePath . $tmpNewPath)) {
                $tmpNewPath = str_replace(basename($tmpNewPath),'',$tmpNewPath);
                if(!$this->copyAndRename(basePath . $oldPath, basePath . $tmpNewPath, basename($oldPath))) {
                    return false;
                }
            } else if (is_dir(basePath . $oldPath)) {
                if(!$this->copyDirAndFiles($oldPath, $tmpNewPath)) {
                    return false;
                }
            } else {
                if(!copy(basePath . $oldPath, basePath . $tmpNewPath)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param string $path
     * @param string $newPath
     * @param string $filename
     * @param int $i
     * @return bool
     */
    private function copyAndRename(string $path, string $newPath, string $filename, $i=1) {
        $file = explode('.',$filename);
        $filename_new = $file[0].'('.$i.').'.$file[1];
        if(file_exists($newPath.$filename_new)) { $i++;
            $this->copyAndRename($path, $newPath, $filename, $i);
        }

        return copy(self::FixPath($path), self::FixPath($newPath. '/' .$filename_new));
    }

    private function ZipAddDir($path, $zipPath) {
        $zipPath = str_replace('//', '/', $zipPath);
        if ($zipPath && $zipPath != '/') {
            $this->zip->addEmptyDir($zipPath);
        }

        $files = self::get_files(basePath.$path);
        foreach ($files as $f) {
            $filePath = $path . '/' . $f;
            if (is_file(basePath.$filePath)) {
                $this->zip->addFile(basePath.$filePath, ($zipPath ? $zipPath . '/' : '') . $f);
            } elseif (is_dir(basePath.$filePath)) {
                $this->ZipAddDir($filePath, ($zipPath ? $zipPath . '/' : '') . $f);
            }
        }
    }

    private function ZipDir($path, $zipFile, $zipPath = '') {
        $this->zip = new ZipArchive();
        $this->zip->open($zipFile, ZIPARCHIVE::CREATE);
        $this->ZipAddDir($path, $zipPath);
        $this->zip->close();
    }

    public function deleteTmp($zipPath){
        if(is_file($zipPath)) {
            unlink($zipPath);
        }
    }

    /**
     * creates unique file name using $filename( " - Copy " and number is added if file already exists) in directory $dir
     *
     * @param string $dir
     * @param string $filename
     * @return string
     */
    private function MakeUniqueFilename($dir, $filename)
    {
        $dir .= '/';
        $dir = self::FixPath($dir . '/');
        $ext = $this->GetExtension($filename);
        $name = $this->GetName($filename);
        $name = $this->CleanupFilename($name);
        $name = mb_ereg_replace(' \\- Copy \\d+$', '', $name);
        if ($ext)
            $ext = '.' . $ext;
        if (!$name)
            $name = 'file';

        $i = 0;
        do {
            $temp = ($i > 0 ? $name . " - Copy $i" : $name) . $ext;
            $i++;
        } while (file_exists($dir . $temp));

        return $temp;
    }

    /**
     * Returns file name without extension
     *
     * @param string $filename
     * @return string
     */
    private function GetName($filename)
    {
        $tmp = mb_strpos($filename, '?');
        if ($tmp !== false)
            $filename = mb_substr($filename, 0, $tmp);
        $dotPos = mb_strrpos($filename, '.');
        if ($dotPos !== false)
            $name = mb_substr($filename, 0, $dotPos);
        else
            $name = $filename;

        return $name;
    }

    /**
     * Replaces any character that is not letter, digit or underscore from $filename with $sep
     *
     * @param string $filename
     * @param string $sep
     * @return string
     */
    private function CleanupFilename($filename, $sep = '_')
    {
        if (strpos($filename, '.')) {
            $ext = $this->GetExtension($filename);
            $name = $this->GetName($filename);
        } else {
            $ext = '';
            $name = $filename;
        }

        if (mb_strlen($name) > 32)
            $name = mb_substr($name, 0, 32);

        $str = mb_ereg_replace("[^\\w]", $sep, $name);
        $str = mb_ereg_replace("$sep+", $sep, $str) . ($ext ? '.' . $ext : '');

        return $str;
    }

    private function CanUploadFile($filename)
    {
        $ret = false;
        $forbidden = array_filter(preg_split('/[^\d\w]+/', strtolower(config::$upload_forbidden_uploads)));
        $allowed = array_filter(preg_split('/[^\d\w]+/', strtolower(config::$upload_allowed_uploads)));
        $ext = $this->GetExtension($filename);

        if ((empty($forbidden) || !in_array($ext, $forbidden)) && (empty($allowed) || in_array($ext, $allowed)))
            $ret = true;

        return $ret;
    }

    public static function CreateUserDir(int $uid) {
        if(!$uid) return;
        $dir = '/_uploads_/_users_/_user'.$uid.'_/';
        $folders = ['Images','Forum','Addons'];
        foreach ($folders as $folder) {
            @mkdir(basePath . $dir.$folder, config::$upload_dir_permissions, true);
        }
    }

    public static function RemoveUserDir(int $uid) {
        if(!$uid) return;
        $fileman = self::getInstance(true);
        return $fileman->deleteFolder('/_uploads_/_users_/_user'.$uid.'_');
    }

    public static function CreateGroupDir(int $gid) {
        if(!$gid) return;
        $dir = '/_uploads_/_group_/_group'.$gid.'_/';
        $folders = ['Documents','Images','Downloads'];
        foreach ($folders as $folder) {
            @mkdir(basePath . $dir.$folder, config::$upload_dir_permissions, true);
        }
    }

    public static function RemoveGroupDir(int $gid) {
        if(!$gid) return;
        $fileman = self::getInstance(true);
        return $fileman->deleteFolder('/_uploads_/_group_/_group'.$gid.'_');
    }
}