<?php
namespace alipay\lib\OpenPlatform\MemberCard;
use alipay\lib\Common\Aop;
/**
 * 会员卡
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
     * 创建会员模板
     * biz_content 数据
     */
    public function createCard($data)
    {
        $this->aop->method = "alipay.marketing.card.template.create";
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data); //返回结果
        return $result;
    }

    /**
     * 会员卡模板修改
     *
     * @param string $template_id
     * @param array $data
     * @return void
     * @author EricGU178
     */
    public function updateCard($template_id,$data)
    {
        $data['template_id']    =   $template_id;
        $this->aop->method = "alipay.marketing.card.template.modify";
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data); //返回结果
        return $result;
    }

    /**
     * 查询会员卡
     *
     * @param  template_id 模版id
     * @return void
     * @author EricGU178
     */
    public function getCard($template_id)
    {
        $this->aop->method = "alipay.marketing.card.template.query";
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute(["template_id"=>$template_id]);
        return $result;
    }
    /**
     * 获取会员卡领卡投放链接
     * 会员卡开卡业务，开发者通过该接口获取用户开卡链接，用于会员卡投放。
     * @param  array $data
     * @return void
     * @author EricGU178
     */
    public function receiveCardUrl($data)
    {
        $this->aop->method = "alipay.marketing.card.activateurl.apply";
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data);
        return $result;
    }

    /**
     * 会员卡开卡表单模板配置
     * 会员卡开卡、用户授权确认
     * @param string $template_id 模板id
     * @param array $data 
     * @return void
     * @author EricGU178
     */
    public function setActivationForm($template_id,$data)
    {
        if(!isset($template_id)){
            trigger_error("卡券模板id不存在");
        }
        $data["template_id"]    =   $template_id;
        $this->aop->method = "alipay.marketing.card.formtemplate.set";
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data);
        return $result;
    }

    /**
     * 查询用户提交的会员卡表单信息
     * 会员卡开卡场景下，用户确认领卡后，跳转到商户开卡处理页面，商户通过该接口查询用户表单信息。
     * @return request_id 请求id 唯一
     * @return template_id 模版id
     * @return auth_token 授权
     * @return void
     * @author EricGU178
     */
    public function queryActivateform($template_id,$request_id,$auth_token)
    {
        $data = [
            'biz_type'      =>  "MEMBER_CARD",
            'template_id'   =>  $template_id,
            'request_id'    =>  $request_id
        ];
        $this->aop->auth_token = $auth_token;
        $this->aop->method = "alipay.marketing.card.activateform.query";
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data);
        return $result;
    }

    /**
     * 会员卡开卡
     *
     * 商户通过API接口，进行会员卡开卡。
     * @return auth_token 令牌
     * @return data 数据
     * @return void
     * @author EricGU178
     */
    public function OpenCard($auth_token,$data)
    {
        $this->aop->auth_token = $auth_token;
        $this->aop->method = "alipay.marketing.card.open";
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data);
        return $result;
    }

    /**
     * 会员卡查询
     * 
     * 根据卡号或者持卡人信息查询会员卡信息
     * @param string $target_card_no
     * @param string $target_card_no_type
     * @return void
     * @author EricGU178
     */
    public function queryCard($target_card_no,$target_card_no_type="BIZ_CARD")
    {
        $this->aop->method = "alipay.marketing.card.query";
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $data = [
            "target_card_no"    =>  $target_card_no,
            "target_card_no_type"   =>  $target_card_no_type,
        ];
        $result = $this->aop->execute($data);
        return $result;
    }
    /**
     * 上传图片视频
     *
     * @return void
     * @author EricGU178
     */
    public function upload($filepath)
    {
        $this->aop->method = "alipay.offline.material.image.upload";
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $file = pathinfo($filepath);
        $data   =   [
            "image_name"    =>  $file['filename'],
            "image_type"    =>  $file['extension'],
            "image_content" =>  "@{$filepath}",
        ];
        $result = $this->aop->execute($data,false);
        return $result;
    }
}