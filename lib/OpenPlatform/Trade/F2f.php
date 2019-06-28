<?php
/*
 * @User: EricGU178
 * @Date: 2019-06-19 16:27:12
 * @LastEditors: EricGU178
 * @LastEditTime: 2019-06-27 11:51:33
 */

namespace alipay\lib\OpenPlatform\Trade;
use alipay\lib\Common\Aop;

/**
 * 当面付
 *
 * @author EricGU178
 */
class F2f extends Index
{

    private $aop;

    public function __construct($config)
    {
        $this->aop = new Aop($config);
        $this->aop->setReturnUrl($config['return_url']);
        $this->aop->setNotifyUrl($config['notify_url']);
    }

    /**
     * 统一收单线下交易预创建
     * 
     * 收银员通过收银台或商户后台调用支付宝接口，生成二维码后，展示给用户，由用户扫描二维码完成订单支付。
     * 
     * @return void
     * @author EricGU178
     */
    public function tradePreCreate($data)
    {
        $this->aop->method = 'alipay.trade.precreate';
        $this->aop->timestamp = date("Y-m-d H:i:s",time());
        $result = $this->aop->execute($data); //返回结果
        return $result;
    }

    /**
     * 统一收单线下交易查询
     * 
     * 该接口提供所有支付宝支付订单的查询，
     * 商户可以通过该接口主动查询订单状态，完成下一步的业务逻辑。 
     * 需要调用查询接口的情况： 
     *      当商户后台、网络、服务器等出现异常，商户系统最终未接收到支付通知； 
     *      调用支付接口后，返回系统错误或未知交易状态情况； 
     *      调用alipay.trade.pay，返回INPROCESS的状态； 
     *      调用alipay.trade.cancel之前，需确认支付状态；
     * 
     * @param $out_trade_no 商户订单号
     * @param $trade_no 支付宝订单号
     * @param $org_pid 银行间联模式下有用，其它场景请不要使用
     * @return void
     * @author EricGU178
     */
    public function tradeQuery($out_trade_no='' , $trade_no='' , $org_pid = '')
    {
        if (empty($out_trade_no) && empty($trade_no)) {
            \trigger_error('商户订单号,和支付宝交易号不能同时为空');
        }
        // 商户订单号
        if (!empty($out_trade_no)) {
            $data['out_trade_no'] = $out_trade_no;
        }
        // 支付宝交易号
        if (!empty($out_trade_no)) {
            $data['trade_no'] = $trade_no;
        }
        // 银行间联模式下有用，其它场景请不要使用
        if (!empty($org_pid)) {
            $data['org_pid'] = $org_pid;
        }
        $this->aop->method = 'alipay.trade.query';
        $this->aop->timestamp = date('Y-m-d H:i:s',time());
        $result = $this->aop->execute($data); // 返回结果
        return $result;
    }

    /**
     * 统一收单交易撤销接口
     *
     * 支付交易返回失败或支付系统超时，调用该接口撤销交易。
     *              如果此订单用户支付失败，支付宝系统会将此订单关闭；
     *              如果用户支付成功，支付宝系统会将此订单资金退还给用户。 
       注意：只有发生支付系统超时或者支付结果未知时可调用撤销，
       其他正常支付的单如需实现相同功能请调用申请退款API。
     * 提交支付交易后调用【查询订单API】，
     * 没有明确的支付结果再调用【撤销订单API】。
     * @param $out_trade_no 商户订单号
     * @param $trade_no 支付宝订单号
     * @return void
     * @author EricGU178
     */
    public function tradeCancel($out_trade_no='',$trade_no='')
    {
        if (empty($out_trade_no) && empty($trade_no)) {
            \trigger_error('商户订单号,和支付宝交易号不能同时为空');
        }
        // 商户订单号
        if (!empty($out_trade_no)) {
            $data['out_trade_no'] = $out_trade_no;
        }
        // 支付宝交易号
        if (!empty($trade_no)) {
            $data['trade_no'] = $trade_no;
        }
        $this->aop->method = 'alipay.trade.cancel';
        $this->aop->timestamp = date('Y-m-d H:i:s',time());
        $result = $this->aop->execute($data); // 返回结果
        return $result;
    }

    /**
     * 统一收单交易退款接口(商户订单号退款)
     *
     * 当交易发生之后一段时间内，
     * 由于买家或者卖家的原因需要退款时，
     * 卖家可以通过退款接口将支付款退还给买家，
     * 支付宝将在收到退款请求并且验证成功之后，
     * 按照退款规则将支付款按原路退到买家帐号上。 
     * 交易超过约定时间（签约时设置的可退款时间）的订单无法进行退款 
     * 支付宝退款支持单笔交易分多次退款，
     * 多次退款需要提交原支付订单的商户订单号和设置不同的退款单号。
     * 一笔退款失败后重新提交，
     * 要采用原来的退款单号。
     * 总退款金额不能超过用户实际支付金额
     * @param $out_trade_no 商户订单号
     * @param $refund_amount 退款金额
     * @param $out_request_no 退款单号
     * @param $refund_reason 退款原因
     * @param $refund_currency 退款币种信息
     * @return void
     * @author EricGU178
     */
    public function tradeOutTradeNoRefund($out_trade_no,$out_request_no,$refund_amount,$refund_reason='正常退款',$refund_currency='CNY')
    {
        if (empty($out_trade_no) ||
            empty($out_request_no) ||
            empty($refund_amount)
        ) {
            throw new \Exception('入参不能传空值');
        }
        
        $data   =   [
            'out_trade_no'  =>  $out_trade_no,
            'out_request_no'    =>  $out_request_no,
            'refund_amount' =>  $refund_amount,
            'refund_currency'   =>  $refund_currency,
            'refund_reason' =>  $refund_reason
        ];
        $this->aop->method = 'alipay.trade.refund';
        $this->aop->timestamp = date('Y-m-d H:i:s',time());
        $result = $this->aop->execute($data); // 返回结果
        return $result;
    }

