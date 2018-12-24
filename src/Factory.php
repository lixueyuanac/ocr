<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/20/020
 * Time: 16:08
 */
namespace Xueyuan\Ocr;

class Factory {
    const ID_CARD         = 'idcard'; // 身份证号
    const BUSINESS_CARD   = 'businesscard'; // 名片
    const DRIVING_LICENCE = 'drivinglicence'; // 驾驶证
    const PLATE           = 'plate'; // 车牌号
    const BANK_CARD       = 'bankcard'; // 银行卡号
    const BIZ_LICENSE     = 'bizlicense'; // 营业执照
    const GENERAL         = 'general'; // 通用印刷体识别
    const HAND_WRITING    = 'handwriting'; // 手写体识别

    private $appid;
    private $secret_id;
    private $secret_key;
    private $bucket;

    public function __construct($config)
    {
       $this->appid      = $config['appid'];
       $this->secret_id  = $config['secret_id'];
       $this->secret_key = $config['secret_key'];
       $this->bucket     = $config['bucket'];
    }

    public function make($module=''){
        $config = [
            'appid'      => $this->appid,
            'secret_id'  => $this->secret_id,
            'secret_key' => $this->secret_key,
            'bucket'     => $this->bucket,
        ];
        switch ($module){
            case self::ID_CARD:
            case self::BUSINESS_CARD:
            case self::DRIVING_LICENCE:
            case self::PLATE:
            case self::BANK_CARD:
            case self::BIZ_LICENSE:
            case self::GENERAL:
            case self::HAND_WRITING:
                return new Ocr($module,$config);
                break;

        }
        return new Ocr($module,$config);
    }
}