<?php

use EricKertz\Libraries\ResponsysLibrary;

class ResponsysController {

	protected $responsysLibrary;

	public function __construct(ResponsysServiceProvider $responsysLibrary)
	{
		$this->responsysLibrary = $responsysLibrary;
	}

	public function create()
	{
		
		$emailAddress = "your_email_address@your_domain.com";
		$firstName = "your_first_name";
		
		// login as api user
		$this->responsysServiceProvider->setUsername('YOUR_USER_NAME');
		$this->responsysServiceProvider->setPassword('**************');
		$login = $this->responsysServiceProvider->login();
		
		// add member to list
		$this->responsysServiceProvider->setMyFolderName('YOUR_FOLDER_NAME');
		$this->responsysServiceProvider->setMyTableName('YOUR_TABLE_NAME');
		$fields = array('EMAIL_ADDRESS_', 'FIRST_NAME');
		$values = array($emailAddress, $firstName);
		$this->responsysServiceProvider->setReqArgsMember($fields,$values);
		$mergeListMembers = $this->responsysServiceProvider->mergeListMembers();
		
		// trigger campaign email
		$this->responsysServiceProvider->setMyCampName('YOUR_CAMPAIGN_NAME');
		$this->responsysServiceProvider->setMyCampFolderName('YOUR_CAMPAIGN_FOLDER_NAME');
		$this->responsysServiceProvider->setReqArgsMessage();
		$this->responsysServiceProvider->setRecipient($emailAddress);
		$triggerCampaignMessage = $this->responsysServiceProvider->triggerCampaignMessage();

		$preferredAirports = $this->preferredAirport->lists('name', 'id');
		$this->layout->content = View::make($this->themeName.'::entries.create')
			->with('preferredAirports', $preferredAirports);
	}
