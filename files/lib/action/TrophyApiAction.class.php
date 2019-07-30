<?php
namespace wcf\action;

use wcf\api\TrophyApi;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class TrophyApiAction extends AbstractAjaxAction {

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
		
        $this->sendJsonResponse((new TrophyApi())->execute());
	}
}
