<?php
/*
 * @User: EricGU178
 * @Date: 2019-06-19 16:27:12
 * @LastEditors: EricGU178
 * @LastEditTime: 2019-06-27 16:06:09
 */

namespace alipay\lib\OpenPlatform\Trade;
use alipay\lib\Common\Aop;

/**
 * 统一支付 （收银员扫码枪）
 *
 * @author EricGU178
 */
class Unified extends Index
{

    private $aop; 

    public function __construct($config)
    {
        $this->aop = new Aop($config);
        $this->aop->setReturnUrl($config['return_url']);
        $this->aop->setNotifyUrl($config['notify_url']);
    }

    
    /**
     * 统一收单交易支付接口
     * 
     * 收银员使用扫码设备读取用户手机支付宝“付款码”/声波获取设备（如麦克风）
     * 读取用户手机支付宝的声波信息后，
     * 将二维码或条码信息/声波信息通过本接口上送至支付宝发起支付。
     * 
     * @return void
     * @author EricGU178
     */
    public function tradePay($data)
    {
        $this->aop->method = 'alipay.trade.pay';
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data); //返回结果
        return $result;
    }
}