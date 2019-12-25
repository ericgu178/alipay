<?php
namespace alipay\lib\AliYun\FaceRecognition;
use tool\Request;

/**
 * 阿里云人脸识别api接口
 *
 * @author EricGU178
 */
class Index
{
    /**
     * ak 密钥
     *
     * @var string
     */
    private $accessKeySecret = null;

    /**
     * ak id
     *
     * @var string
     */
    private $accessKeyId = null;

    /**
     * 阿里云人脸接口地址
     *
     * @var string
     */
    private $host = 'https://dtplus-cn-shanghai.data.aliyuncs.com';

    /**
     * 初始化
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            $this->accessKeySecret = $config['ak_secret'] ?? trigger_error('不存在ak密钥');
            $this->accessKeyId     = $config['ak_id'] ?? trigger_error('不存在ak');
        } else {
            throw new \Exception('配置文件不存在');
        }
    }

    /**
     * 人脸比对API调用说明
     *
     * 0: 通过url识别，参数image_url不为空；1: 通过图片content识别，参数content不为空
     * 图片传入绝对路径
     * 
     * @return void
     * @author EricGU178
     */
    public function verify($file1,$file2)
    {
        $file1  = '/Users/zkbr/Desktop/download.jpg';
        $file2  = '/Users/zkbr/Desktop/download.jpg';
        $url    = $this->host . '/face/verify';
        $data   = [
            'type'      => 1, 
            'content_1' => $this->picToBase64($file1),
            'content_2' => $this->picToBase64($file2),
        ];
        $header = $this->commonHeader($url, $data);
        $head   = [];
        foreach ($header as $k => $v) {
            $head[] = $k . ':' . $v;
        }
        $response = Request::requestPost($url, $data, $head);
        return $response;
    }


    public function detect($file)
    {
        $url    =   $this->host . '/face/detect';
        $data   =   [
            'type'  =>  1,
            'content'   =>  $this->picToBase64($file)
        ];
        $header = $this->commonHeader($url, $data);
        $head   = [];
        foreach ($header as $k => $v) {
            $head[] = $k . ':' . $v;
        }
        $response = Request::requestPost($url, $data, $head);
        return $response;
    }

    /**
     * 人脸属性识别API调用说明
     *
     * @param string $file
     * @return void
     * @author EricGU178
     */
    public function attribute($file)
    {
        $url    =   $this->host . '/face/attribute';
        $data   =   [
            'type'  =>  1,
            'content'   =>  $this->picToBase64($file)
        ];
        $header = $this->commonHeader($url, $data);
        $head   = [];
        foreach ($header as $k => $v) {
            $head[] = $k . ':' . $v;
        }
        $response = Request::requestPost($url, $data, $head);
        return $response;
    }

    /**
     * 构建 请求头部
     *
     * @param [type] $url
     * @param [type] $data
     * @return void
     * @author EricGU178
     */
    private function commonHeader($url, $data)
    {
        $gmdate  = gmdate("D, d M Y H:i:s \G\M\T");
        $options = [
            'http' => [
                'header' => [
                    'accept'       => "application/json",
                    'content-type' => "application/json",
                    'date'         => $gmdate,
                ],
                'method' => "POST", //可以是 GET, POST, DELETE, PUT
            ],
        ];
        $header  = [
            'accept'        => "application/json",
            'content-type'  => "application/json",
            'date'          => $gmdate,
            'authorization' => $this->authorization($url, $data, $options),
        ];
        return $header;
    }

    /**
     * 计算授权信息
     * 
     * @param string $url
     * @param array $data
     * @param array $options
     * @return string
     */
    private function authorization($url, $data, $options)
    {
        $http          = $options['http'];
        $header        = $http['header'];
        $bodymd5       = base64_encode(md5(json_encode($data), true));
        $parse         = parse_url($url);
        $path          = $parse['path'];
        $stringToSign  = $http['method'] . "\n" . $header['accept'] . "\n" . $bodymd5 . "\n" . $header['content-type'] . "\n" . $header['date'] . "\n" . $path;
        $signature     = base64_encode(hash_hmac("sha1", $stringToSign, $this->accessKeySecret, true)); // 签名
        $authorization = "Dataplus {$this->accessKeyId}:{$signature}";

        return $authorization;
    }

    /**
     * 本地文件转base64位格式
     *
     * @param string $file
     * @return binary
     * @author EricGU178
     */
    public function picToBase64($file)
    {
        $fp = fopen($file, "rb", 0) or die("Can't open file");
        $binary = fread($fp, filesize($file)); // 文件读取
        $base64 = base64_encode($binary); // 转码
//        $base64 = chunk_split(base64_encode(fread($fp, filesize($file))));//base64编码
        fclose($fp);
        return $base64;
    }
}