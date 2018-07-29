<?php

namespace wcf\acp\page;

use wcf\page\AbstractPage;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;

class ApiSecretListPage extends AbstractPage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.wscApi.secrets.list';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = [];
	
	/**
	 * @var	array
	 */
	public $secrets = [];
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
        
        $sql = "SELECT *
                FROM	wcf".WCF_N."_api_secret";

        $statement = WCF::getDB()->prepareStatement($sql);

        $statement->execute();


        while ($row = $statement->fetchArray()) {
            array_push($this->secrets, $row);
        }
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
				
		WCF::getTPL()->assign([
			'secrets' => $this->secrets
		]);
	}
}
