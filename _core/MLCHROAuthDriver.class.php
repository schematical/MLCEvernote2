<?php
abstract class MLCHROAuthDriver{
    const AUTH_URL = 'https://launchpad.37signals.com/authorization/new';
    const TOKEN_URL = 'https://launchpad.37signals.com/authorization/token';
    protected static $objClient = null;
    protected static $arrUserData = array();
    public static function Init($objUser = null){

        if(is_null($objUser)){
            $objUser = MLCAuthDriver::User();
        }
        if(!is_null($objUser)){
            self::$arrUserData = json_decode(
                $objUser->GetUserSetting(MLCHRUserSetting::user_data),
                true
            );
            if(is_null(self::$arrUserData)){
                self::$arrUserData = array();
            }
        }
        if(is_null(self::$objClient)){

            /*
            $arrCreds = array(
                'sandbox' => EVERNOTE_SANDBOX
            );
            if(array_key_exists(MLCHRUserSetting::access_token, self::$arrUserData)){
                $arrCreds['token'] = self::$arrUserData[MLCHRUserSetting::access_token];
            }else{
                $arrCreds['consumerKey'] = EVERNOTE_OAUTH_CONSUMER_KEY;
                $arrCreds['consumerSecret'] = EVERNOTE_OAUTH_CONSUMER_SECRET;
            }*/
            self::$objClient = new \OAuth(HIGHRISE_CLIENT_ID, HIGHRISE_CLIENT_SECRET);
        }


    }
    public static function Client(){
        return self::$objClient;
    }
    public static function UserData($strKey){
        self::Init();
        if(array_key_exists($strKey, self::$arrUserData)){
            return self::$arrUserData[$strKey];
        }
        return null;
    }
    public static function RunAuth(){
        $objUser = MLCAuthDriver::User();
        if(is_null($objUser)){
            throw new Exception("User Not Logged In");
        }
        self::Init();
        //self::ClearUserData();
        $strUserData = $objUser->GetUserSetting(MLCHRUserSetting::access_token);

        if(is_null($strUserData)){
            if(array_key_exists(MLCHRQS::code, $_GET)){

                $strTokenUrl = self::TOKEN_URL;
                $strTokenUrl .= '?';
                $arrQuery = array();
                $arrQuery['type'] = 'web_server';
                $arrQuery['client_id'] = HIGHRISE_CLIENT_ID;
                $arrQuery['redirect_uri']=HIGHRISE_REDIRECT_URL;
                $arrQuery['client_secret'] = HIGHRISE_CLIENT_SECRET;
                $arrQuery['code'] = $_GET[MLCHRQS::code];
                $strTokenUrl .= http_build_query($arrQuery);

                //die($strTokenUrl);

                /*self::$objClient->getAccessToken(
                    $strTokenUrl,
                    $_GET['oauth_verifier']
                );*/
                $ch = curl_init($strTokenUrl);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-length: 0"));
                $strTransfer = curl_exec($ch);
                curl_close($ch);

                $arrData = json_decode($strTransfer, true);
                $objUser->SetUserSetting(MLCHRUserSetting::user_data, $strTransfer);
                $objUser->SetUserSetting(MLCHRUserSetting::access_token, $arrData['access_token']);

                //$objUser->SetUserSetting(MLCHRUserSetting::oauth_token_secret, $strSecret);
                die(header('location:' . HIGHRISE_REDIRECT_URL));
            }else{

                //self::$objClient->getRequestToken(self::AUTH_URL, HIGHRISE_REDIRECT_URL);


                $strRedirectUrl = self::GetRedirectUrl();

                die(header('location:' . $strRedirectUrl));
            }
        }
    }
    public static function GetRedirectUrl(){
        $arrData = array();
        $arrData['type'] = 'web_server';
        $arrData['client_id']= HIGHRISE_CLIENT_ID;
        $arrData['redirect_uri'] = HIGHRISE_REDIRECT_URL;
        $strRedirectUrl = self::AUTH_URL . '?' . http_build_query($arrData);
        return $strRedirectUrl;
    }
    public static function ClearUserData(){
        $objUser = MLCAuthDriver::User();
        if(is_null($objUser)){
            throw new MLCHRMLCAuthException("User Not Logged In");
        }
        $objUser->SetUserSetting(MLCHRUserSetting::access_token, null);
        $objUser->SetUserSetting(MLCHRUserSetting::oauth_token, null);
        $objUser->SetUserSetting(MLCHRUserSetting::oauth_token_secret, null);
        $objUser->SetUserSetting(MLCHRUserSetting::user_data, null);

    }
    public static function GetRequestToken($strCallbackUrl = null){
        if(is_null($strCallbackUrl)){
            $strCallbackUrl = constant('EVERNOTE_CALLBACK');
        }
        self::Init();
        return self::$objClient->getRequestToken($strCallbackUrl);
    }
    public static function GetAuthUrl($strRequestToken){
        self::Init();

        return self::$objClient->getAuthorizeUrl($strRequestToken);
    }
    public static function GetAccessToken(){
        self::Init();
        try {
            $accessTokenInfo = self::$objClient->getAccessToken($_SESSION['requestToken'], $_SESSION['requestTokenSecret'], $_SESSION['oauthVerifier']);
            if ($accessTokenInfo) {
                $_SESSION['accessToken'] = $accessTokenInfo['oauth_token'];
                $currentStatus = 'Exchanged the authorized temporary credentials for token credentials';

                return TRUE;
            } else {
                $lastError = 'Failed to obtain token credentials.';
            }
        } catch (OAuthException $e) {
            $lastError = 'Error obtaining token credentials: ' . $e->getMessage();
        }

    }
}