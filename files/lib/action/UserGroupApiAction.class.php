<?php
namespace wcf\action;

use wcf\api\UserGroupApi;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class UserGroupApiAction extends AbstractAjaxAction {

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
		
        $this->sendJsonResponse((new UserGroupApi())->execute());
	}
}
