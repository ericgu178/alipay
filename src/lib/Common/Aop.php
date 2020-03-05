<?php
namespace alipay\lib\Common;

use alipay\tools\Tool;
use alipay\log\Log;

class Aop extends Tool
{
    /**
     * 日志
     */
    protected $log;
    /**
     * 支付宝分配给开发者的应用ID
     */
    public $app_id;
    /**
     * 接口名称
     */
    public $method;
    /**
     * 返回格式
     */
    public $format = "json";
    /**
     * 编码
     */
    public $charset = "UTF-8";
    /**
     * 商户生成签名字符串所使用的签名算法类型
     */
    public $sign_type = "RSA2";
    /**
     * 签名
     */
    public $sign = null;

    /**
     * 版本
     */
    public $version = "1.0";
    /**
     * 发送请求的时间
     *
     * @var string
     */
    public $timestamp;

    /**
     * 针对用户授权接口，获取用户相关数据时，用于标识用户授权关系
     *
     * @var string
     */
    public $auth_token = null;

    /**
     * 请求参数的集合
     *
     * @var string
     */
    public $biz_content = [];
    
    /**
     * 商户私钥
     */
    public $private_key;

    /**
     * 支付宝公钥
     */
    public $public_key;

    public $url;

    /**
     * get 请求 return_url
     *
     * @var string
     */
    public $return_url = null;

    /**
     * post 请求 notify_url
     *
     * @var string
     */
    public $notify_url = null;


    // 初始化
    public function __construct($config)
    {
        // 请求所需要的参数
        $this->url         = !isset($config['request_url']) ? trigger_error("请求url 缺失") : $config['request_url'];
        $this->app_id      = !isset($config['app_id']) ? trigger_error("app_id 缺失") : $config['app_id'];
        $this->private_key = !isset($config['private_key']) ? trigger_error("私钥 缺失") : $config['private_key'];
        $this->public_key  = !isset($config['public_key']) ? trigger_error('支付宝公钥 缺失') : $config['public_key'];
 
        if (isset($config['sign_type'])) {
            $this->sign_type = $config['sign_type'];
        }
        if (isset($config['version'])) {
            $this->version = $config['version'];
        }
        if (isset($config['result_format']) && $config['result_format'] == "array") {
            $this->result_format = $config['result_format'];
        }

        // 日志路径
        $this->log      = Log::instance();
        $this->log->dir = $config['log_path'];
    }


    /**
     * 整理数据发送请求
     *
     * @param array $data
     * @return void
     * @author EricGU178
     */
    public function execute($data, $type = true)
    {
        $request = [
            'app_id'      => $this->app_id,
            'method'      => $this->method,
            'format'      => $this->format,
            'charset'     => $this->charset,
            'sign_type'   => $this->sign_type,
            'version'     => $this->version,
            'timestamp'   => $this->timestamp,
            'biz_content' => json_encode($data, JSON_UNESCAPED_UNICODE)
        ];

        if (!is_null($this->return_url)) {
            $request['return_url'] = $this->return_url;
        }

        if (!is_null($this->notify_url)) {
            $request['notify_url'] = $this->notify_url;
        }

        $this->log->dir('alipay_result/request')->header('请求结果')->info($request);

        if (!is_null($this->auth_token)) {
            $request['auth_token'] = $this->auth_token;
        }
        if (!$type) {
            unset($request["biz_content"]);
            $request = array_merge($request, $data);
        }

        $request['sign'] = $this->setSign($request);
        $this->log->dir('alipay_result/response')->header('签名返回结果')->info($request['sign']);

        $pre_result = $this->curl_post_https($this->url, $request);
        $this->log->dir('alipay_result/response')->header('预处理结果')->info($pre_result);

        if ($this->syncVerifySign($pre_result) === false) {
            \trigger_error('验证签名失败,请检查');
        }

        $result = $this->setResult($pre_result, $request['sign']);
        $this->log->dir('alipay_result/response')->header('返回结果')->info($result);

        return $result;
    }

    
    /**
     * 生成签名
     *
     * @param array $data
     * @return string
     * @author EricGU178
     */
    private function setSign($data)
    {
        $private_key = formatPrivateKey($this->private_key);
        $pri_key_id  = openssl_pkey_get_private($private_key);
        if (!$pri_key_id) {
            trigger_error("商户私钥缺失");
        }
        if (empty($data)) {
            trigger_error("签名数据缺失");
        }
        if (isset($data['image_content'])) {
            unset($data['image_content']);
        }
        $str = null;
        ksort($data);
        foreach ($data as $key => $value) {
            $value = $this->characet($value, $this->charset);
            $str   .= "{$key}={$value}&";
        }
        $str = rtrim($str, '&');
        $this->log->dir('alipay_result/response')->header('签名内容')->info($str);
        return openssl_sign($str, $sign, $pri_key_id, OPENSSL_ALGO_SHA256) ? base64_encode($sign) : trigger_error("签名错误，请检查");
    }

