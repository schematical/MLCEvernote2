<?php
/* 
 * This class handels some of the base functionality that is shared between all Hirise API savable entitys
 */
abstract class MLCHRObjectBase{
    public static $strOAuth = null;
    protected $intId = null;

    public function  __construct($strXml = null) {
        if(!is_null($strXml)){
            $this->Materilize($strXml);
        }
    }
    public function LoadById($intId){
        $strUrl = __HIGHRISE_URL__. sprintf($this->strUpdateUrl,$intId);
        return self::LoadByUrl($strUrl);
    }
    public function __toJson(){
        $arrData = $this->__toArray();
        $arrData['_ClassName'] = get_class($this);
        return json_encode($arrData);
    }
    public static function LoadByUrl($strUrl){
        $strResponse = self::LoadXML($strUrl);
        //die($strResponse);
        $strClassName = self::$strClassName;
        $objHR = new $strClassName(strResponse);
        return $objHR;
    }
	
    public function Save(){
        $objCurl = $this->GetCURLObject();
		if(is_null($this->intId)){
            $strUrl = $this->strCreateUrl;
			curl_setopt($objCurl, CURLOPT_POST,true);
        }else{
            $strUrl = sprintf($this->strUpdateUrl, $this->intId);
			curl_setopt($objCurl, CURLOPT_CUSTOMREQUEST, "PUT");
        }
		//echo('0000000000000000000000000000000000000000000000000000' . __HIGHRISE_URL__ . $strUrl);
        //curl_setopt($objCurl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($objCurl, CURLOPT_URL, __HIGHRISE_URL__ . $strUrl);
		
		echo($this->__toXml() . "\n");
        curl_setopt($objCurl, CURLOPT_POSTFIELDS, $this->__toXml());

        $strResponse = curl_exec($objCurl);
		//echo($strResponse);
        $this->Materilize($strResponse);
        $strStatus = curl_getinfo($objCurl,CURLINFO_HTTP_CODE);
        curl_close($objCurl);
    }
    public function  __get($strName) {
        switch($strName){
            case('Id'):
                return $this->intId;
            break;
            default:
                throw new Exception("No property (" . $strName . ")");
            break;
        }
    }
    public function  __set($strName, $strValue) {
        switch($strName){
            case('Id'):
                $this->intId = $strValue;
            break;
            default:
                throw new Exception("No property (" . $strName . ")");
            break;
        }
    }

    public static function GetCURLObject(){
        $objCurl = curl_init();
        curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($objCurl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($objCurl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($objCurl,CURLOPT_SSL_VERIFYHOST,0);
        $arrHeaders = array();
		if(!is_null(self::$strOAuth)){
            $arrHeaders[] = 'Authorization: Bearer ' . self::$strOAuth;
        }elseif(defined('__HIGHRISE_API_KEY__')){
            curl_setopt($objCurl, CURLOPT_USERPWD,__HIGHRISE_API_KEY__.':x');
        }else{
            curl_setopt($objCurl, CURLOPT_USERPWD, __HIGHRISE_USERNAME__ . ":" . __HIGHRISE_PASSWORD__);
        }

        $arrHeaders[] = 'Content-Type: application/xml';        ;
        curl_setopt($objCurl, CURLOPT_HTTPHEADER, $arrHeaders);
        return $objCurl;
     }
     
    public static function LoadXML($strUrl){
        $objCurl = self::GetCURLObject();
        $strFullUrl = __HIGHRISE_URL__ . $strUrl;
        curl_setopt($objCurl, CURLOPT_URL, $strFullUrl);

        $strResponse = curl_exec($objCurl);

        $strStatus = curl_getinfo($objCurl, CURLINFO_HTTP_CODE);
        curl_close($objCurl);
        return $strResponse;
    }
    
}
?>
