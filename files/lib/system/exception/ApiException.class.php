<?php
namespace wcf\system\exception;
use wcf\system\WCF;
use wcf\util\JSON;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class ApiException extends LoggedException {
	/**
	 * missing parameters
	 * @var	integer
	 */
	const MISSING_PARAMETERS = 400;
	
	/**
	 * session expired
	 * @var	integer
	 */
	const SESSION_EXPIRED = 401;
	
	/**
	 * insufficient permissions
	 * @var	integer
	 */
	const INSUFFICIENT_PERMISSIONS = 403;
	
	/**
	 * illegal link
	 * @var	integer
	 */
	const ILLEGAL_LINK = 404;
	
	/**
	 * bad parameters
	 * @var	integer
	 */
	const BAD_PARAMETERS = 412;
	
	/**
	 * internal server error
	 * @var	integer
	 */
	const INTERNAL_ERROR = 503;
	
	/**
	 * internal server error
	 * @var	integer
	 */
	const IM_A_TEAPOT = 418;
	
	/**
	 * Throws a JSON-encoded error message
	 * 
	 * @param	string		$message
	 * @param	integer		$errorType
	 */
	public function __construct($message, $errorType = self::INTERNAL_ERROR) {
		$responseData = [
			'status' => $errorType,
			'message' => $message
		];
		
		
		$statusHeader = '';
		switch ($errorType) {
			case self::MISSING_PARAMETERS:
				$statusHeader = 'HTTP/1.1 400 Bad Request';
			break;
			
			case self::SESSION_EXPIRED:
				$statusHeader = 'HTTP/1.1 409 Conflict';
			break;
			
			case self::INSUFFICIENT_PERMISSIONS:
				$statusHeader = 'HTTP/1.1 403 Forbidden';
			break;
			
			case self::BAD_PARAMETERS:
				$statusHeader = 'HTTP/1.1 400 Bad Request';
			break;
            case self::IM_A_TEAPOT:
                $statusHeader = 'HTTP/1.1 418 I\'m a Teapot';
            break;
			default:
			case self::ILLEGAL_LINK:
			case self::INTERNAL_ERROR:
				$statusHeader = 'HTTP/1.1 503 Service Unavailable';
			break;
		}
		
		header($statusHeader);
		header('Content-type: application/json');
		echo JSON::encode($responseData);
		exit;
	}
}