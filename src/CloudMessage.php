<?php

/**
 * Created by PhpStorm.
 * User: StriverF
 * Date: 2017/11/8
 * Time: PM4:07
 */

namespace PatPat\CloudMessage;

use Illuminate\Support\Facades\Config;

class CloudMessage
{
    const API_BASE_URL              = 'https://message.ucpaas.com/';
    const ROUTE_ACCOUNT             = 'Accounts/'; //此参数为默认固定值
    const ROUTE_CALLS               = 'Calls/';   //业务功能
    const ROUTE_VOICE_NOTIFY        = 'voiceNotify'; //业务操作，业务功能的各类具体操作分支
    const PARAM_SIG                 = 'sig';  //签名验证

    protected $version              = '2017-06-30/'; //云之讯API当前版本号

    protected $accountSid;
    protected $authToken;
    protected $appId;


    public function __construct()
    {
        $this->accountSid = Config::get('cloud_message.account_sid');
        $this->authToken = Config::get('cloud_message.auth_token');
        $this->appId = Config::get('cloud_message.app_id');
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

    /**
     * @param $toNum   | 要通知的电话号码
     * @param $content  | 通知内容
     * @param int $playTimes  | 语音播报次数（默认3次）
     * @param string $requestId  | 自定义透传字段
     * @param string $templateId  | 语音模板ID
     * @return array|mixed
     */
    public function sendVoiceNotify($toNum, $content, $playTimes = 3, $requestId = 'patpat', $templateId = '708186'){
        $returnData = array();
        $requestUrl = $this->version.self::ROUTE_ACCOUNT.$this->accountSid.'/'.self::ROUTE_CALLS.self::ROUTE_VOICE_NOTIFY;
        date_default_timezone_set('PRC');
        $timestamp =  date('YmdHis');
        $sigParameter = md5($this->accountSid.$this->authToken.$timestamp);
        $authorizationBase64 = base64_encode($this->accountSid.':'.$timestamp);
        $authorization = 'Authorization: ' . $authorizationBase64;
        $requestUrl .= '?'.self::PARAM_SIG.'='.$sigParameter;

        $items['appId'] = $this->appId;
        $items['callee'] = $toNum;
        $items['type'] = '0';
        $items['content'] = $content;
//        $items['caller'] = Config::get('cloud_message.caller');
        $items['playTimes'] = $playTimes;
        $items['requestId'] = $requestId;
//        $items['billUrl'] = ''; //话单推送url
//        $items['templateId'] = $templateId;  //语音模板ID
        $sendData['voiceNotify'] = $items;
        $result = $this->_getApiData($requestUrl, 'POST', $sendData, $authorization);
        if ($result) {
            $returnData = json_decode($result, true);
        }
        return $returnData;
    }
}