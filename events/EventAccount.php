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

/**
 * Class EventAccount
 */
class EventAccount extends BaseEventAbstract {
    /**
     * @var int
     */
    private $uid;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var int
     */
    private int $balance;

    /**
     * @var int
     */
    private int $revenue;

    /**
     * @var int
     */
    private int $expenditure;

    /**
     * @var string
     */
    private string $user_log;

    /**
     * @var Logger
     */
    private Logger $account_logger;

    /**
     * EventAccount constructor.
     * @param BaseSystem $baseSystem
     * @throws Exception
     */
    public function __construct(BaseSystem $baseSystem) {
        try {
            parent::__construct($baseSystem);
        } catch (Exception $e) {
            exit();
        }

        $this->useCert(true);

        $this->getBaseSystem()->getGump()->validation_rules(['uid' => 'required|numeric|min_len,1']);
        $this->getBaseSystem()->getGump()->filter_rules(['uid' => 'sanitize_numbers']);

        $this->uid = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());

        $this->getLogger()->pushHandler(new StreamHandler(LOG_PATH.'/'.__CLASS__.'.log',
            DEBUG ? Logger::DEBUG : Logger::WARNING));

        $this->balance = 0;
        $this->revenue = 0;
        $this->expenditure = -0;
    }

    /**
     * @throws Exception
     */
    public function __run(): void {
        parent::__run();

        if($this->isRedirect())
            return;

        if($this->uid === false) {
            $this->setContent(["results" => [],"status" => "uid_is_missing","code" => 403,"error" => true]);
            return;
        }

        $this->uid = (int)$this->uid['uid']; //Set UID

        if($this->uid !== false) {
            //Konto Type
            $this->getBaseSystem()->getGump()->validation_rules(['type' => 'required|alpha_numeric|min_len,1']);
            $this->getBaseSystem()->getGump()->filter_rules(['type' => 'trim|sanitize_string']);
            $type = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
            $this->type = 'user';
            if($type !== false) {
                switch ($type['type']) {
                    case 'group':
                        $this->type = 'group';
                        break;
                    default: //user
                        $this->type = 'user';
                        break;
                }
            }

            //Logging
            $this->user_log = $this->getEventLogsDir().'/'.$this->getType().'/page_'.$this->getCertId().'/user_'.$this->getUid();
            if(!$this->getBaseSystem()->getFilesystem()->exists($this->user_log)) {
                $this->getBaseSystem()->getFilesystem()->mkdir($this->user_log);
            }

            $this->account_logger = new Logger('Event');
            $this->account_logger->pushHandler(new StreamHandler($this->user_log.'/'.date("d_m_Y").'.log',Logger::INFO));

            $this->calculateBalance(); //Init

            $function = $this->getEventCall();
            if(method_exists($this,$function)) {
                $this->$function();
            } else {
                $this->setContent(["results" => [],"status" => $function."_is_missing","code" => 403,"error" => true]);
            }
        } else
            $this->setContent(["results" => [],"status" => "pid_is_missing","code" => 403,"error" => true]);
    }

    /**
     * Calculate users balance set to global
     */
    private function calculateBalance() {
        $this->revenue = 0;
        $this->expenditure = -0;

        $entrys = $this->getBaseSystem()->getDatabase()->fetchAll(
            'SELECT `balance` FROM `dzcp_server_account` '.
            'WHERE `type` = ? AND `uid` = ? AND `removed` = 0 AND `certid` = ?;',
            $this->getType(),$this->getUid(),$this->getCertId());

        foreach ($entrys as $entry) {
            if($entry['balance'] >= 1) {
                $this->revenue += $entry['balance'];
            }

            if($entry['balance'] <= -1) {
                $this->expenditure += $entry['balance'];
            }
        }

        $this->balance = $this->revenue + $this->expenditure;
    }

    /**
     * https://api.dzcp.de/?event=account&call=getbalance&uid=1
     */
    private function getBalance(): array
    {
        $this->calculateBalance();
        $this->setContent(['results' =>
            [
                'balance'=>$this->balance,
                'revenue'=>$this->revenue,
                'expenditure'=>$this->expenditure
            ]
        ]);

        return $this->getContent();
    }

    /**
     * https://api.dzcp.de/?event=account&call=addTransaction&uid=1&balance=1000&action=test_test&info=Test12345678&[type=user]
     * https://api.dzcp.de/?event=account&call=addTransaction&uid=1&balance=-200&action=test_test&info=Test12345678&[type=user]
     * @throws Exception
     */
    private function addTransaction() {
        //Filter Input
        $this->getBaseSystem()->getGump()->validation_rules([
            'balance' => 'required|numeric|min_len,1',
            'action' => 'required|alpha_numeric_dash|max_len,100|min_len,1'
        ]);

        $this->getBaseSystem()->getGump()->filter_rules([
            'balance' => 'sanitize_numbers',
            'action' => 'trim|sanitize_string',
        ]);

        $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
        if($input !== false) {
            $this->calculateBalance();
            $expenditure = 0;
            if($input['balance'] <= -1) {
                $expenditure += $input['balance'];
                if($this->balance < -$expenditure) {
                    $this->setContent(array_merge($this->getBalance(),
                        ["status" => "overdrawn","code" => 200,"error" => false]));
                    return;
                }
            }

            $transid = strtoupper($this->getBaseSystem()->mkPWD(2,false, false)) . '-' .
                $this->getBaseSystem()->mkPWD(14,false,true,false);

            //Filter Infos
            $this->getBaseSystem()->getGump()->validation_rules(['info' => 'required|min_len,1']);
            $this->getBaseSystem()->getGump()->filter_rules(['info' => 'trim|sanitize_string',]);
            $info = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());

            //Logging
            $log = ['balance' => $input['balance'] >= 1 ? '+'.$input['balance'] : $input['balance'],
                'transid' => $transid,
                'action' => strtolower($input['action']),
                'info' => ($info !== false ? $info['info'] : NULL)];
            $this->getAccountLogger()->info('addTransaction',$log);

            $this->getBaseSystem()->getDatabase()->query(
                'INSERT INTO `dzcp_server_account` SET '.
                '`uid` = ?, `created` = ?, `action` = ?,`balance` = ?,`type` = ?,'.
                '`transid` = ?, `certid` = ?, `info` = ?, `removed` = 0;',
                $this->getUid(),($time=time()),
                strtolower($input['action']),
                $input['balance'],$this->getType(),
                $transid,$this->getCertId(),
                ($info !== false ? $this->getBaseSystem()->encodeText($info['info']) : NULL)
            );

            $this->calculateBalance();
            $return = $this->getBalance();
            $return['results']['transid'] = $transid;
            $return['results']['created'] = $time;
            $this->setContent($return);
            $this->account_logger->close();
        }
    }

    /**
     * @throws Exception
     * https://api.dzcp.de/?event=account&call=removeTransaction&uid=1&transid=FE-29276423753851&[type=user]
     */
    private function removeTransaction() {
        //Filter Input
        $this->getBaseSystem()->getGump()->validation_rules([
            'transid' => 'required|alpha_numeric_dash|max_len,17|min_len,17'
        ]);

        $this->getBaseSystem()->getGump()->filter_rules([
            'transid' => 'trim|sanitize_string',
        ]);

        $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
        if($input !== false) {
            $entry = $this->getBaseSystem()->getDatabase()->query(
                'SELECT `id` FROM `dzcp_server_account` '.
                'WHERE `type` = ? AND `transid` = ? AND `removed` = 0 AND `certid` = ?;',
                $this->getType(),$input['transid'],$this->getCertId());

            if($entry->getRowCount()) {
                //Logging
                $log = ['transid' => $input['transid']];
                $this->getAccountLogger()->info('removeTransaction',$log);

                $this->getBaseSystem()->getDatabase()->query(
                    'UPDATE `dzcp_server_account` SET ' .
                    '`removed` = 1, `updated` = ? WHERE `transid` = ? AND `type` = ? AND `uid` = ? AND `certid` = ?;',
                    ($time = time()), $input['transid'], $this->getType(), $this->getUid(), $this->getCertId());

                $return = $this->getBalance();
                $return['results']['transid'] = $input['transid'];
                $return['results']['updated'] = $time;
                $this->setContent($return);
            } else {
                $this->setContent(array_merge($this->getBalance(),
                    ["status" => "entry_not_found","code" => 200,"error" => false]));
            }
        } else {
            $this->setContent(array_merge($this->getBalance(),
                ["status" => "transid_is_missing","code" => 200,"error" => false]));
        }
    }

    /**
     * From USER TO USER
     * https://api.dzcp.de/?event=account&call=transferTransaction&uid=1&to=2&balance=1000&action=test_test&info=Test12345678&[to_type=user]
     * @throws Exception
     */
    private function transferTransaction() {
        //Filter Input
        $this->getBaseSystem()->getGump()->validation_rules([
            'to' => 'required|numeric|min_len,1',
            'balance' => 'required|numeric|min_len,1',
            'action' => 'required|alpha_numeric_dash|max_len,100|min_len,1'
        ]);

        $this->getBaseSystem()->getGump()->filter_rules([
            'to' => 'sanitize_numbers',
            'balance' => 'sanitize_numbers',
            'action' => 'trim|sanitize_string',
        ]);

        $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
        if($input !== false) {
            $this->calculateBalance();
            $expenditure = 0;
            if($input['balance'] >= 1) {
                $expenditure += (-1 * abs($input['balance']));
                if($this->balance < -$expenditure) {
                    $this->setContent(array_merge($this->getBalance(),
                        ["status" => "overdrawn","code" => 200,"error" => false]));
                    return;
                }

                //Filter Infos
                $this->getBaseSystem()->getGump()->validation_rules(['info' => 'required|min_len,1']);
                $this->getBaseSystem()->getGump()->filter_rules(['info' => 'trim|sanitize_string',]);
                $info = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());

                $transid = strtoupper($this->getBaseSystem()->mkPWD(2,false, false)) . '-' .
                    $this->getBaseSystem()->mkPWD(14,false,true,false);

                //----
                //Logging
                $log = ['balance' => (-1 * abs($input['balance'])),
                    'to' => intval($input['to']),
                    'transid' => $transid,
                    'action' => strtolower($input['action']),
                    'info' => ($info !== false ? $info['info'] : NULL)];
                $this->getAccountLogger()->info('transferTransaction',$log);

                $this->getBaseSystem()->getDatabase()->query(
                    'INSERT INTO `dzcp_server_account` SET '.
                    '`uid` = ?, `created` = ?, `action` = ?,`balance` = ?,`type` = ?,'.
                    '`transid` = ?,`certid` = ?,`info` = ?,`removed` = 0,`to` = ?;',
                    $this->getUid(),($time=time()),
                    strtolower($input['action']),
                    (-1 * abs($input['balance'])),$this->getType(),
                    $transid,$this->getCertId(),
                    ($info !== false ? $this->getBaseSystem()->encodeText($info['info']) : NULL),
                    intval($input['to']),
                );

                //++++
                //Konto Type
                $this->getBaseSystem()->getGump()->validation_rules(['to_type' => 'required|numeric|min_len,1']);
                $this->getBaseSystem()->getGump()->filter_rules(['to_type' => 'sanitize_numbers']);
                $type = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
                $to_type = 'user';
                if($type !== false) {
                    switch ($type['to_type']) {
                        case 'group':
                            $to_type = 'group';
                            break;
                        default: //user
                            $to_type = 'user';
                            break;
                    }
                }

                //Logging
                $ex_log = $this->getEventLogsDir().'/'.$this->type.'/page_'.$this->getCertId().'/user_'.intval($input['to']);
                if(!$this->getBaseSystem()->getFilesystem()->exists($ex_log)) {
                    $this->getBaseSystem()->getFilesystem()->mkdir($ex_log);
                }

                $ex_account_logger = new Logger('Event');
                $ex_account_logger->pushHandler(new StreamHandler($ex_log.'/'.date("d_m_Y").'.log',Logger::INFO));

                $log = ['balance' => '+'.abs($input['balance']),
                    'from' => $this->getUid(),
                    'transid' => $transid,
                    'action' => strtolower($input['action']),
                    'info' => ($info !== false ? $info['info'] : NULL)];
                $ex_account_logger->info('transferTransaction',$log);
                $ex_account_logger->close();
                unset($ex_account_logger,$log);

                $this->getBaseSystem()->getDatabase()->query(
                    'INSERT INTO `dzcp_server_account` SET '.
                    '`uid` = ?, `created` = ?, `action` = ?,`balance` = ?,`type` = ?,'.
                    '`transid` = ?,`certid` = ?,`info` = ?,`removed` = 0,`from` = ?;',
                    intval($input['to']),($time=time()),
                    strtolower($input['action']),
                    abs($input['balance']),$to_type,
                    $transid,$this->getCertId(),
                    ($info !== false ? $this->getBaseSystem()->encodeText($info['info']) : NULL),
                    $this->getUid(),
                );

                $this->calculateBalance();
                $return = $this->getBalance();
                $return['results']['transid'] = $transid;
                $return['results']['created'] = $time;
                $this->setContent($return);
            }
        } else {
            $this->setContent(array_merge($this->getBalance(),
                ["status" => $this->getBaseSystem()->getGump()->get_readable_errors(),"code" => 200,"error" => false]));
        }
    }

    /**
     * From USER TO NULL
     * https://api.dzcp.de/?event=account&call=debitTransaction&uid=1&balance=1000&action=test_test&info=Test12345678&[type=user]
     * @throws Exception
     */
    private function debitTransaction() {
        //Filter Input
        $this->getBaseSystem()->getGump()->validation_rules([
            'balance' => 'required|numeric|min_len,1',
            'action' => 'required|alpha_numeric_dash|max_len,100|min_len,1'
        ]);

        $this->getBaseSystem()->getGump()->filter_rules([
            'balance' => 'sanitize_numbers',
            'action' => 'trim|sanitize_string',
        ]);

        $input = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());
        if($input !== false) {
            $this->calculateBalance();
            $expenditure = 0;
            if($input['balance'] >= 1) {
                $expenditure += (-1 * abs($input['balance']));
                if($this->balance < -$expenditure) {
                    $this->setContent(["status" => "not_enough_balance","code" => 200,"error" => false]);
                    return;
                }

                //Filter Infos
                $this->getBaseSystem()->getGump()->validation_rules(['info' => 'required|min_len,1']);
                $this->getBaseSystem()->getGump()->filter_rules(['info' => 'trim|sanitize_string',]);
                $info = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput());

                $transid = strtoupper($this->getBaseSystem()->mkPWD(2,false, false)) . '-' .
                    $this->getBaseSystem()->mkPWD(14,false,true,false);

                //Logging
                $log = ['balance' => (-1 * abs($input['balance'])),
                    'action' => strtolower($input['action']),
                    'transid' => $transid,
                    'info' => ($info !== false ? $info['info'] : NULL)];
                $this->getAccountLogger()->info('debitTransaction',$log);

                $this->getBaseSystem()->getDatabase()->query(
                    'INSERT INTO `dzcp_server_account` SET '.
                    '`uid` = ?, `created` = ?, `action` = ?,`balance` = ?,`type` = ?,'.
                    '`transid` = ?,`certid` = ?,`info` = ?,`removed` = 0, `payed` = 1;',
                    $this->getUid(),($time=time()),
                    strtolower($input['action']),
                    (-1 * abs($input['balance'])),
                    $this->getType(),
                    $transid,
                    $this->getCertId(),
                    ($info !== false ? $this->getBaseSystem()->encodeText($info['info']) : NULL),
                );

                $return = [];
                $return['results']['transid'] = $transid;
                $return['results']['created'] = $time;
                $this->setContent($return);
                $this->account_logger->close();
            }
        } else {
            $this->setContent(array_merge($this->getBalance(),
                ["status" => $this->getBaseSystem()->getGump()->get_readable_errors(),"code" => 200,"error" => false]));
        }
    }

    /**
     * From USER TO NULL
     * https://api.dzcp.de/?event=account&call=showAccount&uid=1
     */
    private function showAccount() {
        $orderby = 'created';
        if(array_key_exists('orderby',$this->getBaseSystem()->getInput()))
            $orderby = $this->getBaseSystem()->getInput()['orderby'];
        if($orderby == 'datum') $orderby = 'created';

        $this->getBaseSystem()->getGump()->validation_rules(['page' => 'required|numeric|min_len,1',
                                                             'max_items' => 'required|numeric|min_len,1']);
        $this->getBaseSystem()->getGump()->filter_rules(['page' => 'sanitize_numbers',
                                                         'max_items' => 'sanitize_numbers']);
        $page = $this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput()); $SQL_LIMIT = '';
        if($page !== false) {
            $SQL_LIMIT = " LIMIT ".($page['page'] - 1)*$page['max_items'].",".$page['max_items'];
        }

        try {
            $sql = $this->getBaseSystem()->getDatabase()->fetchAll('SELECT * FROM `dzcp_server_account` '.
                'WHERE `uid` = ? AND `certid` = ? AND `removed` = 0 ORDER BY `'.(empty($orderby) ? 'created' : $orderby).'` '.
                ($this->getBaseSystem()->getInput()['order_asc'] ? 'ASC' : 'DESC').$SQL_LIMIT.';',
                $this->getUid(),$this->getCertId());
        } catch (PDOException $error) {
            die($this->getBaseSystem()->getDatabase()->getLastQueryString());
        }

        $item = [];
        foreach ($sql as $get) {
            $item[] = [
                'created' => (int)$get['created'],
                'updated' => (int)$get['updated'],
                'action' => utf8_decode($get['action']),
                'info' => $this->getBaseSystem()->decodeText($get['info']),
                'from' => (int)$get['from'],
                'to' => (int)$get['to'],
                'balance' => (int)$get['balance'],
                'transid' => utf8_decode($get['transid']),
                'show' => $get['show'] ? true : false,
                'payed' => $get['payed'] ? true : false,
            ];
        }

        $total = count($this->getBaseSystem()->getDatabase()->fetchAll('SELECT * FROM `dzcp_server_account` '.
            'WHERE `uid` = ? AND `certid` = ? AND `removed` = 0;',$this->getUid(),$this->getCertId()));

        $this->calculateBalance();
        $return = $this->getBalance();
        $return['results']['total'] = $total;
        $return['results']['created'] = time();
        $return['results']['items'] = $item;
        $this->setContent($return);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * @return Logger
     */
    public function getAccountLogger(): Logger
    {
        return $this->account_logger;
    }
}