<?php
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Think\Controller;
use OSS\OssClient;
use OSS\Core\OssException;

class FatherController extends Controller
{
    protected $time = 3600; //session生存时间
    //判断是否登录
    public function __construct()
    {
    }
    protected function admin_login($adminid){
        $ins['login_ip'] = ip2long($_SERVER['HTTP_X_REAL_IP']);
        $ins['admin_id'] = $adminid;
        $ins['login_time'] = time();
        $model = M('admin_login');
        $res = $model->data($ins)->add();
        if (!$res){
            die();
        }
    }
    protected function consloe_time($adminid,$console_id,$action_name){
        $ins['admin_id'] = $adminid;
        $ins['console_id'] = $console_id;
        $ins['action_name'] = $action_name;
        $ins['createtime'] = time();
        $ins['action_ip'] = ip2long($_SERVER['HTTP_X_REAL_IP']);
        $model = M('adminlog');
        $res = $model->data($ins)->add();
        if (!$res){
            die();
        }
    }

    public function uploadOss($file){
        require_once('vendor/autoload.php');
        $accessKeyId = "LTAIysus6HEVSHMJ";
        $accessKeySecret = "0R9uNt346k4tLFe3HPPqULxVC7qeqd";
        $endpoint = "http://oss-cn-qingdao.aliyuncs.com";
        $bucket = "yanku";
        try {
            $ossClient = new \OSS\OssClient($accessKeyId, $accessKeySecret, $endpoint);
        } catch (OssException $e) {
            print $e->getMessage();
        }
        $object = $file;
        $filePath = '/upload/' . $file;
        try{
            $ossClient->uploadFile($bucket, $object, $filePath);
//            unlink($filePath);
            return $object;
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
    }
}
?>