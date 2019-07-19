<?php
/*
 * @User: EricGU178
 * @Date: 2019-06-19 15:33:14
 * @LastEditors: EricGU178
 * @LastEditTime: 2019-06-26 10:17:24
 */


namespace alipay\lib\OpenPlatform\Trade;
use alipay\lib\Common\Aop;

class Index
{
    /**
     * 方法
     *
     * @var array
     */
    protected $action = [
        'PcTrade'    =>  'PcTrade', // 电脑网站支付
        'MobileTrade'   =>  'MobileTrade', // 手机网站支付
        'F2f' =>  'F2f', // 面对面 支付
        'Unified'   =>  'Unified' // 统一支付（收银扫码起）
    ];

    private $config;
    
    public function __construct($config)
    {
        $this->config = $config;
    }
    
    public function __get($name) 
    {
        $name = self::title($name);
        $class = "\\alipay\\lib\\OpenPlatform\\Trade\\" . $this->action[$name];
        if (class_exists($class)) {
            return new $class($this->config);
        } else {
            throw new \Exception("错误，不存在 {$name} 类");
        }
    }
    
    static private function title($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        $name = str_replace(" ",'', $value);
        return $name;
    }
}