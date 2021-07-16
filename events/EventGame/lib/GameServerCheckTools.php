<?php
/**
 * DZCP - deV!L`z ClanPortal API-Server
 * http://www.dzcp.de
 */

class GameServerCheckTools
{
    /**
     * @param string $host
     * @param int $port
     * @param float $timeout
     * @param bool $udp
     * @return bool
     */
    static function pingServer(string $host = '',int $port = 0,float $timeout = 0.2,bool $udp = false): bool
    {
        try {
            if($fp = fsockopen($udp ? 'udp://'.$host : $host,$port,$errCode,$errStr,$timeout)) {
                if($udp) {
                    if(!fwrite($fp,"\xFA")) {
                        @fclose($fp);
                        return false;
                    }
                }

                if(!fwrite($fp,"\x04")) {
                    @fclose($fp);
                    return false;
                }

                fclose($fp);
                return true;
            } else {
                if(!$udp) {
                    return self::pingServer($host,$port,$timeout,true);
                }
            }
        } catch (Exception $e) { }

        return false;
    }
}