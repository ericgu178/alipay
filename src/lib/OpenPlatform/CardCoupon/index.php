<?php
namespace alipay\lib\OpenPlatform\CardCoupon;
use alipay\lib\Common\Aop;
/**
 * 卡券类api
 *
 * @author EricGU178
 */
class Index
{
    private $aop; 

    public function __construct($config)
    {
        $this->aop = new Aop($config);
    }

    /**
     * 卡券模板创建接口
     * 
     * 创建卡券的模板，卡券的样式、内容信息通过该接口提交到支付宝，
     * 支付宝会生成模板ID反馈给开发者，用于后续的更新和发布。
     *
     * @param string $unique_id 商户用于控制模版的唯一性。（可以使用时间戳保证唯一性）
     * @param string $tpl_content 模板内容信息，遵循JSON规范
     * @return void
     */
    public function couponTemplateCreate(string $unique_id,array $tpl_content) 
    {
        $data   =   [
            'unique_id'     =>  $unique_id,
            'tpl_content'   =>  json_encode($tpl_content,256)
        ];
        $this->aop->method = 'alipay.pass.template.add';
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data);
        return $result;
    }


    /**
     * 卡券模板更新接口
     * 
     * 对于已经创建的模板，如果需要修改模板内容，
     * 可通过该接口修改，适用于修改模板内容；
     * 对于已经发布的模板，如果需要修改内容并同步到用户端，
     * 则应使用更新卡券接口。
     *
     * @param string $tpl_id 商户用于控制模版的唯一性。（可以使用时间戳保证唯一性）
     * @param string $tpl_content 模板内容信息，遵循JSON规范
     * @return void
     */
    public function couponTemplateUpdate(string $tpl_id,array $tpl_content) 
    {
        $data   =   [
            'tpl_id'        =>  $tpl_id,
            'tpl_content'   =>  json_encode($tpl_content,256)
        ];
        $this->aop->method = 'alipay.pass.template.update';
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data);
        return $result;
    }


    /**
     * 参数跳转至领券前置页
     *
     * @param string $userId
     * @param string $tpl_id
     * @param string $templateParams
     * @param string $extInfo
     * @param string $biz_type
     * @return void
     */
    public function getCouponPage(string $userId , string $tpl_id , string $templateParams = '' , string $extInfo = '' ,string $biz_type = 'svPage') 
    {
        $data   =   [
            'bizType'     =>  $biz_type,
            'userId'      =>  $userId,
            'templateId'  =>  $tpl_id,
        ];

        if (!empty($templateParams)) {
            $data['templateParams'] = \urlencode($templateParams);
        }

        if (!empty($extInfo)) {
            // extInfo 参数如果包含中文字符或者其他 URL 协议的特殊字符等，
            // 由于服务端处理逻辑为直接拼接，因此拼接成 url 参数的时候 extInfo 建议进行 2 次 
            $data['extInfo'] = \urlencode($extInfo);
        }
        $coupon_views = '/www/voucher.html?__webview_options__=abv%3dNO%26ttb%3dauto&';
        foreach ($data as $key => $value) {
            $coupon_views  .= $key . '=' . $value . '&';
        }
        $coupon_views  = rtrim($coupon_views,'&');
        $coupon_views  = \urlencode($coupon_views);
        return 'alipays://platformapi/startapp?appId=68687143&url=' . $coupon_views;
    }


    /**
     * 卡券实例发放接口
     *
     * 卡券模板生成后，如需将卡券发布给对应的用户，则调用此接口。
     * 
     * @param string tpl_id 卡券模板id
     * @param string tpl_params 模版动态参数信息：对应模板中$变量名$的动态参数，见模板创建接口返回值中的tpl_params字段 json
     * @param string recognition_info 支付宝用户识别信息：uid发券组件,
     * @param string recognition_type  Alipass添加对象识别类型：1–订单信息,2-为基于用户信息识别
     * @return void
     */
    public function couponInstanceIssue(string $tpl_id , array $tpl_params , array $recognition_info , string $recognition_type) 
    {
        $data   =   [
            'tpl_id'            =>  $tpl_id,
            'tpl_params'        =>  json_encode($tpl_params,256),
            'recognition_type'  =>  $recognition_type,
            'recognition_info'  =>  json_encode($recognition_info,256),
        ];
        $this->aop->method = 'alipay.pass.instance.add';
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data);
        return $result;
    }

    /**
     * 卡券实例更新接口
     * 
     * 对于已经发布的卡券，如需更新内容，可通过此接口更新，主要用于更新卡券的使用状态。
     *
     * @param string $serial_number 商户指定卡券唯一值
     * @param string $channel_id 代理商代替商户发放卡券后，再代替商户更新卡券时，此值为商户的pid/appid
     * @return void
     */
    public function couponInstanceUpdate(
        string $serial_number,
        string $channel_id,
        string $status = '',
        array  $tpl_params = [],
        string $verify_code = '',
        string $verify_type = ''
    )
    {
        $data = [
            'serial_number' =>  $serial_number,
            'channel_id'    =>  $channel_id,
        ];
        
        if (count($tpl_params) != 0) {
            $data['tpl_params'] = json_encode($tpl_params,256);
        }

        if ($status === 'USED') {
            if (empty($verify_code) || empty($verify_type)) {
                \trigger_error('当状态变更为USED时,请传verify_code和verify_type');
            }
            $data['status'] = $status;
            $data['verify_code'] = $verify_code;
            $data['verify_type'] = $verify_type;
        }

        if ($status === 'CLOSED') {
            $data['status'] = $status;
        }

        $this->aop->method = 'alipay.pass.instance.update';
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data);
        return $result;
    }


    /**
     * 集点查询
     *
     * 查询用户集点
     * 
     * @param string $user_id 用户ID
     * @param string $activity_account 活动积分帐户
     * @return void
     * @author EricGU178
     */
    public function couponPointQuery(string $user_id , string $activity_account)
    {
        $data = [
            'user_id'   =>  $user_id,
            'activity_account'  =>  $activity_account
        ];
        $this->aop->method = 'koubei.marketing.tool.points.query';
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data);
        return $result;
    }
}