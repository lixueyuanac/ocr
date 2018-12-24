<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14/014
 * Time: 10:01
 */

namespace Xueyuan\Ocr;

use GuzzleHttp\Client;
use Xueyuan\Ocr\Exception\Exception;
use Xueyuan\Ocr\Exception\HttpException;
use Xueyuan\Ocr\Exception\InvalidArgumentException;

class Ocr
{
    protected $url = 'http://recognition.image.myqcloud.com/ocr/'; // 基础网址
    protected $card_type = [0, 1]; // 0 表示正面 1 表示反面
    protected $driving_type = [0, 1, 2]; // 0 行驶证 1 驾驶证 2 驾驶证副页
    protected $header_content_type = ['application/json', 'multipart/form-data'];
    protected $config = array();

    public function __construct($module = 'idcard', array $config)
    {
        $this->appid = $config['appid'];
        $this->bucket = $config['bucket'];
        $this->secret_id = $config['secret_id'];
        $this->secret_key = $config['secret_key'];
        $this->config = $config;
        $this->url = $this->url . $module;
    }

    protected function httpRequest()
    {
        return new Client();
    }

    /**
     * 识别身份证正面反面
     * @param int $card_type 0 正面 1 背面
     * @param array $url_list 图片链接字符串数组
     */
    public function recognition($card_type = 0, $content_type = 'application/json', $url_list = array('http://img.wenzhangba.com/userup/883/1P4020F057-35O-0.jpg'))
    {
        if (!in_array($card_type, $this->card_type)) {
            throw new InvalidArgumentException('类型错误，card_type 不能为' . $card_type);
        }
        if (!in_array($content_type, $this->header_content_type)) {
            throw new InvalidArgumentException('类型错误，content_type 不能为' . $content_type);
        }
        if ($content_type === 'application/json') {
            $params = $this->json_param($card_type, implode(',', $url_list));

        } else {
            $params = $this->form_data_param($card_type, $url_list);
        }
        return $this->postData($params);
    }

    /**
     *  识别驾驶证
     * @param string $content_type
     * @param int $type
     * @param string $image
     */
    public function recognition_url($type = 0, $content_type = 'application/json', $url = '')
    {
        if (!in_array($content_type, $this->header_content_type)) {
            throw new InvalidArgumentException('参数不正确' . $content_type);
        }

        if (!in_array($type, $this->driving_type)) {
            throw new InvalidArgumentException('参数不正确' . $type);
        }
        if ($content_type === 'application/json') {
            $param = $this->json_param_url($type, $url);
        } else {
            $param = $this->form_data_param_url($type, $url);
        }

        return $this->postData($param);
    }

    public function postData($params)
    {
        $response = self::httpRequest()->request('POST', $this->url, $params);
        try {
            $data = $response->getBody()->getContents();
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage());
        }
        return $data;
    }

    /**
     * 获取 【传值方式为 application/json】 的头部
     */
    protected function json_param($card_type, $url_list)
    {
        $query_data = [
            'headers' => [
                'host' => 'recognition.image.myqcloud.com',
                'content-type' => 'application/json',
                'authorization' => $this->sign(),
            ],
            'query' => [
                'appid' => $this->appid,
                'card_type' => $card_type,
                'url_list' => $url_list,
            ],
        ];
        return $query_data;
    }

    /**
     * 拼写驾驶证参数2类
     * @param $type
     * @param $url
     * @return array
     */
    protected function json_param_url($type, $url)
    {
        $query_data = [
            'headers' => [
                'authorization' => $this->sign(),
                'content-type' => 'application/json',
            ],
            'json' => [
                "appid" => $this->appid,
                "bucket" => "",
                "type" => $type,
                "url" => $url,
            ]
        ];
        return $query_data;
    }

    /**
     * 获取 【传值方式为multipart/form-data】 的头部
     */
    protected function form_data_param($card_type, $images)
    {
        // 如果是数组[上传多个图片]
        $img_arr = [];
        if (is_array($images)) {
            $i = 0;
            foreach ($images as $item) {
                $img_arr[$i + 3] = [
                    'name' => 'image[' . $i . ']',
                    'contents' => $this->get_base64($item),
                ];
                $i++;
            }
        }
        $arr_base = [
            [
                'name' => 'appid',
                'contents' => $this->config['appid'],
            ],
            [
                'name' => 'card_type',
                'contents' => $card_type
            ]
        ];
        $query_data = [
            'headers' => [
                'Authorization' => $this->sign(),
            ],
            'multipart' => array_merge($arr_base, $img_arr),
        ];
        return $query_data;
    }

    /**
     * 获取 【传值方式为multipart/form-data】 的头部
     * @param $card_type
     * @param $images
     * @return array
     */
    protected function form_data_param_url($card_type, $images)
    {
        // 如果是数组[上传多个图片]

        $query_data = [
            'headers' => [
                'Authorization' => $this->sign(),
            ],
            'multipart' => [
                [
                    'name' => 'appid',
                    'contents' => $this->config['appid'],
                ],
                [
                    'name' => 'card_type',
                    'contents' => $card_type
                ],
                [
                    'name' => 'image',
                    'contents' => $this->get_base64($images)
                ]
            ]
        ];
        return $query_data;
    }

    public function get_base64($base64_image)
    {
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)) {
            return base64_decode(str_replace($result[1], '', $base64_image));
        }
        return false;
    }

    public function sign()
    {
        $appid = $this->config['appid'];
        $bucket = $this->config['bucket'];
        $secret_id = $this->config['secret_id'];
        $secret_key = $this->config['secret_key'];
        $expired = time() + 2592000;
        $onceExpired = 0;
        $current = time();
        $rdm = rand();
        $fileid = "tencentyunSignTest";

        $srcStr = 'a=' . $appid . '&b=' . $bucket . '&k=' . $secret_id . '&e=' . $expired . '&t=' . $current . '&r=' . $rdm . '&f=';

        $signStr = base64_encode(hash_hmac('SHA1', $srcStr, $secret_key, true) . $srcStr);

        return $signStr;
    }

    public function recognition_driving()
    {
        $result = $this->httpRequest()->request('POST', 'http://recognition.image.myqcloud.com/ocr/drivinglicence', [
            'headers' => [
                'authorization' => 'HDnqw7uIVNJQCvrkv6rzMyoh0CthPTEyNTY5NTMwMzEmYj0maz1BS0lEalBzQk9sUTg3eGpQTk1qemNDaTljRWs4c1ZtM3RaSnYmZT0xNTQ4MDUxMzYxJnQ9MTU0NTQ1OTM2MSZyPTQxMTY0ODA4MCZmPQ==',
                'content-type' => 'application/json',
            ],
            'json' => [
                "appid" => "1256953031",
                "bucket" => "",
                "type" => 0,
                "url" => "https://gss0.baidu.com/-fo3dSag_xI4khGko9WTAnF6hhy/zhidao/pic/item/d1160924ab18972bcadd8e7fe1cd7b899f510a9b.jpg",
            ]
        ]);
        $data = $result->getBody()->getContents();
        var_dump($data);
    }
}