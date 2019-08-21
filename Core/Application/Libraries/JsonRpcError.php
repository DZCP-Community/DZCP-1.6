<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package DZCP\Exceptions
 * @version 1.0
 */

namespace Application\Libraries;

/**
 * Class JsonRpcError
 * @package Application\Libraries
 */
class JsonRpcError {
    /**
     * Errors
     */
    const NoError           = 0;
    const ParseError        = -32700;
    const InvalidRequest    = -32600;
    const MethodNotFound    = -32601;
    const InvalidParams     = -32602;
    const InternalError     = -32603;
    const ServerError       = -32000;

    /**
     * @var array
     */
    private $error = [];

    /**
     * JsonRpcError constructor.
     */
    public function __construct() {
       $this->error = [
            'code' => self::NoError,
            'message' => '',
            'data' => ''];

       return $this;
    }

    /**
     * @return array
     */
    public function getError(): array {
        return $this->error;
    }

    /**
     * @return int
     */
    public function getCode(): int {
        return $this->error['code'];
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void {
        $this->error['code'] = $code;
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->error['message'];
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void {
        $this->error['message'] = $message;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->error['data'];
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void {
        $this->error['data'] = $data;
    }
}