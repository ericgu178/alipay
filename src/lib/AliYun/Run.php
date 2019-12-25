<?php
namespace alipay\lib\AliYun;

class Run
{
    /**
     * 配置
     *
     * @var string
     */
    private $config;

    public function __construct($config=[])
    {
        if(empty($config)){
            throw new \Exception("阿里配置文件出错");
        }
        $this->config = $config;
    }

    public function __get($name) 
    {
        $classname = self::title($name);
        $class = "\\alipay\\lib\\AliYun\\{$classname}\\Index";
        if(class_exists($class)){
            return new $class($this->config);
        }else{
            throw new \Exception("暂时没有这个类");
        }
    }
    
    static private function title($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        $name = str_replace(" ",'', $value);
        return $name;
    }
}
