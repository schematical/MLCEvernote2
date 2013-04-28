<?php
abstract class MLCEvernoteDriver{
    protected static $objClient = null;
    protected static $arrUserData = array();
    public static function Init($objUser = null){
        if(is_null($objUser)){
            $objUser = MLCAuthDriver::User();
        }
        if(!is_null($objUser)){
            self::$arrUserData = json_decode(
                $objUser->GetUserSetting(MLCENUserSetting::user_data),
                true
            );
            if(is_null(self::$arrUserData)){
                self::$arrUserData = array();
            }
        }
        if(is_null(self::$objClient)){
            $arrCreds = array(
                'sandbox' => EVERNOTE_SANDBOX
            );

            if(array_key_exists(MLCENUserSetting::access_token, self::$arrUserData)){
                $arrCreds['token'] = self::$arrUserData[MLCENUserSetting::access_token];
            }else{
                $arrCreds['consumerKey'] = EVERNOTE_OAUTH_CONSUMER_KEY;
                $arrCreds['consumerSecret'] = EVERNOTE_OAUTH_CONSUMER_SECRET;
            }
            self::$objClient = new \Evernote\Client($arrCreds);
        }


    }
    public static function Client(){
        self::Init();
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
           throw new MLCENMLCAuthException("User Not Logged In");
        }
        self::Init();
        //self::ClearUserData();
        $strUserData = $objUser->GetUserSetting(MLCENUserSetting::user_data);
        if(is_null($strUserData)){
            if(array_key_exists(MLCENQS::oauth_token, $_GET)){

                $arrData = self::$objClient->getAccessToken(
                    $objUser->GetUserSetting(MLCENUserSetting::oauth_token),
                    $objUser->GetUserSetting(MLCENUserSetting::oauth_token_secret),
                    $_GET['oauth_verifier']
                );
                $arrData[MLCENUserSetting::access_token] = $arrData[MLCENQS::oauth_token];
                $objUser->SetUserSetting(MLCENUserSetting::edam_userId, $arrData[MLCENUserSetting::edam_userId]);

                $objUser->SetUserSetting(MLCENUserSetting::user_data, json_encode($arrData));

                die(header('location:' . EVERNOTE_CALLBACK));
            }else{
                $arrData = self::GetRequestToken();
                $strRequestToken = $arrData[MLCENQS::oauth_token];
                $objUser->SetUserSetting(MLCENUserSetting::oauth_token, $strRequestToken);
                $strSecret = $arrData[MLCENQS::oauth_token_secret];
                $objUser->SetUserSetting(MLCENUserSetting::oauth_token_secret, $strSecret);
                $strRedirectUrl = self::GetAuthUrl($strRequestToken);
                die(header('location:' . $strRedirectUrl));
            }
        }
    }
    public static function ClearUserData(){
        $objUser = MLCAuthDriver::User();
        if(is_null($objUser)){
            throw new MLCENMLCAuthException("User Not Logged In");
        }
        $objUser->SetUserSetting(MLCENUserSetting::access_token, null);
        $objUser->SetUserSetting(MLCENUserSetting::oauth_token, null);
        $objUser->SetUserSetting(MLCENUserSetting::oauth_token_secret, null);
        $objUser->SetUserSetting(MLCENUserSetting::user_data, null);

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
    public static function ParseWebhookData($arrData = null){
        if(is_null($arrData)){
            $arrData = $_GET;
        }

        $objUser = AuthUser::QueryByUserSetting(MLCENUserSetting::edam_userId, $arrData['userId']);

        self::Init($objUser);
        $arrData['user'] = $objUser;
        //guid=[noteGuid]&reason=[create | update]
        return $arrData;

    }
    public static function GetNote($strNoteGuid,  $withContent = false, $withResourcesData = false, $withResourcesRecognition = false, $withResourcesAlternateData = false){
        self::Init();


        $arrNoteData = self::$objClient->getNoteStore()->getNote(
            self::UserData(MLCENUserSetting::access_token),
            $strNoteGuid,
            $withContent,
            $withResourcesData,
            $withResourcesRecognition,
            $withResourcesAlternateData
        );
        return $arrNoteData;
    }

}