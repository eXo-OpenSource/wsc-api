<?php
namespace wcf\action;

use wcf\api\UserGroupApi;
use wcf\system\exception\ApiException;
use wcf\util\StringUtil;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class UserGroupApiAction extends AbstractAjaxAction {

	/**
	 * A valid request method
	 * @var	string
	 */
	private $method;
	
	/**
	 * The api secret
	 * @var	string
	 */
	private $secret;

	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
	}

	/**
	 * @inheritDoc
	 */
	public function execute() {
		parent::execute();
		
        $this->sendJsonResponse(array('status' => 200, 'data' => (new UserGroupApi())->execute()));
	}
}
