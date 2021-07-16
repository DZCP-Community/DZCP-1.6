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
 * Class EventNews
 */
class EventNews extends BaseEventAbstract {
    /**
     * EventNews constructor.
     * @param BaseSystem $baseSystem
     */
    public function __construct(BaseSystem $baseSystem)
    {
        try {
            parent::__construct($baseSystem);
        } catch (Exception $e) {
            exit();
        }

        $this->useCert(false);

        $this->setCompress(true);

        $this->getLogger()->pushHandler(new StreamHandler(LOG_PATH.'/'.__CLASS__.'.log',
            DEBUG ? Logger::DEBUG : Logger::WARNING));
    }

    /**
     * @throws Exception|\Symfony\Component\Filesystem\Exception\IOException
     */
    public function __run(): void
    {
        parent::__run();

        if($this->isRedirect())
            return;

        $sql_query = 'WHERE `public` = 1 ';
        if(array_key_exists('version',$this->getBaseSystem()->getInput()) && /* Check of format x.x.x.x */
            boolval(preg_match("/[0-9].[0-9].[0-9].[0-9]/i", $this->getBaseSystem()->getInput()['version']))) {
            $sql_query = 'WHERE (`version` = \''.strtolower($this->getBaseSystem()->getInput()['version']).'\' OR `version` = \'all\') AND `public` = 1 ';
        }

        //Filter input for OLD NEWS 1.6.1.x => proxy.php
        $this->getBaseSystem()->getGump()->validation_rules(['old_news' => 'required|min_len,1']);
        $this->getBaseSystem()->getGump()->filter_rules(['old_news' => 'trim|sanitize_string']);
        $news = [];
        if($this->getBaseSystem()->getGump()->run($this->getBaseSystem()->getInput()) !== false) {
            $sql = $this->getBaseSystem()->getDatabase()->fetchAll('SELECT `id`,`text`,`titel`,`url` FROM `dzcp_server_news` '.
                $sql_query.'ORDER BY `datum` DESC LIMIT 3;');
            foreach ($sql as $get) {
                $news[] = '<a href="' .utf8_decode($get['url']). '" title="' .
                    strip_tags($this->getBaseSystem()->decodeText($get->text)) . '" target="_blank">' .
                    utf8_decode($get->titel) . '</a> ';
            }

            $news = array_reverse($news); $news_list = '';
            foreach ($news AS $news_entity) {
                if(empty($news_entity))
                    continue;

                $news_list .= $news_entity . '- ';
            } unset($i,$news_entity,$news,$end);

            $this->setContent(['results' => ['news' => $news_list]]);
        } else {
            //Newer Version of Adminnews 1.8++
            $sql = $this->getBaseSystem()->getDatabase()->fetchAll('SELECT * FROM `dzcp_server_news` '.$sql_query.'ORDER BY `datum` DESC LIMIT 5;');
            foreach ($sql as $get) {
                $image = 'https://static.dzcp.de/images/news/default.jpg';
                foreach(["jpg", "jpeg", "gif", "png"] as $tmpendung) {
                    if(file_exists(HOME_DIR."/public_html/static/images/news/".utf8_decode($get->id).".".$tmpendung)) {
                        $image = HOME_DIR."/public_html/static/images/news/".utf8_decode($get->id).'.'.$tmpendung;
                        break;
                    }
                }

                $news[] = [
                    'id' => $get->id,
                    'image' => $image,
                    'titel' => utf8_decode($get->titel),
                    'text' => $this->getBaseSystem()->decodeText($get->text),
                    'url' => empty($get->url) ? '' : utf8_decode($get->url),
                    'date' => intval($get->datum)];
            }

            $this->setContent(['results' => ['news' => $news]]);
        }
    }
}