    /**
     * 统一收单交易退款接口(支付宝单号退款)
     *
     * 当交易发生之后一段时间内，
     * 由于买家或者卖家的原因需要退款时，
     * 卖家可以通过退款接口将支付款退还给买家，
     * 支付宝将在收到退款请求并且验证成功之后，
     * 按照退款规则将支付款按原路退到买家帐号上。 
     * 交易超过约定时间（签约时设置的可退款时间）的订单无法进行退款 
     * 支付宝退款支持单笔交易分多次退款，
     * 多次退款需要提交原支付订单的商户订单号和设置不同的退款单号。
     * 一笔退款失败后重新提交，
     * 要采用原来的退款单号。
     * 总退款金额不能超过用户实际支付金额
     * @param $trade_no 支付宝订单号
     * @param $refund_amount 退款金额
     * @param $out_request_no 退款单号
     * @param $refund_reason 退款原因
     * @param $refund_currency 退款币种信息
     * @return void
     * @author EricGU178
     */
    public function tradeTradeNoRefund($trade_no,$out_request_no,$refund_amount,$refund_reason='正常退款',$refund_currency='CNY')
    {
        if (empty($trade_no) ||
            empty($out_request_no) ||
            empty($refund_amount)
        ) {
            \trigger_error('入参不能传空值');
        }
        
        $data   =   [
            'trade_no'  =>  $trade_no,
            'out_request_no'    =>  $out_request_no,
            'refund_amount' =>  $refund_amount,
            'refund_currency'   =>  $refund_currency,
            'refund_reason' =>  $refund_reason
        ];
        $this->aop->method = 'alipay.trade.refund';
        $this->aop->timestamp = date('Y-m-d H:i:s',time());
        $result = $this->aop->execute($data); // 返回结果
        return $result;
    }

    /**
     * 统一收单交易退款查询
     * 
     * 商户可使用该接口查询自已通过alipay.trade.refund或alipay.trade.refund.apply提交的退款请求是否执行成功。
     * 该接口的返回码10000，仅代表本次查询操作成功，不代表退款成功。
       如果该接口返回了查询数据，且refund_status为空或为REFUND_SUCCESS，则代表退款成功，
       如果没有查询到则代表未退款成功，可以调用退款接口进行重试。
       重试时请务必保证退款请求号一致。
     *
     * @return void
     * @author EricGU178
     */
    public function tradeFastpayRefundQuery($out_request_no,$out_trade_no='',$trade_no='',$org_pid='')
    {
        $data['out_request_no'] =   $out_request_no;
        if (empty($out_trade_no) && empty($trade_no)) {
            \trigger_error('商户订单号,和支付宝交易号不能同时为空');
        }
        // 商户订单号
        if (!empty($out_trade_no)) {
            $data['out_trade_no'] = $out_trade_no;
        }
        // 支付宝交易号
        if (!empty($trade_no)) {
            $data['trade_no'] = $trade_no;
        }
        // 银行间联模式下有用，其它场景请不要使用
        if (!empty($org_pid)) {
            $data['org_pid'] = $org_pid;
        }
        $this->aop->method = 'alipay.trade.fastpay.refund.query';
        $this->aop->timestamp = date('Y-m-d H:i:s',time());
        $result = $this->aop->execute($data); // 返回结果
        return $result;
    }

    /**
     * 统一收单交易关闭接口
     *
       用于交易创建后，
       用户在一定时间内未进行支付，
       可调用该接口直接将未付款的交易进行关闭。
       
     * @return void
     * @author EricGU178
     */
    public function tradeClose($out_trade_no = '',$trade_no = '')
    {
        if (empty($out_trade_no) && empty($trade_no)) {
            \trigger_error('商户订单号,和支付宝交易号不能同时为空');
        }
        // 商户订单号
        if (!empty($out_trade_no)) {
            $data['out_trade_no'] = $out_trade_no;
        }
        // 支付宝交易号
        if (!empty($trade_no)) {
            $data['trade_no'] = $trade_no;
        }
        $this->aop->method = 'alipay.trade.close';
        $this->aop->timestamp = date('Y-m-d H:i:s',time());
        $result = $this->aop->execute($data); // 返回结果
        return $result;
    }
}