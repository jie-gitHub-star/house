<?php

namespace app\admin\model;

use think\Model;

class wxUsers extends Model
{
    protected $pk = "id";
	protected $table = "wxp_prousers";
	protected $connection = 'db_config';
}
