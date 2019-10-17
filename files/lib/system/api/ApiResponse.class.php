<?php
namespace wcf\system\api;
use wcf\system\WCF;
use wcf\util\JSON;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class ApiResponse {
    /**
     * @var string
     */
    public $status;

    /**
     * @var array
     */
    public $response;

    /**
     * @var integer
     */
    public $resultCount;

    public function __construct($response, $status = 200, $resultCount = null) {
        $this->response = $response;
        $this->status = $status;
        $this->resultCount = $resultCount;
    }

    public function toArray() {
        $response = ['status' => $this->status, 'data' => $this->response];

        if ($this->resultCount !== null) {
            $response['resultCount'] = $this->resultCount;
        }

        return $response;
    }
}
