<?php

/**
 * Created by PhpStorm.
 * User: patpat
 * Date: 2017/11/8
 * Time: PM4:07
 */

namespace PatPatJoint\CloudMessage;

use Illuminate\Support\Facades\Config;

class CloudMessage
{
    const API_BASE_URL              = 'https://message.ucpaas.com/';
    const ROUTE_ACCOUNT             = 'Accounts/'; //此参数为默认固定值
    const ROUTE_CALLS               = 'Calls/';   //业务功能
    const ROUTE_VOICE_NOTIFY        = 'voiceNotify'; //业务操作，业务功能的各类具体操作分支
    const PARAM_SIG                 = 'sig';  //签名验证

    protected $version              = '2014-06-30/'; //云之讯API当前版本号

    protected $accountSid;
    protected $authToken;
    protected $appId;


    public function __construct()
    {
        $this->accountSid = Config::get('cloud_message.accountSid');
        $this->authToken = Config::get('cloud_message.authToken');
        $this->appId = Config::get('cloud_message.appId');
    }

    protected function _getApiData($route, $method = 'GET', $sendData = array(), $authorization = null){
        $method     = strtoupper($method);
        $requestUrl = self::API_BASE_URL.$route;
        $curlObj    = curl_init();
        curl_setopt($curlObj, CURLOPT_URL,$requestUrl);
        if($method == 'GET'){
            curl_setopt($curlObj, CURLOPT_HTTPGET,true);
        }elseif($method == 'POST'){
            curl_setopt($curlObj, CURLOPT_POST, true);
        }elseif ($method == 'PUT'){
            curl_setopt($curlObj, CURLOPT_PUT, true);
        }else{
            curl_setopt($curlObj, CURLOPT_CUSTOMREQUEST, $method);
        }

        curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curlObj, CURLOPT_TIMEOUT, 90);

        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);

        $isDebug = Config::get('app.debug');
        $headers = array(
            'Content-Type: application/json',
        );
        if ($authorization) {
            $headers[] = $authorization;
        }
        if($sendData){
            $dataString = json_encode($sendData);
            curl_setopt($curlObj, CURLOPT_POSTFIELDS, $dataString);
            $headers[] = 'Content-Length: ' . strlen($dataString);
        }
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curlObj);
        curl_close($curlObj);
        unset($curlObj);
        return $response;
    }

    public function sendVoiceNotify(){
        $returnData = array();
        $requestUrl = $this->version.self::ROUTE_ACCOUNT.$this->accountSid.'/'.self::ROUTE_CALLS.self::ROUTE_VOICE_NOTIFY;
        $timestamp = time();
        $sigParameter = md5($this->accountSid.$this->authToken.$timestamp);
        $authorizationBase64 = base64_encode($this->accountSid.':'.$timestamp);
        $authorization = 'Authorization: ' . $authorizationBase64;
        $requestUrl .= '?'.self::PARAM_SIG.'='.$sigParameter;

        $items['appId'] = $this->appId;
        $items['to'] = '13560710913';
        $items['type'] = '2';
        $items['content'] = '服务器异常警报';
        $items['toSerNum'] = '057112345678';
        $items['playTimes'] = '3';
        $items['userData'] = 'patpat';
//        $items['billUrl'] = '';
        $items['templateId'] = '708186';  //语音模板ID
        $sendData['voiceNotify'] = $items;
        $result = $this->_getApiData($requestUrl, 'POST', $sendData, $authorization);

        if ($result) {
            $returnData = json_decode($result, true);
        }
        return $returnData;
    }
}