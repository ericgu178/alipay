# ericgu178/alipay

封装支付宝接口

> 支付宝会员卡

> 支付宝支付

### 如何使用

首先配置文件内容如下

```
<?php
return [
    'alipay'    =>  [
        "request_url"   =>  "https://openapi.alipaydev.com/gateway.do", //"https://openapi.alipay.com/gateway.do?charset=utf-8", // 支付宝网关
        "app_id"    =>  "" // 支付宝的app_id
        "private_key"   =>  '-----BEGIN RSA PRIVATE KEY-----
-----END RSA PRIVATE KEY-----', // 商户私钥
        "result_format" =>  "array", // 返回类型 
        "log_path"  =>  "/log", //日志目录
        'return_url'    =>  'http://cj_member.xinliu.org/index/index/notify', // 跳转地址
        'notify_url'    =>  'http://cj_member.xinliu.org/index/index/notify', // 异步回调地址
        'public_key'    => '' // 支付宝公钥
]
    ];
```

然后

```
$config = [
        "request_url"   =>  "https://openapi.alipaydev.com/gateway.do", //"https://openapi.alipay.com/gateway.do?charset=utf-8", // 支付宝网关
        "app_id"    =>  "" // 支付宝的app_id
        "private_key"   =>  '-----BEGIN RSA PRIVATE KEY-----
-----END RSA PRIVATE KEY-----', // 商户私钥
        "result_format" =>  "array", // 返回类型 
        "log_path"  =>  "/log", //日志目录
        'return_url'    =>  '', // 跳转地址
        'notify_url'    =>  '', // 异步回调地址
        'public_key'    => '' // 支付宝公钥
];
$aop = Alipay::OpenPlatform($config);
```

其次 通过链式调用

例子展示的是 支付宝退款
```
$result = $aop -> trade -> F2f -> tradeOutTradeNoRefund($data['out_trade_no'],$data['out_request_no'],$data['refund_amount'],$data['refund_reason']);
```
