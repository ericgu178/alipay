<!--
 * @User: EricGU178
 * @Date: 2019-08-09 14:58:47
 * @LastEditors: Please set LastEditors
 * @LastEditTime: 2019-08-13 09:29:18
 -->
# 卡券模板

创建 卡券 tpl_content 需要的内容
```php
$coupon = [
            'icon'      =>  '',
            'logo'      =>  'http://img.xianzhaiwang.cn/d/file/shehui/879c888f393798c81a40d7a1e698bcff.jpg',
            'strip'     =>  '',
            'content'   =>  [
                'evoucherInfo'  =>  [
                    "title"         =>  '四季风情优惠券',
                    'startDate'     =>  '2013-07-16 08:00:00',
                    'endDate'       =>  '2014-06-18 23:59:59',
                    'type'          =>  'coupon',
                    'product'       =>  'free',
                    'operation' => [
                        [
                            'altText'   =>  '45612346579465',
                            'format'    => 'qrcode',
                            'message'   => '45612346579465',
                            'messageEncoding'   => 'UTF-8'
                        ],
                    ],
                    'remindInfo'   =>  [
                        'offset'    =>  2
                    ],
                    "einfo" =>  [
                        "logoText"  =>  "四季风情优惠券",
                        "headFields"    =>  [
                            [
                                "key"   => "status",
                                "label" => "状态",
                                "value" => "可使用"
                            ]
                        ],
                        "primaryFields" =>  [
                            [
                                "key"   => "strip",
                                "label" => "",
                                "value" => "凭此全即可打8.5折",
                                "type"  => "image"
                            ],
                            [
                                "key"   => "title",
                                "label" => "",
                                "value" => "嘉定城中路200号.5折",
                                "type"  => "text"
                            ],
                        ],
                        "secondaryFields"   =>  [
                            [
                                "key"   =>  "validDate",
                                "label" =>  "有效期至：",
                                "value" =>  "2014-06-18 23:59:59"
                            ]
                        ],
                        "auxiliaryFields"   =>  null,
                        "backFields"    =>  [
                            [
                                "key"=>"description",
                                "label"=>"详情描述",
                                "value"=>"1.该优惠有效期：截止至2014年06月18日；\n2.凭此券可以享受以下优惠：\n享门市价8.5折优惠\n不与其他优惠同享。详询商家。"
                            ],
                            [
                                "key"=> "shops",
                                "label"=> "可用门店",
                                "value"=> ""
                            ],
                            [
                                "key"=> "disclaimer",
                                "label"=> "负责声明",
                                "value"=> "除特殊注明外，本优惠不能与其他优惠同时享受； 本优惠最终解释权归商家所有，如有疑问请与商家联系。 提示：为了使您得到更好的服务，请在进店时出示本券。"
                            ]
                        ]
                    ]
                ],
                'style' =>  [
                    'backgroundColor'   =>  'RGB(255,126,0)'
                ],
                'fileInfo'  =>  [
                    'canShare'      => false,
                    'formatVersion' =>  2,
                    'serialNumber'  =>  $serialNumber$
                ],
                'merchant'  => [
                    'mname' => '四季风情',
                    'mtel'  => '26888888',
                    'minfo' =>  'http://life.taobao.com/'
                    'mcallbackUrl'  =>  'callback'
                ],
                'platform'  => [
                    'channelID' =>  appid,
                ],
            ],
        ];
```

> 卡券实例发券

```php
"tpl_id": "2018071711565730423894319", 
"tpl_params": {
    "expireTime": "2019-04-30 23:59:59", 
    "activeTime": "2017-04-01 00:00:00", 
    "serialNumber": "abc_test_201807171156", 
    "url":"http://xxxxx"
}, 
"recognition_type": "2", 
"recognition_info": {
   "user_id": "2088302449179665", 
   "user_token": "d81ca68b6811678960f1c7bd44792b11"
}

解释

tpl_id：指定券模板 id

tpl_params：动态参数传值，模板中定义的占位动态参数，应全部通过此参数传入实际值

key-value map 格式，key 和 value 都必须为 String 类型

recognition_type：发券（用户信息识别）类型，“2”为基于用户信息识别 recognition_type 不同，recognition_info 的传参也不同

recognition_info：发券用户信息模型，当recognition_type = "2"时，配置传参如下
            user_id：指定本次发券的支付宝userId
            user_token：本次发券该用户确认领取时生成并传回给商家的发券token令牌值

注意：发券接口参数中传入的 user_token 与 tpl_id 及 user_id 都必须一一对应，否则会调用接口失败。
```