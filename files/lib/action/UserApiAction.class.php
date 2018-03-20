<?php
namespace wcf\action;

use wcf\api\UserApi;
use wcf\system\exception\ApiException;
use wcf\util\StringUtil;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class UserApiAction extends AbstractAjaxAction {

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
		
		$this->method = (isset($_REQUEST['method'])) ? StringUtil::trim($_REQUEST['method']) : '';
		$this->secret = (isset($_REQUEST['secret'])) ? StringUtil::trim($_REQUEST['secret']) : '';
		
		if (empty(WSC_API_SECRET)) {
			throw new ApiException('API disabled', 403);
		}

		
		if (WSC_API_SECRET !== $this->secret) {
			throw new ApiException('Invalid secret', 403);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function execute() {
		parent::execute();
		
        if (method_exists(UserApi::class, $this->method)) {
			$result = call_user_func_array(array(UserApi::class, $this->method), array());
        } else {
            throw new ApiException('Invalid method', 412);
        }

        $this->sendJsonResponse(array('status' => 200, 'data' => $result));
	}
}
