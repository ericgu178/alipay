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
                    'serialNumber'  =>  '$serialNumber$'
                ],
                'merchant'  => [
                    'mname' => '四季风情',
                    'mtel'  => '26888888',
                    'minfo' =>  'http://life.taobao.com/',
                    'mcallbackUrl'  =>  'https://nhns.xinliu.org/gongju/index/callback'
                ],
                'platform'  => [
                    'channelID' =>  app_id,
                ],
            ],
        ];
```