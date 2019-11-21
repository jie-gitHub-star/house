<?php

namespace app\Admin\controller;

use think\Controller;
use think\Request;
use think\Db;
/* 引入 roominfo 数据表模型*/
use app\admin\model\Room;

class house extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        $data = Room::field('r_id,r_desc,sell_price,house_type,acreage,pics')->select();
        foreach ($data as $key => $value){
            if(!empty($value['pics'])){
                $data[$key]['pics'] = explode(',',$value['pics']);
            }
        }
        // exit('2');
        $this->assign('data',$data);
        return $this->fetch();

    }

    /**
     * 添加房源，表单页
     *
     * @return 
     */
    public function addhouse()
    {
        // var_dump(Room::get(1));
        return $this->fetch();
    }
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存添加房源信息
     *
     * @param  \think\Request  $request
     * @return \think\Response

     *  
     */
    public function save(Request $request)
    {
        $data = [];
        $params = $request->param(); //获取参数
        foreach ($params as $key => $value) {
            if(empty($value)){
                return $this->error('参数都不能为空');
            }else{
                //存入数组
                $data[$key] = $value;
            }
        }
             

        $files = $request->file();//获取文件
        // var_dump($files);
        $data['pics'] = '';
        $data['issue'] = date('Y-m-d H:i:s',time());//发布时间
        foreach($files['pics'] as $key => $file){           
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->rule('md5')->validate(['ext'=>'jpg,png,gif,jpeg'])->move( './upload');
            if($info){
                if(!$key==0){
                    $data['pics'] .= ',upload/'.$info->getSaveName(); 
                }else{

                    $data['pics'] .= 'upload/'.$info->getSaveName(); 
                }
            }else{
                // 上传失败获取错误信息
                return  $this->error($file->getError());
            }    
        }

        // 处理下数据，存入数据库
        $room = new Room();
        if($room->save($data)){
            return $this->success('添加完成');
        }else{
            return $this->error('添加失败，请仔细检查');
        }


    }

    /**
     * 显示指定的资源
     *['pics']
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
        $rinfo = Db::name('roominfo')->where('r_id',$id)->findOrEmpty();
        $rinfo['pics'] = explode(',',$rinfo['pics']);
        $this->assign('rinfo',$rinfo);
        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {   

        $params = $request->param(); //获取参数
        foreach ($params as $key => $value) {/*参数不能为空*/
            if(empty($value)){
                return $this->error('参数都不能为空');
            }else{
                //存入数组
                $data[$key] = $value;
            }
        }
        /*如果有图片*/
        $files = $request->file()['pics'];
        if($files){ 
            /*处理新上传的图片*/
            $data['pics'] = '';
            // $data['issue'] = date('Y-m-d H:i:s',time());//发布时间
            foreach($files as $key => $file){           
                // 移动到框架应用根目录/uploads/ 目录下
                $info = $file->rule('md5')->validate(['ext'=>'jpg,png,gif,jpeg'])->move( './upload');
                if($info){
                    if(!$key==0){
                        $data['pics'] .= ',upload/'.$info->getSaveName(); 
                    }else{

                        $data['pics'] .= 'upload/'.$info->getSaveName(); 
                    }
                }else{
                    // 上传失败获取错误信息
                    return  $this->error($file->getError());
                }    
            }

                   /*  处理完成，删除旧图片*/
            $errimg = [];
            $rinfo = Db::name('roominfo')->where('r_id',$id)->field('pics')->findOrEmpty();
            if($rinfo){
                $pics = explode(',',$rinfo['pics']);
                foreach ($pics as $key => $value) {
                    /* 如果有文件，删除，如果不存在，记录到日志 */
                    if(file_exists("./$value")){
                        unlink("./$value");
                    }else{
                        $errimg[] = $value;
                    }
                }
            }
            // var_dump($request->param());
            if($errimg){ //如果有删除失败的，记录到日志文件               
               $log = fopen('./upload/upimg.log','a');
               fwrite($log,json_encode($errimg));
               fclose($log);
            }


        }

        // 处理下数据，存入数据库
        $room = new Room();
        if($room->save($data,['r_id'=>$id])){
            return $this->success('更新完成','/house');
        }else{
            return $this->error('添加失败，请仔细检查或数据没有改变');
        }


    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $errimg = [];
        $rinfo = Db::name('roominfo')->where('r_id',$id)->field('pics')->findOrEmpty();
            if($rinfo){
                $pics = explode(',',$rinfo['pics']);
                foreach ($pics as $key => $value) {
                    /* 如果有文件，删除，如果不存在，记录到日志 */
                    if(file_exists("./$value")){
                        unlink("./$value");
                    }else{
                        $errimg[] = $value;
                    }
                }
            }
            if($errimg){
                $log = fopen('./upload/delimg.log','a');
                fwrite($log,json_encode($errimg));
                fclose($log);
            }
        $res = Room::destroy($id);
        if($res){
            $this->success('成功删除');
        }else{
            $this->error('成功失败,');

        }
    }


/*----- 处理上传的文件 
    *
    * @ files   图片对象 
    *  
    * return    根目录下图片路径
------*/
    public function upload($files){
        foreach($files['pics'] as $key => $file){           
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->rule('md5')->validate(['ext'=>'jpg,png,gif,jpeg'])->move( './upload');
            if($info){
                if(!$key==0){
                    $data['pics'] .= ',upload/'.$info->getSaveName(); 
                }else{

                    $data['pics'] .= 'upload/'.$info->getSaveName(); 
                }
            }else{
                // 上传失败获取错误信息
                return  $this->error($file->getError());
            }    
        }
        return $data['pics'];
    }
}
