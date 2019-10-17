<?php
namespace wcf\api;

use wcf\util\StringUtil;
use wcf\system\exception\ApiException;
use wcf\action\AbstractAjaxAction;
use wcf\system\api\ApiSecretPermissionHandler;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class BaseApiAction extends AbstractAjaxAction  {

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
	 * Class
	 */
    public $class = null;

	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
        parent::readParameters();
		$this->method = isset($_GET['method']) ? StringUtil::trim($_GET['method']) : '';
        $this->secret = isset($_POST['secret']) ? StringUtil::trim($_POST['secret']) : '';
    }
    
	/**
	 * @inheritDoc
	 */
	public function execute() {
        parent::execute();

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

        try {
            $method = new \ReflectionMethod($this->class, $this->method);
            $doc = $method->getDocComment();

            if ($doc) {
                if (strpos($doc, '@api')) {
                    if (strpos($doc, '@permission')) {
                        preg_match('/@permission\(["\']([^"\']{0,})["\']\)/', $doc, $permission);
                        if (empty($permission)) {
                            throw new ApiException('Permission denied', 403);
                        }
                        $permission = $permission[1];

                        if (!ApiSecretPermissionHandler::getInstance()->getPermission($this->secretID, $permission)) {
                            throw new ApiException('Permission denied', 403);
                        }
                    }

                    $params = $method->getParameters();
                    $callParams = [];

                    foreach ($params as $param) {
                        if (isset($_POST[$param->getName()])) {
                            $callParams[$param->getName()] = StringUtil::trim($_POST[$param->getName()]);
                        } else {
                            if ($param->isDefaultValueAvailable()) {
                                $callParams[$param->getName()] = $param->getDefaultValue();
                            } else {
                                throw new ApiException($param->getName() . ' is required', 400);
                            }
                        }
                    }
                    $instance = new $this->class($this->secretID);
                    $result = call_user_func_array([$instance, $this->method], $callParams);
                    return $this->sendJsonResponse(['status' => 200, 'data' => $result]);
                }
            }
            throw new ApiException('method is not callable', 400);
        } catch(\ReflectionException $exception) {
            throw new ApiException('method is unknown', 400);
        }
    }
}
