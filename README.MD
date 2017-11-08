
.env 需要配置的参数
 
    //注册云之讯官网，在控制台中即可获取此参数
    'accountSid' =>  env('CLOUD_MESSAGE_ACCOUNT_SID', ''),

    //账户授权令牌, 在控制台中即可获取此参数
    'authToken' =>  env('CLOUD_MESSAGE_AUTH_TOKEN', ''),

    //应用id
    'appId' =>  env('CLOUD_MESSAGE_APP_ID', ''),
    
    
使用
   
    $cloudMessage = new CloudMessage();
    $result = $cloudMessage->sendVoiceNotify();