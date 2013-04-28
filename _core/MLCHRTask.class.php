<?php
/*
 *This gets and saves client data for a high rise task
 */
class MLCHRTask extends MLCHRObjectBase{
    protected static $strClassName = __CLASS__;

    protected $strCreateUrl = "/tasks.xml";
    protected $strUpdateUrl = "/task/%s.xml?reload=true";

    protected $intSubjectId = null;
    protected $strSubjectType = null;
    protected $strBody = null;
    protected $intRecordingId = null;
    protected $intCategoryId = null;
    protected $strFrame = null;
	protected $intOwnerId = null;

    public static function LoadByUrl($strUrl){
        $strResponse = self::LoadXML($strUrl);
        //_dv($strResponse);
        $xmlResposne = simplexml_load_string((string)$strResponse);
        $arrReturn = array();
        $strClassName = self::$strClassName;

        foreach($xmlResposne->task as $intIndex => $xmlTask){
            $arrReturn[] = new $strClassName($xmlTask);
        };

        return $arrReturn;
    }
    public function Materilize($strResponse){
        if(is_string($strResponse)){
            $xmlResponse = simplexml_load_string((string)$strResponse);
        }else{
            $xmlResponse = $strResponse;
        }
        $this->Id = $xmlResponse->{'id'};
        $this->Body = $xmlResponse->{'body'};
        $this->CategoryId = $xmlResponse->{'category-id'};
        $this->RecordingId = $xmlResponse->{'recording-id'};
        $this->SubjectId = $xmlResponse->{'subject-id'};
        $this->SubjectType = $xmlResponse->{'subject-type'};
        $this->Frame = $xmlResponse->{'frame'};
		$this->OwnerId = $xmlResponse->{'owner-id'};

        return $this;

    }
    public function __toXml(){
        $strReturn = '<task>';
        if(!is_null($this->intId)){
            $strReturn .= sprintf("<id>%s</id>", $this->intId);
        }
        $strReturn .= sprintf("<body>%s</body>", $this->strBody);
        $strReturn .= sprintf("<category-id>%s</category-id>", $this->intCategoryId);
        $strReturn .= sprintf("<recording-id>%s</recording-id>", $this->intRecordingId);
        $strReturn .= sprintf("<subject-id>%s</subject-id>", $this->intSubjectId);
        $strReturn .= sprintf("<subject-type>%s</subject-type>", $this->strSubjectType);
        $strReturn .= sprintf("<frame>%s</frame>", $this->strFrame);
		$strReturn .= sprintf("<owner-id>%s</owner-id>", $this->intOwnerId);

        $strReturn .= "</task>";
        return $strReturn;
    }
    public function __toArray(){
        $arrReturn = array();

        if(!is_null($this->intId)){
            $arrReturn['id'] = $this->intId;
        }
        $arrReturn['body'] = (String)$this->strBody;
        $arrReturn['category-id'] = (String)$this->intCategoryId;
        $arrReturn['recording-id'] = (String)$this->intRecordingId;
        $arrReturn['subject-id'] = (String)$this->intSubjectId;
        $arrReturn['subject-type'] = (String)$this->strSubjectType;
        $arrReturn['frame'] = (String)$this->strFrame;
        $arrReturn['owner-id'] = (String)$this->intOwnerId;
        return $arrReturn;

    }
    public function  __get($strName) {
        switch($strName){
            case('Body'):
                return $this->strBody;
            break;
            case('CategoryId'):
                return $this->intCategoryId;
            break;
            case('RecordingId'):
                return $this->intRecordingId;
            break;
            case('SubjectId'):
                return $this->intSubjectId;
            break;
            case('SubjectType'):
                return $this->strSubjectType;
            break;
            case('Frame'):
                return $this->strFrame;
            break;
            case('OwnerId'):
                return $this->intOwnerId;
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
            case('RecordingId'):
                 $this->intRecordingId = $strValue;
            break;
            case('SubjectId'):
                 $this->intSubjectId = $strValue;
            break;
            case('SubjectType'):
                 $this->strSubjectType = $strValue;
            break;
            case('Frame'):
                 $this->strFrame = $strValue;
            break;
			case('OwnerId'):
                 $this->intOwnerId = $strValue;
            break;
            default:
                return parent::__set($strName, $strValue);
            break;
        }
    }


}
?>
