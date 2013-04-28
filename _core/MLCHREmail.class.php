<?php
/*
 *This gets and saves client data for a high rise task
 */
class MLCHREmail extends MLCHRObjectBase{
    protected static $strClassName = __CLASS__;

    protected $strCreateUrl = "/emails.xml";
    protected $strUpdateUrl = "/emails/%s.xml?reload=true";

    protected $intSubjectId = null;
    protected $strSubjectType = null;
    protected $strBody = null;
	protected $strTitle = null;
    //protected $intRecordingId = null;
    protected $intCategoryId = null;


    
    public function Materilize($strResponse){
    	if(is_string($strResponse)){
        	$xmlResponse = simplexml_load_string((string)$strResponse);
		}else{
			$xmlResponse = $strResponse;
		}
        $this->Id = $xmlResponse->{'id'};
        $this->Body = $xmlResponse->{'body'};
        $this->CategoryId = $xmlResponse->{'category-id'};
        //$this->RecordingId = $xmlResponse->{'recording-id'};
        $this->SubjectId = $xmlResponse->{'subject-id'};
        $this->SubjectType = $xmlResponse->{'subject-type'};
        
		$this->Title = $xmlResponse->{'title'};
        return $this;

    }
    public function __toXml(){
        $strReturn = '<email>';
        if(!is_null($this->intId)){
            $strReturn .= sprintf("<id>%s</id>", $this->intId);
        }
        $strReturn .= sprintf("<body>%s</body>", htmlspecialchars($this->strBody));
        //$strReturn .= sprintf("<category-id>%s</category-id>", $this->intCategoryId);
        //$strReturn .= sprintf("<recording-id>%s</recording-id>", $this->intRecordingId);
        $strReturn .= sprintf("<subject-id>%s</subject-id>", $this->intSubjectId);
        $strReturn .= sprintf("<subject-type>%s</subject-type>", $this->strSubjectType);
        $strReturn .= sprintf("<title>%s</title>", $this->strTitle);

        $strReturn .= "</email>";
        return $strReturn;
    }
    public function  __get($strName) {
        switch($strName){
            case('Body'):
                return $this->strBody;
            break;
            case('CategoryId'):
                return $this->intCategoryId;
            break;
            /*case('RecordingId'):
                return $this->intRecordingId;
            break;*/
            case('SubjectId'):
                return $this->intSubjectId;
            break;
            case('SubjectType'):
                return $this->strSubjectType;
            break;
            case('Title'):
                return $this->strTitle;
            break;
            default:
                return parent::__get($strName);
            break;
        }
    }
    public function  __set($strName, $strValue) {
        switch($strName){
            case('Body'):
                $this->strBody = $strValue;
            break;
            case('CategoryId'):
                 $this->intCategoryId = $strValue;
            break;
            /*case('RecordingId'):
                 $this->intRecordingId = $strValue;
            break;*/
            case('SubjectId'):
                 $this->intSubjectId = $strValue;
            break;
            case('SubjectType'):
                 $this->strSubjectType = $strValue;
            break;
            case('Title'):
                 $this->strTitle = $strValue;
            break;
            default:
                return parent::__set($strName, $strValue);
            break;
        }
    }


}
?>
