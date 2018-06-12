<?php
/*
 * DZCP - deV!L`z ClanPortal 1.6
 * http://www.dzcp.de
 *
 * This class is modified for DZCP 1.6 by Hammermaps.de
 * Original License by Moxiecode Systems AB
 */

/**
 * tiny_mce_gzip.php
 *
 * Copyright 2010, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

define('basePath', dirname(dirname(dirname(__FILE__))));
ob_start();

## Require ##
$ajaxJob = false;
include(basePath.'/vendor/autoload.php');
include(basePath."/inc/debugger.php");
include(basePath."/inc/config.php");
include(basePath."/inc/bbcode.php");
TinyMCE_Compressor::getParams();

// Handle incoming request if it's a script call
if(TinyMCE_Compressor::getParam("js")) {
    // Default settings
    $tinyMCECompressor = new TinyMCE_Compressor();

    // Handle request, compress and stream to client
    $tinyMCECompressor->handleRequest();
}

class TinyMCE_Compressor {
    private $files, $settings;
    private static $opt_params = array();
    private static $defaultSettings = array("plugins"    => "",
                                            "themes"     => "",
                                            "languages"  => "en",
                                            "disk_cache" => true,
                                            "core"       => true,
                                            "expires"    => "30d",
                                            "headers"    => false,
                                            "compress"   => true,
                                            "debug"      => false,
                                            "cc"         => true,
                                            "suffix"     => "",
                                            "files"      => "",
                                            "source"     => false);

    /**
     * Constructs a new compressor instance.
     *
     * @param Array $settings Name/value array with non-default settings for the compressor instance.
     */
    public function __construct($settings = array()) {
        $this->settings = array_merge(self::$defaultSettings, $settings);
    }

    /**
     * Adds a file to the concatenation/compression process.
     *
     * @param String $path Path to the file to include in the compressed package/output.
     */
    public function &addFile($file) {
        $this->files .= ($this->files ? "," : "") . $file;
        return $this;
    }

    /**
     * Handles the incoming HTTP request and sends back a compressed script depending on settings and client support.
     */
    public function handleRequest() {
        global $cache;
        $files = array(); $buffer = "";
        $this->settings["debug"] = (self::getParam("debug") && view_error_reporting);
        $expiresOffset = $this->parseTime($this->settings["expires"]);
        $tinymceDir = dirname(__FILE__);
        $CachedString = $cache->getItem(md5(implode($_GET)));
        if (!$this->settings["disk_cache"] || $this->settings["debug"] || is_null($CachedString->get())) {
            // Override settings with querystring params
            if ($plugins = self::getParam("plugins")) {
                $this->settings["plugins"] = $plugins;
            }
            $plugins = explode(',', $this->settings["plugins"]);

            if ($themes = self::getParam("themes")) {
                $this->settings["themes"] = $themes;
            }
            $themes = explode(',', $this->settings["themes"]);

            if ($languages = self::getParam("languages")) {
                $this->settings["languages"] = $languages;
            }
            $languages = explode(',', $this->settings["languages"]);

            if ($tagFiles = self::getParam("files")) {
                $this->settings["files"] = $tagFiles;
            } unset($tagFiles);

            if ($src = self::getParam("src")) {
                $this->settings["source"] = ($src === "true");
            } unset($src);
            
            if ($suffix = self::getParam("suffix")) {
                $this->settings["suffix"] = $suffix;
            } unset($suffix);
            
            //Set Cache
            $this->settings["disk_cache"] = self::getParam("disk_cache");
            
            //Set Compress
            $this->settings['compress'] = self::getParam("compress");
            
            //Set Core
            $this->settings['core'] = self::getParam("core");
            
            //Set Headers
            $this->settings['headers'] = self::getParam("headers");
            
            //Set Cache-Control
            $this->settings['cc'] = self::getParam("cc");

            if($this->settings["debug"]) {
                echo '<p>########################<br>Settings:<br>########################<p>';
                var_dump($this->settings);
            }
            
            // Add core
            if($this->settings["core"]) {
                $files[] = "tiny_mce";
            }
            
            foreach ($languages as $language) {
                $files[] = "langs/".$language;
            } unset($language);

            // Add plugins && languages
            foreach ($plugins as $plugin) {
                $files[] = "plugins/".$plugin."/editor_plugin";
                
                foreach ($languages as $language) {
                    $files[] = array("file"=>"plugins/".$plugin."/langs/".$language);
                }
            } unset($plugins, $plugin);

            // Add themes
            foreach ($themes as $theme) {
                $files[] = "themes/$theme/editor_template";
                foreach ($languages as $language) {
                    $files[] = "themes/".$theme."/langs/".$language;
                }
            } unset($themes, $theme);

            if($this->settings["debug"]) {
                echo '<p>########################<br>Files on Call:<br>########################<p>';
                var_export($files);
            }
            
            // Add any specified files.
            $allFiles = array_merge($files, explode(',', $this->settings['files']));
            $newallFiles = array();
            foreach ($allFiles as $id => $file) {
                if(empty($file)) continue; $lang = false;
                if(is_array($file)) {
                    $lang = true;
                    $file = $file['file'];
                }
                
                if (file_exists($file . ".js")) {
                    $file .= ".js";
                    $newallFiles[$id] = $file;
                } else if ($this->settings["source"] && file_exists($file . "_src.js")) {
                    $file .= "_src.js";
                    $newallFiles[$id] = $file;
                } else {
                    if(!$lang) {
                        $message = '#####################################################################'.EOL.
                        'Datum           = '.date("d.m.y H:i", time()).EOL.
                        'Message         = TinyMCE Files not found'.EOL.
                        'Compressed File = '.$file.'.js'.EOL.
                        'Source File     = '.$file.'_src.js'.EOL.
                        '#####################################################################'.EOL.EOL;
                        $fp = fopen(basePath."/inc/_logs/tinymce_compressor.log", "a+");
                        fwrite($fp, $message); fclose($fp);
                    }
                }
            } unset($allFiles, $file);

            if($this->settings["debug"]) {
                echo '<p>########################<br>Files for Load:<br>########################<p>';
                var_export($newallFiles);
            }
            
            // Set base URL for where tinymce is loaded from
            $buffer = "var tinyMCEPreInit={base:'" . dirname(GetServerVars("SCRIPT_NAME")) . "',suffix:'".$this->settings["suffix"]."'};";

            if($this->settings["debug"]) {
                echo '<p>########################<br>Files Loaded:<br>########################<p>';
            }
            
            // Load all tinymce script files into buffer
            foreach ($newallFiles as $file) {
                if (file_exists($tinymceDir . "/" . $file)) {
                    $fileContents = $this->getFileContents($tinymceDir . "/" . $file);
                    if($this->settings["debug"]) {
                        echo 'File:'.$file.' -> Content: "'.strlen($fileContents).'" characters<br>';
                    }
                    $buffer .= $fileContents;
                } else {
                    if($this->settings["debug"]) {
                        echo 'File not found:'.$file.'<br>';
                    }
                    $message = '#####################################################################'.EOL.
                    'Datum           = '.date("d.m.y H:i", time()).EOL.
                    'Message         = TinyMCE Files not found'.EOL.
                    'File            = '.$tinymceDir."/".$file.EOL.
                    '#####################################################################'.EOL.EOL;
                    $fp = fopen(basePath."/inc/_logs/tinymce_compressor.log", "a+");
                    fwrite($fp, $message); fclose($fp);
                }
            }

            // Mark all themes, plugins and languages as done
            $buffer .= 'tinymce.each("' . implode(',', $files) . '".split(","),function(f){tinymce.ScriptLoader.markDone(tinyMCE.baseURL+"/"+f+".js");});';
           
            if($this->settings["disk_cache"]) {
                $CachedString->set($buffer)->expiresAfter($expiresOffset);
                $cache->save($CachedString);
            }
        } else {
            $buffer = $CachedString->get();
        }

        if($this->settings["debug"]) {
            echo '<p>########################<br>Buffer Loaded:<br>########################<p>';
            echo 'Buffer has "'.strlen($buffer).'" total characters!<br>';
            echo '<br>Buffer content:<br>'.$buffer.'<br>';
        }
        
        // Check if it supports gzip
        $zlibOn = ini_get('zlib.output_compression') || (ini_set('zlib.output_compression', 0) === false);
        $encodings = GetServerVars('HTTP_ACCEPT_ENCODING') ? strtolower(GetServerVars('HTTP_ACCEPT_ENCODING')) : "";
        $encoding = preg_match( '/\b(x-gzip|gzip)\b/', $encodings, $match) ? $match[1] : "";
        
        // Is northon antivirus header
        if (GetServerVars('---------------')) {
            $encoding = "x-gzip";
        }
        
        $supportsGzip = !empty($encoding) && !$zlibOn && function_exists('gzencode');
        
        // Set headers
        if(!$this->settings["debug"] && $this->settings['headers']) {
            header("Content-type: text/javascript");
            if($this->settings['cc']) {
                header("Vary: Accept-Encoding");  // Handle proxies
                header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");
                header("Cache-Control: public, max-age=" . $expiresOffset);
            } else {
                header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
            }
        } else {
            if($this->settings['headers']) {
                echo '<p>########################<br>Headers:<br>########################<p>';
                echo "Content-type: text/javascript";
                if($this->settings['cc']) {
                    echo "<br>Vary: Accept-Encoding";
                    echo "<br>Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT";
                    echo "<br>Cache-Control: public, max-age=" . $expiresOffset;
                } else {
                    echo "<br>Expires: " . gmdate("D, d M Y H:i:s") . " GMT";
                    echo "<br>Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT";
                    echo "<br>Cache-Control: no-store, no-cache, must-revalidate";
                    echo "<br>Cache-Control: post-check=0, pre-check=0";
                    echo "<br>Pragma: no-cache";
                }

                if ($supportsGzip && $this->settings['compress']) {
                    echo "<br>Content-Encoding: " . $encoding;
                }
            }
        }
        
        if($this->settings["debug"]) {
            echo '<p>########################<br>GZIP Compression:<br>########################<p>';
            echo 'Send Northon Antivirus Header: '.(GetServerVars('---------------') ? 'yes' : 'no');
            echo '<br>GZIP Compression Support: '.($supportsGzip ? 'yes' : 'no');
            echo '<br>GZIP Compression Support by Webbrowser: '.($encoding ? 'yes' : 'no');
        }
        
        if ($supportsGzip && $this->settings['compress'] && $this->settings['headers']) {
            if(!$this->settings["debug"]) {
                header("Content-Encoding: " . $encoding);
            }
            
            $buffer = gzencode($buffer, 9, FORCE_GZIP);
            if($this->settings["debug"]) {
                echo '<br>GZIP Content:<br>'.$buffer;
            }
        }
        
        if($this->settings["debug"]) {
            exit('</pre>');
        } else {
            exit($buffer);
        }
    }

    /**
     * Returns a sanitized query string parameter.
     *
     * @param String $name Name of the query string param to get.
     * @param String $default Default value if the query string item shouldn't exist.
     * @return String Sanitized query string parameter value.
     */
    public static function getParam($name, $default = "") {
        if(!array_key_exists($name, self::$opt_params)) {
            if(empty($default) && !array_key_exists($name, self::$defaultSettings)) {
               return $default;
            }
            
            return self::$defaultSettings[$name];
        }

        return self::$opt_params[$name];
    }
    
    public static function getParams() { //Load Params
        $bolean_index = array('js','diskcache','core','compress','src','debug','headers','cc');
        foreach($_GET as $key => $param) {
            if(in_array($key, $bolean_index)) {
                self::$opt_params[$key] = ((trim($_GET[$key]) == 'true' || trim($_GET[$key]) == '1' || trim($_GET[$key]) == 1) ? true : false);
            } else {
                self::$opt_params[$key] = preg_replace("/[^0-9a-z\-_,]+/i", "", $param); // Sanatize for security, remove anything but 0-9,a-z,-_,
            }
        }
        
        if(self::$opt_params["debug"]) {
            echo '<pre>';
            echo '########################<br>Params:<br>########################<p>';
            var_dump($_GET);
        }
    }

    /**
     * Parses the specified time format into seconds. Supports formats like 10h, 10d, 10m.
     *
     * @param String $time Time format to convert into seconds.
     * @return Int Number of seconds for the specified format.
     */
    private function parseTime($time) {
        $multipel = 1;

        // Hours
        if (strpos($time, "h") > 0) {
            $multipel = 3600;
        }

        // Days
        if (strpos($time, "d") > 0) {
            $multipel = 86400;
        }

        // Months
        if (strpos($time, "m") > 0) {
            $multipel = 2592000;
        }

        // Trim string
        return (int)($time) * $multipel;
    }

    /**
     * Returns the contents of the script file if it exists and removes the UTF-8 BOM header if it exists.
     *
     * @param String $file File to load.
     * @return String File contents or empty string if it doesn't exist.
     */
    private function getFileContents($file) {
        $content = file_get_contents($file);

        // Remove UTF-8 BOM
        if (substr($content, 0, 3) === pack("CCC", 0xef, 0xbb, 0xbf)) {
            $content = substr($content, 3);
        }

        return $content;
    }
}
ob_end_flush();