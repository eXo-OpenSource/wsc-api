<?php
namespace wcf\action;

use wcf\api\ThreadApi;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class ThreadApiAction extends AbstractAjaxAction {

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
		
        $this->sendJsonResponse((new ThreadApi())->execute());
	}
}
