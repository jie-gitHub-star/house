<?php

namespace app\Admin\controller;

use think\Controller;
use think\Request;
use app\Admin\model\Room;

class house extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        $data = Room::select();
        
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
            $info = $file->validate(['ext'=>'jpg,png,gif,jpeg'])->move( './upload');
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
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
