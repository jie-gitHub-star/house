<?php
namespace app\Admin\model;

use think\Model;

class Admin extends Model
{
	protected $pk = "uid";
	protected $table = "wxp_admin";
	protected $connection = 'db_config';

	// 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }
}