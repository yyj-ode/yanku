<?php
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Back2\Model\VarietyModel;
use Think\Controller;

class VarietyController extends Controller
{
    public function videoImgUpload(){
        $fatherController = new FatherController();
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3 * 1024 * 1024 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     '/upload/'; // 设置附件上传根目录
        $upload->savePath  =     'SortVideo/';
        $info = $upload->upload();
//
        if(!$info) {// 上传错误提示错误信息
            $arr = [
                "code"=> 1,
                "msg"=>"图片上传失败!"
            ];
        }
        else{// 上传成功 获取上传文件信息
                $src = '';
                foreach ($info as $k=>$v){
                    $up = $v['savepath'].$v['savename'];
                    $src = $fatherController->uploadOss($up);
                }
            $arr = [
                "code"=> 0,
                "msg"=>"图片上传成功!",
                "data"=> [
                    "src"=> $src
                ]
            ];
        }
        echo json_encode($arr);
    }

    public function episodeUpload(){
        $fatherController = new FatherController();
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     100 * 1024 * 1024 ;// 设置附件上传大小
        $upload->exts      =     array('mp4', 'mov','mpg');// 设置附件上传类型
        $upload->rootPath  =     '/upload/'; // 设置附件上传根目录
        $upload->savePath  =     'SortVideo/';
        $info = $upload->upload();

        if(!$info) {// 上传错误提示错误信息
            $arr = [
                "code"=> 1,
                "msg"=>"视频上传失败!"
            ];
        }else{// 上传成功 获取上传文件信息
            $src = '';
            foreach ($info as $k=>$v){
                $up = $v['savepath'].$v['savename'];
                $src = $fatherController->uploadOss($up);
            }
            $arr = [
                "code"=> 0,
                "msg"=>"视频上传成功!",
                "data"=> [
                    "src"=> $src
                ]
            ];
        }
        echo json_encode($arr);
    }


    public function getActorList(){
        $data = M('yanyiaudit y')
            ->field("DISTINCT(y.user_id),u.nickname")
            ->join('yk_user u ON y.user_id = u.user_id','LEFT')
            ->where("y.status=2 AND u.nickname is not NULL")
            ->select();
//        dumpp($data);
//        foreach($data as $k => $v){
//            $data[$k]['info'] = $v['user_id'].'-'.$v['nickname'];
//        }
        echo json_encode($data);
    }

    public function varietyAdd(){
        $episode_src = I('episode_src')?:[];
        $uids = I('uids')?:[];
        $data1['title'] = I('title')?:'';
        $data1['introduce'] = I('introduce')?:'';
        $data1['sort_introduce'] = I('sort_introduce')?:'';
        $data1['video_img'] = I('video_img')?:'';
        $data1['view'] = 0;
        $data1['push'] = 1;
        //验证字段
        VarietyModel::videoVerify($data1,$uids,$episode_src);
        //添加综艺
        M()->startTrans();
        $trans[] = $vid = M('video')->data($data1)->add();
        //添加参与的艺人
        foreach($uids as $k => $v){
            $data2['vid'] = $vid;
            $data2['uid'] = $v;
            $trans[] = M('video_join')->data($data2)->add();
        }
        //添加集数
        $i = 1;
        foreach($episode_src as $k1 => $v1){
            $data3['vid'] = $vid;
            $data3['episode_src'] = $v1;
            $data3['episode_num'] = $i;
            $i++;
            $trans[] = M('video_episode')->data($data3)->add();
        }
        $if_all_success = 0;
        foreach($trans as $k2 => $v2){
            if(!$v2){
                $if_all_success++;
            }
        }
        if($if_all_success != 0){   //事务没通过
            M()->rollback();
            echo '提交失败';
        }else{  //事务通过
            M()->commit();
            echo '提交成功';
        }
    }

    public function varietyList(){
        //先给默认值
        $page = I('page')?:1;
        $rows = I('rows')?:12;
        $search_key = I('search_key')?:'';
        $data = VarietyModel::getVarietyListData($page, $rows,$search_key);
        //        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }
}
?>