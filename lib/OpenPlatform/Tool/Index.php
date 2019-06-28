<?php
/*
 * @User: EricGU178
 * @Date: 2019-01-29 10:16:49
 * @LastEditors: EricGU178
 * @LastEditTime: 2019-06-25 10:05:09
 */
namespace alipay\lib\OpenPlatform\Tool;
use alipay\lib\Common\Aop;
/**
 * 工具类api
 *
 * @author EricGU178
 */
class Index
{
    private $aop; 

    public function __construct($config)
    {
        $this->aop = new Aop($config);
    }
    /**
     * 换取授权访问令牌
     * 
     * @return void
     */
    public function oauthToken($code,$grant_type="authorization_code")
    {
        $data['grant_type'] =   $grant_type;
        $data['code']   =   $code;
        $this->aop->method = "alipay.system.oauth.token";
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data,false); //返回结果
        return $result;
    }
}
