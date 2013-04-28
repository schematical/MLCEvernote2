<?php
abstract class MLCENUserSetting{
    const access_token = 'access_token';
    const oauth_token = 'oauth_token';
    const oauth_token_secret = 'oauth_token_secret';
    const user_data = 'user_data';//JSON ENCODE OF USER DATA
    const edam_userId = 'edam_userId';

}
abstract class MLCENQS{
    const access_token = 'access_token';
    const oauth_token = 'oauth_token';
    const oauth_token_secret = 'oauth_token_secret';
    const guid = 'guid';
    const noteGuid = 'noteGuid';
    const reason = 'reason';

}