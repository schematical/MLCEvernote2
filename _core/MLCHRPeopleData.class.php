<?php
/* 
 * This holds some specific contact data associated with a High Rise Contact
 */
class MLCHRPeopleData{
    protected $strUrl = '/people';
    protected $strId = 'id';
    protected $intId = null;
	protected $strExtra = '';
    protected $strDataType = null;
    protected $strDataValue = null;
    protected $strDataLocation = null;
    public function __construct($strDataValue, $strDataType, $intId = null, $strDataLocation = MLCHRContactDataLocation::Work) {
        $this->strDataType = $strDataType;
        $this->strDataValue = $strDataValue;
        $this->intId = $intId;
        $this->strDataLocation = $strDataLocation;
        
    }
    public function __toXml(){
        $strReturn = sprintf("<%s>", $this->strDataType);
              switch($this->strDataType){
                  case(MLCHRPeopleDataType::EMAIL):
                      $strNodeName = MLCHRPeopleDataTypeNode::EMAIL;
                  break;
                  case(MLCHRPeopleDataType::PHONE):
                      $strNodeName = MLCHRPeopleDataTypeNode::PHONE;
                  break;
                  case(MLCHRPeopleDataType::WEBSITE):
                      $strNodeName = MLCHRPeopleDataTypeNode::WEBSITE;
                  break;
				  case(MLCHRPeopleDataType::TWITTER):
                      $strNodeName = MLCHRPeopleDataTypeNode::TWITTER;
                  break;
				  case(MLCHRPeopleDataType::SUBJECT_DATA):
                      $strNodeName = MLCHRPeopleDataTypeNode::SUBJECT_DATA;
					  $this->strId = 'subject_field_id';
					  $this->strExtra = '';
                  break;
                  default:
                    throw new Exception("There is no Node Name for DataType (" . $this->strDataType . ")");
                  break;
              }
        if(!is_null($this->intId)){
            $strReturn .= sprintf("<%s %s>%s</%s>", $this->strId, $this->strExtra, $this->intId, $this->strId);
        }
        $strReturn .= sprintf("<%s>%s</%s>", $strNodeName, $this->strDataValue, $strNodeName);
		if($this->strDataType != MLCHRPeopleDataType::SUBJECT_DATA){
        	$strReturn .= sprintf("<location>%s</location>", $this->strDataLocation);
		}
        $strReturn .= sprintf("</%s>", $this->strDataType);
        return $strReturn;


    }
}
?>
