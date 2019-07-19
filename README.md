# 如何使用

### 首先是配置文件
```
<?php
    [
        "request_url"   =>  "https://openapi.alipaydev.com/gateway.do", //"https://openapi.alipay.com/gateway.do?charset=utf-8",
        "app_id"    =>  "", // 
        "private_key"   =>  '', //商户私钥
        "result_format" =>  "array", //返回类型
        "log_path"  =>  "", // 日志
        'return_url'    =>  '', // 
        'notify_url'    =>  '', //
        'public_key'    =>'' // 支付宝公钥
    ];
```

`composer require ericgu178/alipay` 安装


```
use alipay\Alipay;

$data = [
    'out_trade_no'  =>  date('YmdHis') time(),
    'total_amount'  =>  123,
    'scene' =>  'bar_code',
    'subject'   =>  'asdas',
    'body'  =>  '购买商品1件共123.00元',
    'seller_id' =>  '2088102176553156',
    'extend_params' =>  [
        'sys_service_provider_id'   =>'2088712232312861'
    ],
    'auth_code' =>  '284106663673410926'
];
$config = [
        "request_url"   =>  "https://openapi.alipaydev.com/gateway.do", //"https://openapi.alipay.com/gateway.do?charset=utf-8",
        "app_id"    =>  "", // 
        "private_key"   =>  '', //商户私钥 中间不要留有空格 
        "result_format" =>  "array", //返回类型
        "log_path"  =>  "", // 日志
        'return_url'    =>  '', // 
        'notify_url'    =>  '', //
        'public_key'    =>'' // 支付宝公钥 一行
];
$aop = Alipay::OpenPlatform($config);
$response = $aop->trade->Unified->tradePay($data);
print_r($response);
```