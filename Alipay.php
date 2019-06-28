<?php
/*
 * @User: EricGU178
 * @Date: 2019-01-26 11:58:27
 * @LastEditors: EricGU178
 * @LastEditTime: 2019-01-26 16:22:40
 */
namespace alipay;

class Alipay
{
    static public function init($name,array $config)
    {
        $namespace = self::title($name);
        $index = "\\alipay\\lib\\{$namespace}\\Run";
        if(class_exists($index)){
            return new $index($config);
        }else{
            trigger_error("错误alipay::{$name}，请检查");
        }
    }

    /**
     * 重载
     *
     * @return void
     * @author EricGU178
     */
    static public function __callStatic($name, $arguments)
    {
        return self::init($name, ...$arguments);//参数展开
    }

    static private function title($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return $value;
    }
}