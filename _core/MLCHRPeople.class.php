<?php
/* 
 *This gets and saves client data for a high rise contact
 */
class MLCHRPeople extends MLCHRObjectBase{
    protected static $strClassName = __CLASS__;

    public $strCreateUrl = "/people.xml";
    public $strUpdateUrl = "/people/%s.xml?reload=true";

    public $strFirstName = null;
    public $strLastName = null;
    public $strTitle = null;
    public $strCompanName = null;
    public $strBackground = null;
    public $arrEmails = array();
    public $arrPhones = array();
    public $arrWebsites = array();
	public $arrTwitter = array();
	public $arrSubjectData = array();

    public static function QueryArray($strQuery, $strCriteria = MLCHRPeopleSearchCriteria::Email, $arrParams = array()){
    	
    	if(is_array($strQuery)){
    		$arrParams = $strQuery;  
			$strUrl = "/people.xml?";	
    	}else{
    		$arrParams = array();
    		$arrParams['criteria'] = array(
    			$strCriteria => $strQuery
    		);
    		$strUrl = '/people/search.xml?';
    	}
    	$strUrl .= urldecode(http_build_query($arrParams));
		//die($strUrl);
		$strXml = self::LoadXML($strUrl);
		//_dv($strXml);
		$xmlResponse = simplexml_load_string((string)$strXml);
		$arrReturn = array();
		foreach($xmlResponse as $strKey => $xmlPerson){
			$arrReturn[] = new MLCHRPeople($xmlPerson);
		}
		
		return $arrReturn;
    }
    public function Materilize($strXml){
    	if(is_string($strXml)){
        	$xmlResponse = simplexml_load_string((string)$strXml);
		}else{
			$xmlResponse = $strXml;
		}

        $this->Id = $xmlResponse->{'id'};
        $this->FirstName = $xmlResponse->{'first-name'};
        $this->FirstName = $xmlResponse->{'first-name'};
        $this->LastName = $xmlResponse->{'last-name'};
        $this->Title = $xmlResponse->{'title'};
        $this->CompanyName = $xmlResponse->{'company-name'};
        $this->Background = $xmlResponse->{'background'};

        //Add phone numbers
        foreach($xmlResponse->{'contact-data'}->{'phone-numbers'} as $xmlPhone){
            $this->AddPhone($xmlPhone->{'phone-number'}->number, $xmlPhone->{'phone-number'}->id);
        }
        //Add email address
        foreach($xmlResponse->{'contact-data'}->{'email-addresses'} as $xmlPhone){
            $this->AddEmail($xmlPhone->{'email-address'}->address, $xmlPhone->{'email-address'}->id);
        }
        //Add website
        foreach($xmlResponse->{'contact-data'}->{'web-addresses'} as $xmlPhone){
            $this->AddWebsite($xmlPhone->{'web-address'}->url, $xmlPhone->{'web-address'}->id);
        }
		//Add twitter
        foreach($xmlResponse->{'contact-data'}->{'twitter-accounts'} as $xmlTwitter){
            $this->AddTwitter($xmlTwitter->{'twitter-account'}->username, $xmlTwitter->{'twitter-account'}->id);
        }
		foreach($xmlResponse->{'subject_datas'} as $xmlTwitter){
			
            $this->AddSubjectData($xmlTwitter->{'subject_data'}->value, $xmlTwitter->{'subject_data'}->{'subject_field_id'});
        }
		
        return $this;

    }
    
