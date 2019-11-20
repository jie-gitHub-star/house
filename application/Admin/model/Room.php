<?php
namespace app\Admin\model;

use think\Model;

class Room extends Model
{
	protected $pk = "r_id";
	protected $table = "wxp_roominfo";
	protected $connection = 'db_config';

	// 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }
}