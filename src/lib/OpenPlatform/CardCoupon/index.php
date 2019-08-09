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
    public function couponTemplateCreate(string $unique_id,string $tpl_content) 
    {
        $data   =   [
            'unique_id'     =>  $unique_id,
            'tpl_content'   =>  $tpl_content
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
    public function couponTemplateUpdate(string $tpl_id,string $tpl_content) 
    {
        $data   =   [
            'tpl_id'        =>  $tpl_id,
            'tpl_content'   =>  $tpl_content
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
     * @param string recognition_info 支付宝用户识别信息：uid发券组件,
     * @param string recognition_type  Alipass添加对象识别类型：1–订单信息
     * @return void
     */
    public function couponInstanceIssue(string $tpl_id , string $recognition_info , string $recognition_type = '1') 
    {
        $data   =   [
            'tpl_id'            =>  $tpl_id,
            'recognition_type'  =>  $recognition_type,
            'recognition_info'  =>  $recognition_info,
            // 'tpl_params'        =>  $tpl_content
        ];
        $this->aop->method = 'alipay.pass.instance.add';
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