<?php
/**
 * Created by PhpStorm.
 * User: StriverF
 * Date: 2017/11/8
 * Time: PM6:19
 */

return [

    //注册云之讯官网，在控制台中即可获取此参数
    'accountSid' =>  env('CLOUD_MESSAGE_ACCOUNT_SID', ''),

    //账户授权令牌, 在控制台中即可获取此参数
    'authToken' =>  env('CLOUD_MESSAGE_AUTH_TOKEN', ''),

    //应用id
    'appId' =>  env('CLOUD_MESSAGE_APP_ID', ''),

];