<?php
/**
 * DZCP - deV!L`z ClanPortal - Server ( api.dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * DZCP - deV!L`z ClanPortal - Server
 * Homepage: https://www.dzcp.de
 * E-Mail: lbrucksch@hammermaps.de
 * Author Lucas Brucksch
 * Copyright 2021 © Codedesigns
 */

use Ifsnop\Mysqldump\Mysqldump;
use Phpfastcache\Exceptions\PhpfastcacheDriverCheckException;
use Phpfastcache\Exceptions\PhpfastcacheDriverException;
use Phpfastcache\Exceptions\PhpfastcacheDriverNotFoundException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use phpFastCache\Exceptions\phpFastCacheInvalidConfigurationException;

use Ifsnop\Mysqldump as IMysqldump;

/**
 * Class BaseSystem
 */
class BaseSystem extends BaseSystemAbstract {
    /**
     * @var BaseEventAbstract|null
     */
    private ?BaseEventAbstract $event;

    /**
     * @var string
     */
    private string $event_name;

    const IPV6_NULL_ADDR = '::';
    const IPV4_NULL_ADDR = '0.0.0.0';

    /**
     * BaseSystem constructor.
     * @param bool $cronjob
     * @throws ReflectionException|\Phpfastcache\Exceptions\PhpfastcacheLogicException
     */
    public function __construct(bool $cronjob = false) {

        try {
            parent::__construct($cronjob);
        } catch (PhpfastcacheDriverCheckException | PhpfastcacheDriverException | PhpfastcacheDriverNotFoundException
        | PhpfastcacheInvalidArgumentException | phpFastCacheInvalidConfigurationException $e) {
            $this->getSystemLogger()->critical('PhpfastcacheException:',$e);
        }

        $this->event = null;
        $this->event_name = '';
    }

    /**
     * Call Events
     */
    public function __run(): void {
        parent::__run();

        require_once(SCRIPT_PATH.'/system/BaseEventInterface.php');
        require_once(SCRIPT_PATH.'/system/BaseEventAbstract.php');

        if($this->isCronjob()) {
            $this->setInput(['event'=>'Cronjob']);
        }

        //Filter Input
        $this->getGump()->validation_rules(['event' => 'required|alpha_numeric|min_len,2']);
        $this->getGump()->filter_rules(['event' => 'trim|sanitize_string']);

        $input = $this->getGump()->run($this->getInput());
        if ($input !== false) {
            /** @var TYPE_NAME $eventname */
            $eventname = 'Event'.ucfirst(strtolower($input['event']));
            $this->getSystemLogger()->debug('Call Event: '.$eventname);
            if($this->getFilesystem()->exists(SCRIPT_PATH.'/events/'.$eventname.'.php')) {
                if($eventname == 'EventCronjob' && !$this->isCronjob()) {
                    return;
                }

                require_once(SCRIPT_PATH.'/events/'.$eventname.'.php');
                $this->event_name = $eventname;
                $this->event = new $eventname($this);
                $this->event->__run();
                $this->event->__output();
            }
        }
    }

    /**
     * __shutdown
     */
    public function __shutdown(): void {
        if($this->event instanceof BaseEventAbstract) {
            $this->event->__shutdown();
        }

        parent::__shutdown();
    }

    /**
     * Funktion um Passwoerter generieren zu lassen
     * @param int $passwordLength (optional)
     * @param bool $specialcars (optional)
     * @param bool $numbers (optional)
     * @param bool $letters (optional)
     * @return string
     */
    public function mkPWD(int $passwordLength=8,bool $specialcars=true,bool $numbers = true,bool $letters = true): string {
        $passwordComponents = ["ABCDEFGHIJKLMNOPQRSTUVWXYZ" , "abcdefghijklmnopqrstuvwxyz" , "0123456789" , "#$@!"];
        if(!$specialcars) { unset($passwordComponents[3]); }
        if(!$numbers) { unset($passwordComponents[2]); }
        if(!$letters) { unset($passwordComponents[0]); unset($passwordComponents[1]); }

        $componentsCount = count($passwordComponents);
        shuffle($passwordComponents); $password = '';
        for ($pos = 0; $pos < $passwordLength; $pos++) {
            $componentIndex = ($pos % $componentsCount);
            $componentLength = strlen($passwordComponents[$componentIndex]);
            $random = rand(0, $componentLength-1);
            $password .= $passwordComponents[$componentIndex][$random];
        }

        unset($random,$componentLength,$componentIndex);
        return $password;
    }

    /**
     * Gibt Informationen uber Server und Ausfuhrungsumgebung zuruck
     * @param string $var
     * @return string
     */
    public function GetServerVars(string $var) {
        if (array_key_exists($var, $_SERVER) && !empty($_SERVER[$var])) {
            return utf8_encode($_SERVER[$var]);
        } else if (array_key_exists($var, $_ENV) && !empty($_ENV[$var])) {
            return utf8_encode($_ENV[$var]);
        }

        if($var=='HTTP_REFERER') { //Fix for empty HTTP_REFERER
            return $this->GetServerVars('REQUEST_SCHEME').'://'.$this->GetServerVars('HTTP_HOST').
                $this->GetServerVars('DOCUMENT_URI');
        }

        return false;
    }

