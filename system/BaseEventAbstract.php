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

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Phpfastcache\Core\Item\ExtendedCacheItemInterface;

/**
 * Class BaseEventAbstract
 */
class BaseEventAbstract implements BaseEventInterface
{
    /**
     * @var BaseSystem
     */
    private BaseSystem $baseSystem;

    /**
     * @var array
     */
    private array $content;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var string
     */
    private $content_type;

    /**
     * @var string
     */
    private string $event_call;

    /**
     * @var string
     */
    private string $event_dir;

    /**
     * @var string
     */
    private string $event_store_dir;

    /**
     * @var string
     */
    private string $event_lib_dir;

    /**
     * @var string
     */
    private string $event_logs_dir;

    /**
     * @var bool
     */
    private bool $use_cert;

    /**
     * @var int
     */
    private int $cert_id;

    /**
     * @var bool
     */
    private bool $redirect;

    /**
     * @var string
     */
    private string $cert;

    /**
     * @var bool
     */
    private bool $reload;

    /**
     * @var int
     */
    private int $event_cache_time;

    /**
     * @var bool
     */
    private bool $compress;

    /**
     * BaseEventAbstract constructor.
     * @param BaseSystem $baseSystem
     * @throws Exception
     */
    public function __construct(BaseSystem $baseSystem) {
        $this->baseSystem = $baseSystem;

        $this->getBaseSystem()->getSystemLogger()->debug('GUMP: Input',$this->getBaseSystem()->getInput());

        $this->content = ['results' => [], 'status' => 'not found', 'code' => 404, 'error' => true];
        $this->content_type = self::JSON;

        $this->logger = new Logger('Event');

        $this->event_call = '';
        $this->event_dir = '';
        $this->event_store_dir = '';
        $this->event_lib_dir = '';
        $this->event_logs_dir = '';
        $this->event_cache_time = 60;

        $this->cert = '';
        $this->cert_id = 0; //API-Server is 0

        $this->use_cert = false;
        $this->redirect = false;
        $this->reload = false;
        $this->compress = false;

        //force reload the cache
        $this->getBaseSystem()->getGump()->validation_rules(['reload' => 'required|min_len,1']);

        $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
        if ($input !== false) {
            if(!empty($input['reload']) &&
                $input['reload']) {
                $this->reload = true;
            }
        }

        //Use call
        $this->getBaseSystem()->getGump()->validation_rules(['call' => 'required|alpha_numeric|min_len,2']);
        $this->getBaseSystem()->getGump()->filter_rules(['call' => 'trim|sanitize_string']);

        $event_call = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
        if ($event_call !== false) {
            $this->event_call = lcfirst($event_call['call']);
        }
    }

    /**
     * Output the Data
     */
    public function __output(): void {
        if($this->getBaseSystem()->isCronjob()) {
            return;
        }

        if($this->getContentType() != self::GZIP) {
            /** @var TYPE_NAME $serializer */
            $serializer = JMS\Serializer\SerializerBuilder::create();
            $serializer->setCacheDir(SCRIPT_PATH . '/cache');
            $serializer->setDebug(DEBUG_SERIALIZER);

            $this->content += ['results' => [], 'status' => 'ok', 'code' => 200, 'error' => false];
            if ($this->compress) {
                foreach ($this->content['results'] as $key => $result) {
                    $this->content['results'][$key] = bin2hex(gzcompress(json_encode($result)));
                }
                $this->content['results']['compress'] = true;
            }
            $this->content += ['crc32' => crc32(serialize($this->content['results']))];
        }

        switch ($this->getContentType()) {
            case self::JSONP:
                $callback = array_key_exists('callback',$this->getBaseSystem()->getInput()) ?
                    preg_replace('/[^a-zA-Z0-9$_.]/s', '', $this->getBaseSystem()->getInput()['callback']) : false;
                header('Content-Type: '.($callback ? 'application/javascript' : 'application/json') .';charset=UTF-8');
                header('Access-Control-Allow-Origin: *');
                echo ($callback ? $callback . '(' : '') . $serializer->build()->serialize($this->getContent(), 'json'). ($callback ? ')' : '');
                break;
            case self::GZIP:
                header('Content-Type: application/gzip');
                header('Content-Encoding: gzip');
                header('Access-Control-Allow-Origin: *');
                echo gzinflate($this->getContent()['results']);
                break;
            case self::JSON:
            default:
                header('Content-Type: application/json;charset=UTF-8');
                header('Access-Control-Allow-Origin: *');
                echo utf8_encode($serializer->build()->serialize($this->getContent(), 'json'));
                break;
        }
    }

