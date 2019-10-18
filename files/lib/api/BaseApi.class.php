<?php
namespace wcf\api;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class BaseApi {

	/**
	 * @var	string
	 */
	public $secretID;

	/**
	 * @inheritDoc
	 */
	public function __construct($secretID) {
        $this->secretID = $secretID;
    }
}