    public function getClientIP(): array {
        $SetIP = ['v4' => self::IPV4_NULL_ADDR, 'v6' => self::IPV6_NULL_ADDR];
        $ServerVars = ['REMOTE_ADDR','HTTP_CLIENT_IP','HTTP_X_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR','HTTP_FORWARDED','HTTP_VIA','HTTP_X_COMING_FROM','HTTP_COMING_FROM'];
        foreach ($ServerVars as $ServerVar) {
            if($IP=$this->detectIP($ServerVar)) {
                //IP-Version 4
                if($this->isIP($IP, false)) {
                    $SetIP[$this->isIP($IP, false) ? 'v4' : 'v6'] = $IP;
                }

                //IP-Version 6
                if($this->isIP($IP, true)) {
                    $SetIP['v6'] = $IP;
                }
            }
        }

        return $SetIP;
    }

    /**
     * @param $var
     * @return bool|string
     */
    public function detectIP($var) {
        if(!empty($var) && ($REMOTE_ADDR = $this->GetServerVars($var)) && !empty($REMOTE_ADDR)) {
            $REMOTE_ADDR = trim($REMOTE_ADDR);
            if ($this->isIP($REMOTE_ADDR) || $this->isIP($REMOTE_ADDR, true)) {
                return $REMOTE_ADDR;
            }
        }

        return false;
    }

    /**
     * Check given ip for ipv6 or ipv4.
     * @param    string        $ip
     * @param    boolean       $v6 (optional)
     * @return   boolean
     */
    public static function isIP(string $ip,bool $v6=false) {
        if(!$v6) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? true : false;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? true : false;
    }

    /**
     * @param string $url
     * @param bool $post
     * @return string
     */
    public function getExternalContents(string $url, $post = false): string {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER , true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $cookie = tmpfile();
        curl_setopt($curl, CURLOPT_COOKIEFILE , $cookie);
        curl_setopt($curl, CURLOPT_COOKIEJAR , $cookie);
        curl_setopt( $curl, CURLOPT_USERAGENT, USER_AGENT);

        if($post && count($post) >= 1) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }

        $output = curl_exec($curl);
        curl_close($curl);
        return strval($output);
    }

    /**
     * Codiert Text in das UTF8 Charset.
     * @param string $txt
     * @param bool $htmlentities
     * @return false|string|string[]
     */
    public function encodeText(string $txt='',bool $htmlentities=true): string {
        $txt = str_replace(["\r\n", "\r", "\n"], ["[nr]","[r]","[n]"], $txt);
        $txt = ($htmlentities ? htmlentities($txt, ENT_COMPAT, 'UTF-8') : $txt);
        $txt = utf8_encode($txt);
        return $txt;
    }

    /**
     * Decodiert UTF8 Text in das aktuelle Charset der Seite.
     * @param string|null $txt
     * @return false|string|string[]
     */
    public function decodeText(?string $txt=''): string {
        if(is_null($txt) || empty($txt))
            return '';

        $txt = utf8_decode($txt);
        $txt = html_entity_decode($txt, ENT_COMPAT, 'UTF-8');
        $txt = str_replace(['[nr]','[r]','[n]'],["\r\n","\r","\n"], $txt);
        return $txt;
    }

    /**
     * Get an array that represents directory tree
     * @param string $rootDir
     * @param bool $absolutePath
     * @return array
     */
    public function scanDirectory(string $rootDir, bool $absolutePath = false): array
    {
        $invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
        $dirContent = scandir($rootDir); $allData = [];
        foreach($dirContent as $key => $content) {
            $path = $rootDir.'/'.$content;
            if(!in_array($content, $invisibleFileNames)) {
                if(is_file($path) && is_readable($path)) {
                    $allData[] = $absolutePath ? $path : $content;
                }
            }
        }

        return $allData;
    }

    /**
     * @param string $filename
     * @param array $tables
     * @param array $sql_connection
     */
    public function makeMySQLDump(string $filename, array $tables = [], array $sql_connection = []): void
    {
        $dumpSettingsDefault = array(
            'add-drop-table' => true,
            'include-tables' => $tables,
        );

        try {
            $dump = new IMysqldump\Mysqldump(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $dumpSettingsDefault);
            if(count($sql_connection) == 3){
                $dump = new IMysqldump\Mysqldump($sql_connection['dsn'],
                    $sql_connection['username'], $sql_connection['password'],
                    $dumpSettingsDefault);
            }
            $dump->start($filename);
            $this->getDatabaseLogger()->info('MySQLDump "'.$filename.'" has created!');
        } catch (\Exception $e) {
            $this->getSystemLogger()->critical('MysqldumpException:',$e);
        }
    }

    /**
     * @param string $zipFile
     * @param array $files
     */
    public function zipFiles(string $zipFile,array $files=[]) {
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE)!==TRUE) {
            exit("cannot open <$zipFile>\n");
        }

        foreach ($files as $name => $file) {
            $zip->addFile($file,$name);
        }
        $zip->close();
    }

    /**
     * @param string $event_name
     */
    public function setEventName(string $event_name): void
    {
        $this->event_name = $event_name;
    }

    /**
     * @return BaseEventAbstract
     */
    public function getEvent(): BaseEventAbstract
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->event_name;
    }
}