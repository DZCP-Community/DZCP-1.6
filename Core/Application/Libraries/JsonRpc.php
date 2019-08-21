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

use Application\Exceptions\JsonException;

class JsonRpc {
    /**
     * @var array
     */
    private $data= [
        'request'=>[],
        'response'=>[]
    ];

    /**
     * @var int|null
     */
    private $id = null;

    /**
     * JsonRpc constructor.
     */
    public function __construct() {
        $this->data['response'] = [
            'jsonrpc' => '2.0',
            'result' => '',
            'error' => new JsonRpcError(),
            'id' => $this->id
        ];

        $this->data['request'] = [
            'jsonrpc' => '2.0',
            'method' => '',
            'params' => [],
        ];
    }

    /**
     * @param array $input
     * @throws JsonException
     */
    public function setRequest(array $input): void {
        if(array_key_exists('jsonrpc',$input) && array_key_exists('method',$input) &&
            array_key_exists('params',$input) && array_key_exists('id',$input)) {
            if(strtolower($input['jsonrpc']) == '2.0') {
                $this->id = (int)$input['id']; unset($input['id']);
                $this->data['request'] = $input;
                return;
            }
        }

        $this->getError()->setCode(JsonRpcError::ParseError);
        $this->getError()->setMessage('No JSON-RPC 2.0 Specification!');
        $this->getError()->getData($input);
    }

    /**
     * Send JSON-RPC 2.0 Response
     */
    public function getResponse(): void {
        $send = $this->data['response'];
        $send['id'] = $this->id;
        //Set Error then code has not JsonRpcError::NoError
        $error = $this->data['response']['error']->getError();
        if($error['code'] != JsonRpcError::NoError) {
            $send['error'] = $error;
        }
        header("Content-type: application/json; charset=utf-8");
        echo json_encode($send,JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void {
        $this->id = $id;
    }

    /**
     * @return JsonRpcError
     */
    public function getError(): JsonRpcError {
        return $this->data['response']['error'];
    }

    /**
     * @param mixed $result
     */
    public function setResult($result): void {
        $this->data['response']['result'] = $result;
    }

    /**
     * @return mixed
     */
    public function getResult() {
       return $this->data['response']['result'];
    }

    /**
     * @return string
     */
    public function getMethod(): string {
        return strval($this->data['request']['method']);
    }

    /**
     * @return array
     */
    public function getParams(): array {
        return (array)$this->data['request']['params'];
    }
}