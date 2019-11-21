<?php

namespace wcf\acp\page;

use wcf\system\WCF;
use wcf\page\AbstractPage;
use wcf\system\request\RouteHandler;

class ApiSecretDocumentationPage extends AbstractPage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.wscApi.secrets.documentation';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = [];


	public $apiData = [];

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		$classes = [
			'user' => \wcf\api\UserApi::class,
			'user-group' => \wcf\api\UserGroupApi::class,
		];

		foreach($classes as $key => $class) {
			$class = new \ReflectionClass($class);
			
			$classMethods = [];
			$methods = $class->getMethods();

			foreach($methods as $method) {
				$doc = $method->getDocComment();
				if ($doc && strpos($doc, '@api')) {
					$tmp = [
						'name' => $method->getName(),
						'params' => [],
					];
	
					$params = $method->getParameters();
					preg_match_all('/@param ([^ ]{0,}) \$([a-zA-Z_-]{1,})/', $doc, $paramHint, PREG_SET_ORDER, 0);
					
					foreach($params as $param) {
						$para = [
							'name' => $param->getName(),
							'types' => NULL,
						];
	
						if ($param->isDefaultValueAvailable()) {
							$para['defaultValue'] = $param->getDefaultValue();
						}
	
						foreach($paramHint as $hint) {
							if ($hint[2] === $param->getName()) {
								if (strpos($hint[1], '|')) {
									$para['types'] = explode('|', $hint[1]);
								} else {
									$para['types'] = [$hint[1]];
								}
							}
						}
	
						if ($param->isDefaultValueAvailable()) {
							$para['defaultValue'] = $param->getDefaultValue();
						}
						$para['types_text'] = join(', ', $para['types']);
	
						array_push($tmp['params'], $para);
					}
					
					$classMethods[$method->getName()] = $tmp;
				}
			}

			$this->apiData[$key] = $classMethods;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		// https://docs.hetzner.cloud/#floating-ip-actions-unassign-a-floating-ip
		WCF::getTPL()->assign([
			'host' => RouteHandler::getHost(),
			'apiData' => $this->apiData,
		]);
	}
}
