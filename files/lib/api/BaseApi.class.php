<?php
namespace wcf\api;

use wcf\util\StringUtil;
use wcf\system\exception\ApiException;
use wcf\system\api\ApiSecretPermissionHandler;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class BaseApi {

	/**
	 * A valid request method
	 * @var	string
	 */
	public $method;
	
	/**
	 * The api secret
	 * @var	string
	 */
    public $secret;
	
	/**
	 * The api secret
	 * @var	integer
	 */
    public $secretID;
    
	/**
	 * Allowed methods
	 * @var string[]
	 */
    public $allowedMethods = [];

    public function __construct() {
		$this->method = (isset($_REQUEST['method'])) ? StringUtil::trim($_REQUEST['method']) : '';
        $this->secret = (isset($_REQUEST['secret'])) ? StringUtil::trim($_REQUEST['secret']) : '';
    
        // check if secret exists
        $sql = "SELECT  *
                FROM    wcf".WCF_N."_api_secret
                WHERE   secretKey = ?";
        $statement = \wcf\system\WCF::getDB()->prepareStatement($sql, 1);
        $statement->execute([$this->secret]);
        $row = $statement->fetchSingleRow();

        if (!$row) {
            throw new ApiException('Invalid secret', 412);
        }
        
        $this->secretID = $row['secretID'];
    }


    public function execute() {
        if (in_array($this->method, $this->allowedMethods)) {
            if (method_exists($this, $this->method)) {
                $result = $this->{$this->method}(); // call_user_func_array(array(UserGroupApi::class, $this->method), array());
            } else {
                throw new ApiException('Invalid method', 412);
            }
        } else {
            throw new ApiException('Invalid method', 412);
        }

        return array('status' => 200, 'data' => $result);
    }

    public function checkPermission($permission) {
        if (ApiSecretPermissionHandler::getInstance()->getPermission($this->secretID, $permission)) {
            return true;
        } else {
            throw new ApiException('Permission denied', 403);
        }
    }
}
