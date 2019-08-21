<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

/**
 * RPC-Call Version 2.0
 * https://de.wikipedia.org/wiki/JSON-RPC
 * Request: jsonrpc,method,params,id
 * Response: jsonrpc,result,error,id
 */
ob_start();
define('_rpc',true); //Called from RPC
require_once(dirname(__DIR__).'/Application/bootstrap.php');
ob_end_flush();