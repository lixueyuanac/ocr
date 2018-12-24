<h1 align="center"> ocr </h1>

<p align="center"> Tencent Ocr.</p>


## Installing

```shell
$ composer require xueyuan/ocr -vvv
```
## config
 1.在使用本扩展之前，你需要去 腾讯云 注册账号，然后创建应用，获取应用的 appid,secret_id,secret_key,bucket。
 2.仔细阅读腾讯ocr api https://cloud.tencent.com/document/api/866/17594 
 
## Usage

此扩展包依赖于 guzzlehttp/guzzle 
1. 普通安装
    直接初始化
    ```
    $factory = new Factory(["appid"=>'','secret_id'=>'','secret_key'=>'','bucket'=>'']);
    // 支持的类型有idcard businesscard drivinglicence plate bankcard bizlicense general handwriting
    $result = $factory->make('idcard'); 
    
    ```
2. laravel
    > 1. ServicePrivider 参考laravel容器
    ```
        $ocr = app(\Xueyuan\Ocr\Ocr::class)->make('idcard');
        return $ocr->recognition(0,'application/json',['http://i3.qhimg.com/t0148d78bd495777810.jpg']);
    ```
    > 2. Facade  参考laravel 门面
        ```
        use Xueyuan\Ocr\Facades\Ocr;
        $ocr = Ocr::make('idcard');
        return $ocr->recognition(0,'application/json',['http://i3.qhimg.com/t0148d78bd495777810.jpg']);
        
        ```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/xueyuan/ocr/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/xueyuan/ocr/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT
