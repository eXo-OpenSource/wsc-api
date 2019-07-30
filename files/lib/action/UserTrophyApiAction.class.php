<?php
namespace wcf\action;

use wcf\api\UserTrophyApi;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class UserTrophyApiAction extends AbstractAjaxAction {

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
		
        $this->sendJsonResponse((new UserTrophyApi())->execute());
	}
}