    public function __run(): void {
        //Set format for response
        $this->getBaseSystem()->getGump()->validation_rules(['format' => 'required|alpha_numeric|min_len,4']);
        $this->getBaseSystem()->getGump()->filter_rules(['format' => 'trim|sanitize_string']);

        $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
        if ($input !== false) {
            switch (strtolower($input['format'])) {
                case 'jsonp':
                    $this->setContentType(self::JSONP);
                    break;
                default:
                case 'json':
                    $this->setContentType(self::JSON);
                    break;
            }
        }

        $dir = SCRIPT_PATH.'/events/'.$this->getBaseSystem()->getEventName();
        if($this->getBaseSystem()->getFilesystem()->exists($dir) &&
            $this->getBaseSystem()->getFilesystem()->isAbsolutePath($dir)) {

            //Event Dirs
            $this->event_dir = $dir;
            $this->event_lib_dir = $this->getEventDir().'/lib';
            $this->event_store_dir = $this->getEventDir().'/store';
            $this->event_logs_dir = $this->getEventDir().'/logs';

            //Create EventDir
            if(!is_dir($this->getEventDir())) {
                $this->getBaseSystem()->getFilesystem()->mkdir($this->getEventDir());
            }

            //Generate Logger
            if(is_dir($this->getEventLogsDir())) {
                if(!$this->getBaseSystem()->isDZCPClient()) {
                    try {
                        $this->logger->pushHandler(new StreamHandler($this->getEventLogsDir() . '/event.log'), Logger::DEBUG);
                    } catch (Exception $e) {
                        $this->getBaseSystem()->getSystemLogger()->critical('BaseEvent-Error: ' . $e->getMessage(), $e);
                    }
                }
            } else {
                $this->getBaseSystem()->getFilesystem()->mkdir($this->getEventLogsDir());
            }

            //Load Libs
            if(is_dir($this->getEventLibDir())) {
                foreach ($this->getBaseSystem()->scanDirectory($this->event_lib_dir) as $lib) {
                    if($this->getBaseSystem()->getFilesystem()->exists($this->event_lib_dir.'/'.$lib) &&
                        $this->getBaseSystem()->getFilesystem()->isAbsolutePath($this->event_lib_dir.'/'.$lib)) {
                        require_once($this->event_lib_dir.'/'.$lib);
                    }
                }
            } else {
                $this->getBaseSystem()->getFilesystem()->mkdir($this->getEventLibDir());
            }

            //To Compress
            $this->getBaseSystem()->getGump()->validation_rules(['compress' => 'required|alpha_numeric|min_len,1']);
            $this->getBaseSystem()->getGump()->filter_rules(['compress' => 'trim']);

            $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
            if ($input !== false) {
                $this->setCompress((bool)$input['compress']);
            }

            //Cert
            if($this->use_cert && (!array_key_exists('cert',$this->getBaseSystem()->getInput()) ||
                    empty($this->getBaseSystem()->getInput()['cert']) || !$this->checkCert())) {
                $this->setContentType(BaseEventInterface::JSON);
                $this->setContent(["results" => [],"status" => "forbidden","code" => 403,"error" => true]);
                $this->redirect = true;
                return;
            }
        }
    }

    public function __shutdown(): void {
        $this->logger->close();
        unset($this->logger);
    }

    /**
     * @return bool
     */
    private function checkCert(): bool
    {
        $this->cert = strtolower($this->getBaseSystem()->getInput()['cert']);

        $row = $this->getBaseSystem()->getDatabase()->fetch(
            'SELECT `id`,`ipv4`,`ipv6` FROM `dzcp_server_certs` WHERE `indent` = ? AND `enabled` = 1;', $this->cert);

        if(is_null($row)) {
            return false;
        }

        //Check IP Version 4
        if($row->ipv4 != '0.0.0.0' && $row->ipv4 != $this->getBaseSystem()->getClientIP()['v4']) {
            return false;
        }

        //Check IP Version 6
        /** @var TYPE_NAME $row */
        if($row->ipv6 != '::' && $row->ipv6 != $this->getBaseSystem()->getClientIP()['v6']) {
            return false;
        }

        $this->cert_id = $row->id;

        //Update Time
        $this->getBaseSystem()->getDatabase()->query('UPDATE `dzcp_server_certs` SET `time` = ? WHERE `id` = ?; ', time() ,$row->id);

        return true;
    }

    /**
     * @return BaseSystem
     */
    public function getBaseSystem(): BaseSystem
    {
        return $this->baseSystem;
    }

    /**
     * @param array $content
     */
    public function setContent(array $content): void
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param int $content_type
     */
    public function setContentType(int $content_type): void
    {
        $this->content_type = $content_type;
    }

    /**
     * @return int
     */
    public function getContentType(): int
    {
        return $this->content_type;
    }

    /**
     * @return string
     */
    public function getEventDir(): string
    {
        return $this->event_dir;
    }

    /**
     * @return string
     */
    public function getEventCall(): string
    {
        return $this->event_call;
    }

    /**
     * @return string
     */
    public function getEventLibDir(): string
    {
        return $this->event_lib_dir;
    }

    /**
     * @return string
     */
    public function getEventStoreDir(): string
    {
        return $this->event_store_dir;
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @return string
     */
    public function getEventLogsDir(): string
    {
        return $this->event_logs_dir;
    }

    /**
     * @return bool
     */
    public function isRedirect(): bool
    {
        return $this->redirect;
    }

    /**
     * @param bool $cert
     */
    public function useCert(bool $cert = false): void
    {
        $this->use_cert = $cert;
    }

    /**
     * @return string
     */
    public function getCert(): string
    {
        return $this->cert;
    }

    /**
     * @return int
     */
    public function getCertId(): int
    {
        return $this->cert_id;
    }

    /**
     * @return bool
     */
    public function isReload(): bool
    {
        return $this->reload;
    }

    /**
     * @return int
     */
    public function getEventCacheTime(): int
    {
        return $this->event_cache_time;
    }

    /**
     * @param int $event_cache_time
     */
    public function setEventCacheTime(int $event_cache_time): void {
        $this->event_cache_time = $event_cache_time;
    }

    /**
     * @param bool $compress
     */
    public function setCompress(bool $compress): void {
        $this->compress = $compress;
    }

    /**
     * @param ExtendedCacheItemInterface $CachedServer
     * @return bool
     */
    public function isCached(ExtendedCacheItemInterface $CachedServer): bool {
        if(is_null($CachedServer->get()))
            return false;

        if(empty($CachedServer->get()))
            return false;

        if($this->isReload())
            return false;

        return true;
    }
}