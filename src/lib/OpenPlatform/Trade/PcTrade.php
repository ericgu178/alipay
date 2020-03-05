<?php
/*
 * @User: EricGU178
 * @Date: 2019-06-19 16:27:12
 * @LastEditors: EricGU178
 * @LastEditTime: 2019-06-27 11:50:16
 */

namespace alipay\lib\OpenPlatform\Trade;
use alipay\lib\Common\Aop;

/**
 * 支付宝电脑网站支付
 *
 * @author EricGU178
 */
class PcTrade extends Index
{

    private $aop; 

    public function __construct($config)
    {
        $this->aop = new Aop($config);
        $this->aop->setReturnUrl($config['return_url']);
        $this->aop->setNotifyUrl($config['notify_url']);
    }

    
    /**
     * 统一收单下单并支付页面接口
     * 
     * PC场景下单并支付
     * 
     * @return void
     * @author EricGU178
     */
    public function pcTradePagePay($data)
    {
        $this->aop->method = 'alipay.trade.page.pay';
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data); //返回结果
        return $result;
    }
}