    public function __toXml(){
        $strReturn = '<person>';
        if(!is_null($this->intId)){
            $strReturn .= sprintf("<id>%s</id>", $this->intId);
        }
        $strReturn .= sprintf("<first-name>%s</first-name>", $this->strFirstName);
        $strReturn .= sprintf("<last-name>%s</last-name>", $this->strLastName);
        $strReturn .= sprintf("<title>%s</title>", $this->strTitle);
        $strReturn .= sprintf("<company-name>%s</company-name>", $this->strCompanName);
        $strReturn .= sprintf("<background>%s</background>", $this->strBackground);
        $strReturn .= "<contact-data>";
        //Attach email addresses
        $strReturn .= "<email-addresses>";
        foreach($this->arrEmails as $objContactData){
            $strReturn .= $objContactData->__toXml();
        }

        $strReturn .= "</email-addresses>";
        //attach phone numbers
        $strReturn .= "<phone-numbers>";
        foreach($this->arrPhones as $objContactData){
            $strReturn .= $objContactData->__toXml();
        }
        $strReturn .= "</phone-numbers>";
        //attach website numbers
        $strReturn .= "<web-addresses>";
        foreach($this->arrWebsites as $objContactData){
            $strReturn .= $objContactData->__toXml();
        }
        $strReturn .= "</web-addresses>";
		$strReturn .= "<twitter-accounts>";
        foreach($this->arrTwitter as $objContactData){
            $strReturn .= $objContactData->__toXml();
        }
        $strReturn .= "</twitter-accounts>";
   

        $strReturn .= "</contact-data>";
		
	    $strReturn .= "<subject_datas  type='array'>";
        foreach($this->arrSubjectData as $objContactData){
            $strReturn .= $objContactData->__toXml();
        }
        $strReturn .= "</subject_datas>";
        $strReturn .= "</person>";
        return $strReturn;
    }
    public function AddEmail($strDataValue, $intId = null, $strDataLocation = MLCHRPeopleDataLocation::Work){
        $this->arrEmails[] =  new MLCHRPeopleData($strDataValue,MLCHRPeopleDataType::EMAIL, $intId, $strDataLocation);
    }
    public function AddPhone($strDataValue, $intId = null, $strDataLocation = MLCHRPeopleDataLocation::Work){
        $this->arrPhones[] =  new MLCHRPeopleData($strDataValue,MLCHRPeopleDataType::PHONE, $intId, $strDataLocation);
    }
    public function AddWebsite($strDataValue, $intId = null, $strDataLocation = MLCHRPeopleDataLocation::Work){
        $this->arrWebsites[] =  new MLCHRPeopleData($strDataValue,MLCHRPeopleDataType::WEBSITE, $intId, $strDataLocation);
    }
    public function AddTwitter($strDataValue, $intId = null, $strDataLocation = MLCHRPeopleDataLocation::Work){
        $this->arrTwitter[] =  new MLCHRPeopleData($strDataValue,MLCHRPeopleDataType::TWITTER, $intId, $strDataLocation);
    }
    public function AddSubjectData($strDataValue, $intId = null, $strDataLocation = MLCHRPeopleDataLocation::Work){
        $this->arrSubjectData[] =  new MLCHRPeopleData($strDataValue,MLCHRPeopleDataType::SUBJECT_DATA, $intId, $strDataLocation);
    }
    public function AttachTask($strBody, $strFrame, $intCategoryId = null, $intIdOwner = null){
        //if this object does not have an id then we need to save first to get one
        if(is_null($this->intId)){
            $this->Save();
        }
        $objTask = new MLCHRTask();
        $objTask->Body = $strBody;
        $objTask->CategoryId = $intCategoryId;
        $objTask->SubjectId = $this->intId;
        $objTask->SubjectType = MLCHRTaskSubjectType::Party;
		$objTask->Frame = $strFrame;
        $objTask->Save();
        return $objTask;
    }
    public function AttachEmail($strBody, $strTitle = '', $intCategoryId = null){
        //if this object does not have an id then we need to save first to get one
        if(is_null($this->intId)){
            $this->Save();
        }
		
        $objTask = new MLCHREmail();
        $objTask->Body = $strBody;
		$objTask->Title = $strTitle;
        $objTask->CategoryId = $intCategoryId;
        $objTask->SubjectId = $this->intId;
        $objTask->SubjectType = MLCHRTaskSubjectType::Party;
        $objTask->Save();
    }
    public function  __get($strName) {
        switch($strName){
            case('FirstName'):
                return $this->strFirstName;
            break;
            case('LastName'):
                return $this->strLastName;
            break;
            case('Title'):
                return $this->strTitle;
            break;
            case('CompanyName'):
                return $this->strCompanyName;
            break;
            case('Background'):
                return $this->strBackground;
            break;
            case('Emails'):
                return $this->arrEmails;
            default:
                return parent::__get($strName);
            break;
        }
    }
    public function  __set($strName, $strValue) {
        switch($strName){
            case('FirstName'):
                $this->strFirstName = $strValue;
            break;
            case('LastName'):
                 $this->strLastName = $strValue;
            break;
            case('Title'):
                 $this->strTitle = $strValue;
            break;
            case('CompanyName'):
                 $this->strCompanName = $strValue;
            break;
            case('Background'):
                 $this->strBackground = $strValue;
            break;
            default:
                return parent::__set($strName, $strValue);
            break;
        }
    }
   

}
?>
