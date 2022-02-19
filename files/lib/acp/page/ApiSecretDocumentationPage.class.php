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
            ['class' => \wcf\api\UserApi::class, 'endpoint' => 'user', 'name' => 'user'],
            ['class' => \wcf\api\UserGroupApi::class, 'endpoint' => 'user-group', 'name' => 'group'],
            ['class' => \wcf\api\TrophyApi::class, 'endpoint' => 'trophy', 'name' => 'trophy'],
            ['class' => \wcf\api\UserTrophyApi::class, 'endpoint' => 'user-trophy', 'name' => 'trophy.user'],
            ['class' => \wcf\api\PostApi::class, 'endpoint' => 'post', 'name' => 'post'],
            ['class' => \wcf\api\ThreadApi::class, 'endpoint' => 'thread', 'name' => 'thread'],
		];

		foreach($classes as $class) {
			$cls = new \ReflectionClass($class['class']);

			$classMethods = [];
			$methods = $cls->getMethods();

			foreach($methods as $method) {
				$doc = $method->getDocComment();
				if ($doc && strpos($doc, '@api')) {
					$tmp = [
						'name' => $method->getName(),
						'params' => [],
					];

					$params = $method->getParameters();
					preg_match_all('/@param ([^ ]{0,}) \$([a-zA-Z_-]{1,}) {0,}([^\n]{0,})/', $doc, $paramHint, PREG_SET_ORDER, 0);

					foreach($params as $param) {
						$para = [
							'name' => $param->getName(),
							'types' => NULL,
							'description' => NULL,
						];

                        $para['hasDefaultValue'] = false;
						if ($param->isDefaultValueAvailable()) {
							$para['hasDefaultValue'] = true;
							$para['defaultValue'] = $param->getDefaultValue();
						}

						foreach($paramHint as $hint) {
							if ($hint[2] === $param->getName()) {
								$para['description'] = $hint[3] ?: NULL;
								if (strpos($hint[1], '|')) {
									$para['types'] = explode('|', $hint[1]);
								} else {
									$para['types'] = [$hint[1]];
								}
							}
                        }

						$para['types_text'] = join(', ', $para['types']);

						array_push($tmp['params'], $para);
					}

					$classMethods[$method->getName()] = $tmp;
				}
			}

            array_push($this->apiData, array_merge($class, ['methods' => $classMethods]));
		}
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign([
			'host' => RouteHandler::getHost(),
			'apiData' => $this->apiData,
		]);
	}
}
