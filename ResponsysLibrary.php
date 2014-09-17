<?php namespace EricKertz\Libraries;

class ResponsysLibrary  {
		
		// API Properties
		protected $interact2_WSDL = "https://ws5.responsys.net/webservices/wsdl/ResponsysWS_Level1.wsdl";
		protected $interact2_EndPoint = "https://ws5.responsys.net/webservices/services/ResponsysWSService";
		protected $interact_URI = "urn:ws.rsys.com";
		
		// Client Properties
		protected $client;
		protected $username;
		protected $password;
		protected $loginResult;
		protected $sessionId;
		protected $sessionHeader;
		protected $header;
		protected $jsessionID;
		
		// List / Member Properties
		protected $myFolderName;
		protected $myTableName;
		protected $reqArgsMember;

		// Campaign / Message Properties
		protected $myCampName;
		protected $myCampFolderName;
		protected $reqArgsMessage;
		protected $recipient;
		protected $optionalData;
		protected $recDataValues;
		
		public function register()
		{
			
		}

		public function __construct()
		{
			$this->createClient();
		}

		// Client Methods

		public function createClient()
		{
			$this->client = new \SoapClient($this->interact2_WSDL, array(
				'location' => $this->interact2_EndPoint,
				'uri' => $this->interact_URI,
				'trace' => TRUE,
			));
			return $this->client;
		}

		public function login()
		{
			$this->loginResult = $this->client->login(array(
				"username"=>$this->username,
				"password"=>$this->password
			)); //session ID and jsession returned from this call
			if($this->loginResult){
				$this->sessionId = array('sessionId'=>new \SoapVar($this->loginResult->result->sessionId, XSD_STRING, null, null, null, 'ws.rsys.com'));
				$this->sessionHeader = new \SoapVar($this->sessionId,SOAP_ENC_OBJECT);
				$this->header = new \SoapHeader('ws.rsys.com', 'SessionHeader', $this->sessionHeader);  //how you set the sessionID in the header
				$this->client->__setSoapHeaders(array($this->header));
				$this->jsessionID = $this->client->_cookies["JSESSIONID"][0]; //how you can retrieve the JSESSIONID
				$this->client->__setCookie("JSESSIONID", $this->jsessionID); //how you set the cookie to use jsessionID	
			}
			return $this->loginResult;
		}
		
		public function setUsername($username)
		{
			$this->username = $username;
		}
		
		
		public function setPassword($password)
		{
			$this->password = $password;
		}
		
		// List / Member Methods
		
		public function setMyFolderName($myFolderName)
		{
			$this->myFolderName = $myFolderName;
		}
		
		public function setMyTableName($myTableName)
		{
			$this->myTableName = $myTableName;
		}
		
		public function setReqArgsMember($fields,$values)
		{
			$this->reqArgsMember = new \stdClass();
			$this->reqArgsMember->list = new \stdClass();
			$this->reqArgsMember->list->folderName = $this->myFolderName; //Name of folder the table exists
			$this->reqArgsMember->list->objectName = $this->myTableName;  //Name of table
			$this->reqArgsMember->recordData=new \stdClass();
			$this->setMemberFields($fields);
			$this->setMemberValues($values);
			$this->mergeListRules();
		}
		
		public function mergeListMembers()
		{
			$response = $this->client->mergeListMembers($this->reqArgsMember);
			return $response;
		}
		
		private function setMemberFields($fields)
		{
			$this->reqArgsMember->recordData->fieldNames = $fields;
		}
		
		private function setMemberValues($values)
		{
			$fieldValues[]=array("fieldValues"=>$values); //records to insert/update 
			$this->reqArgsMember->recordData->records=new \stdClass();
			$this->reqArgsMember->recordData->records=$fieldValues;
		}
		
		private function mergeListRules()
		{
			$this->reqArgsMember->mergeRule = new \stdClass();
			$this->reqArgsMember->mergeRule->insertOnNoMatch=True;
			$this->reqArgsMember->mergeRule->updateOnMatch='REPLACE_ALL';
			$this->reqArgsMember->mergeRule->matchColumnName1='EMAIL_ADDRESS_';  //column to match 
			$this->reqArgsMember->mergeRule->matchOperator='NONE';
			$this->reqArgsMember->mergeRule->optinValue='I';
			$this->reqArgsMember->mergeRule->optoutValue='O';
			$this->reqArgsMember->mergeRule->htmlValue='H';
			$this->reqArgsMember->mergeRule->textValue='T';
			$this->reqArgsMember->mergeRule->rejectRecordIfChannelEmpty='E';
		}
		
		// Campaign / Message Methods
		
		public function setMyCampName($myCampName)
		{
			$this->myCampName = $myCampName;
		}
		
		public function setMyCampFolderName($myCampFolderName)
		{
			$this->myCampFolderName = $myCampFolderName;
		}
		
		public function triggerCampaignMessage()
		{
			$this->setRecDataValues();
			$response = $this->client->triggerCampaignMessage($this->reqArgsMessage); //triggerCampaignMessage call
			return $response;
		}
		
		public function setReqArgsMessage()
		{
			$this->reqArgsMessage = new \stdClass();
			$this->reqArgsMessage->campaign = new \stdClass();
			$this->reqArgsMessage->campaign->folderName = $this->myCampFolderName; //Name of folder the campaign exists
			$this->reqArgsMessage->campaign->objectName = $this->myCampName;  //Name of campaign
			$this->reqArgsMessage->recipientData = new \stdClass();
		}
		
		public function setRecipient($emailAddress)
		{
			$this->recipient = new \stdClass();
			$this->recipient->listName = new \stdClass();
			$this->recipient->listName->folderName = $this->myFolderName; //name of folder the table/list exists
			$this->recipient->listName->objectName = $this->myTableName; //name of the list/table
			$this->recipient->emailAddress=$emailAddress; //email of recipient 1
		}
		
		public function setOptionalData($name = "", $value = "")
		{
			$this->optionalData = new \stdClass();
			$this->optionalData->name = $name;
			$this->optionalData->value = $value;
		}
		
		private function setRecDataValues()
		{
			if(empty($this->optionalData)){
				$this->optionalData = $this->setOptionalData();
			}
			$this->recDataValues[]=array("recipient"=>$this->recipient,"optionalData"=>$this->optionalData);
			$this->reqArgsMessage->recipientData=$this->recDataValues;
		}
		
}
