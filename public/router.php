<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$
if (is_file($_SERVER["DOCUMENT_ROOT"] . $_SERVER["SCRIPT_NAME"])) {
    return false;
} else {
    require __DIR__ . "/index.php";
}

/*
*  author kervi
*  上面语句的意思是：检查/route/route.php文件是否存在,不存在就进入index文件；
*  如果存在的话，就跳到route文件； 
*

* is_file 检测文件是否存在，与file_exits类似，但is_file()效率更高,
* 两者不同在于is_file会有缓存，file_exits没有

*/
