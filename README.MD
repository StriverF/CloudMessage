
.env 需要配置的参数,新增config文件 cloud_message.php 内容如下
 
    //注册云之讯官网，在控制台中即可获取此参数
    'account_sid' =>  env('CLOUD_MESSAGE_ACCOUNT_SID', ''),
    
    //账户授权令牌, 在控制台中即可获取此参数
    'auth_token' =>  env('CLOUD_MESSAGE_AUTH_TOKEN', ''),
    
    //应用id
    'app_id' =>  env('CLOUD_MESSAGE_APP_ID', ''),
    
    //来电显示的号码（需要去申请开通号码）
    'to_ser_num' => env('TO_SER_NUM', '057112345678'),
    
    
使用
   
    $cloudMessage = new CloudMessage();
    $result = $cloudMessage->sendVoiceNotify("13560710913", "服务器异常警告");
    if($result && $result['resp']['respCode'] == '000000'){
        //成功
    }else{
        //失败, error_code: $result['resp']['respCode']
    }