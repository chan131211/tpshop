<?php

namespace app\admin\model;

use think\Model;

class Goods extends Model
{
    //设置软删除
    use \traits\model\SoftDelete;
    //设置软删除相关的字段
    protected $deleteTime = 'delete_time';
}