    /**
     * 处理返回结果
     *
     * @param $pre_result 预返回结果
     * @param $sign 签名
     * @return array
     * @author EricGU178
     */
    private function setResult($pre_result, $sign)
    {
        $responseStr = str_replace('.', '_', $this->method) . "_response";

        if (empty($pre_result)) {
            return [
                'code'     => 10003,
                'msg'      => 'empty result',
                'sub_code' => 'isp.unknow-error',
                'sub_msg'  => "遇到罕见的返回结果为空",
                'sign'     => $sign
            ];
        }
        // 不存在 不处理 原数据返回 
        if (!isset($pre_result[$responseStr])) {
            return $pre_result;
        }

        foreach ($pre_result[$responseStr] as $key => $value) {
            $result[$key] = $value;
        }

        unset($pre_result[$responseStr]);

        $result = array_merge($result, $pre_result);

        return $result;
    }

    /**
     * 设置return_url
     *
     * @param string $url
     * @return void
     * @author EricGU178
     */
    public function setReturnUrl($url)
    {
        $this->return_url = $url;
    }

    /**
     * 设置通知回调
     *
     * @param string $url
     * @return void
     * @author EricGU178
     */
    public function setNotifyUrl($url)
    {
        $this->notify_url = $url;
    }

    /**
     * 转换编码格式
     *
     * @param string $data
     * @param string $targetCharset
     * @return void
     * @author EricGU178
     */
    protected function characet($data, $targetCharset)
    {
        // 先写死utf-8
        if (!empty($data)) {
            if (strcasecmp('UTF-8', $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, 'UTF-8');
            }
        }
        return $data;
    }

    /**
     * 同步验签
     *
     * 开发者只对支付宝返回的json中xxx_response的值做验签（xxx代表接口名）。
       xxx_response的json值内容需要包含首尾的“{”和“}”两个尖括号，
       双引号也需要参与验签，
       如果字符串中包含“http://”的正斜杠，
       需要先将正斜杠做转义，
       默认打印出来的字符串是已经做过转义的。
       建议验签不通过时将正斜杠转义一次后再做一次验签。
     * @param 验签字符串
     * @return void
     * @author EricGU178
     */
    protected function syncVerifySign($toSign)
    {
        $responseStr = str_replace('.', '_', $this->method) . "_response";
        if (!is_array($toSign)) {
            $toSign = json_decode($toSign,true);
        }

        if (!isset($toSign[$responseStr])) {
            return true; // 不验证 直接验证通过返回 无伤大雅
        }
        $sign = $toSign['sign'];
        $str = json_encode($toSign[$responseStr],JSON_UNESCAPED_UNICODE); // 如果验证签名失败 极有可能是转义的问题

        $this->public_key = formatPubKey($this->public_key);
        $public_key_id = openssl_pkey_get_public($this->public_key);
        $result = openssl_verify($str,base64_decode($sign),$public_key_id,OPENSSL_ALGO_SHA256);
        openssl_free_key($public_key_id);
        return $result === 1 ? true : false;
    }
}

/**
 * 公钥格式化
 *
 * @param string $pubKey
 * @return void
 * @author EricGU178
 */
function formatPubKey($pubKey) {
    $fKey = "-----BEGIN PUBLIC KEY-----\n";
    $len = strlen($pubKey);
    for($i = 0; $i < $len; ) {
        $fKey = $fKey . substr($pubKey, $i, 64) . "\n";
        $i += 64;
    }
    $fKey .= "-----END PUBLIC KEY-----";
    return $fKey;
}

/**
 * 私钥格式化 RSA2 格式
 *
 * @param string $privateKey
 * @return void
 * @author EricGU178
 */
function formatPrivateKey($privateKey) {
    $fKey = "-----BEGIN RSA PRIVATE KEY-----\n";
    $len = strlen($privateKey);
    for($i = 0; $i < $len;) {
        $fKey = $fKey . substr($privateKey, $i, 64) . "\n";
        $i += 64;
    }
    $fKey .= "-----END RSA PRIVATE KEY-----";
    return $fKey;
}

