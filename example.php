<?php

use EricKertz\Libraries\ResponsysLibrary;

class ResponsysController {

	protected $responsysLibrary;

	public function __construct(ResponsysLibrary $responsysLibrary)
	{
		$this->responsysLibrary = $responsysLibrary;
	}

	public function create()
	{
		
		$emailAddress = "your_email_address@your_domain.com";
		$firstName = "your_first_name";
		
		// login as api user
		$this->responsysLibrary->setUsername('YOUR_USER_NAME');
		$this->responsysLibrary->setPassword('**************');
		$login = $this->responsysLibrary->login();
		
		// add member to list
		$this->responsysLibrary->setMyFolderName('YOUR_FOLDER_NAME');
		$this->responsysLibrary->setMyTableName('YOUR_TABLE_NAME');
		$fields = array('EMAIL_ADDRESS_', 'FIRST_NAME');
		$values = array($emailAddress, $firstName);
		$this->responsysLibrary->setReqArgsMember($fields,$values);
		$mergeListMembers = $this->responsysLibrary->mergeListMembers();
		
		// trigger campaign email
		$this->responsysLibrary->setMyCampName('YOUR_CAMPAIGN_NAME');
		$this->responsysLibrary->setMyCampFolderName('YOUR_CAMPAIGN_FOLDER_NAME');
		$this->responsysLibrary->setReqArgsMessage();
		$this->responsysLibrary->setRecipient($emailAddress);
		$triggerCampaignMessage = $this->responsysLibrary->triggerCampaignMessage();
		
	}
	
}
