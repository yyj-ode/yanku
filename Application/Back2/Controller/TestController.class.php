<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/3 0003
 * Time: 13:22
 */
namespace Back2\Controller;
use OSS\OssClient;
use OSS\Core\OssException;

use Think\Controller;

class TestController extends Controller
{
    public function transferOss($file){
        $accessKeyId = "LTAIysus6HEVSHMJ";
        $accessKeySecret = "0R9uNt346k4tLFe3HPPqULxVC7qeqd";
        $endpoint = "http://oss-cn-qingdao.aliyuncs.com";
        $bucket = "yanku";
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        } catch (OssException $e) {
            print $e->getMessage();
        }
        $object = $file['path']."/".$file['filename'];

    }

    public function bianli(){
        $files = [];
        $initname = 'Upload';
        $dirname = '/usr/local/apache/htdocs/yanku/'.$initname;
        $handle1 = opendir($dirname);
        $i = 0;
        while($filename1 = readdir($handle1)){
            if($filename1 != '.' && $filename1 != '..' ){
                if(is_dir($dirname.'/'.$filename1)){
                    $handle2 = opendir($dirname.'/'.$filename1);
                    while($filename2 = readdir($handle2)){
                        if($filename2 != '.' && $filename2 != '..' ){
//                            echo $dirname.'/'.$filename1.'/'.$filename2;die;
                            if(is_dir($dirname.'/'.$filename1.'/'.$filename2)){
                                $handle3 = opendir($dirname.'/'.$filename1.'/'.$filename2);
                                while($filename3 = readdir($handle3)){
                                    if($filename3 != '.' && $filename3 != '..' ){
                                        $files[$i]['filepath'] = $dirname.'/'.$filename1.'/'.$filename2.'/'.$filename3;
//                                        $files[$i]['filename'] = $filename3;
//                                        $files[$i]['dirname'] = $filename1;
                                        $files[$i]['location'] = $filename1.'/'.$filename3;
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        echo '<pre>';
        var_dump($files);
    }
}