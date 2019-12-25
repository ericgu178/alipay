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


# 阿里云

## 阿里云 人脸识别


官方服务API校验规范

```php
AccessKey相当于您访问阿里云产品的口令，拥有您完整的权限，请妥善保管、避免泄露，并定期更换您的AccessKey

api 校验规则

Authorization =  Dataplus AccessKeyId + ":" + Signature
Signature = Base64( HMAC-SHA1( AccessSecret, UTF-8-Encoding-Of(StringToSign) ) )
StringToSign =
//HTTP协议header
HTTP-Verb + "\n" +  //GET|POST|PUT...
Accept + "\n" +
Content-MD5 + "\n" + //Body的MD5值放在此处
Content-Type + "\n" +
Date + "\n" +
url


签名计算方法
API请求使用标准的Authorization头来签名自己的请求，请求格式如下所示：

Authorization: Dataplus AccessKeyId:Signature
```

使用
```php
use alipay\Alipay;
$aliyun = Alipay::AliYun(['ak_secret'=>123,'ak_id'=>123])->face_recognition;
$s = $aliyun->detect('/Users/zkbr/Desktop/download.jpg'); // 检测定位
$s = $aliyun->verify('/Users/zkbr/Desktop/download.jpg'); // 人脸识别
$s = $aliyun->attribute('/Users/zkbr/Desktop/download.jpg'); // 属性识别
var_dump($s);die;
``` 