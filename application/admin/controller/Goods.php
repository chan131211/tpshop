<?php

namespace app\admin\controller;

use think\Request;

class Goods extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询
        $keyword = input('keyword');
        $where = [];
        if (!empty($keyword)) {
            $where['goods_name'] = ['like', "%{$keyword}%"];
        }
        $list = \app\admin\model\Goods::where($where)->order('id desc')->paginate(5,false, [
            'query' => ['keyword' => $keyword]
        ]);
        $this->assign('list', $list);
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //获取表单提交的数据
        $params = $request->param();
        //验证表单数据
        $rule = [
            'goods_name|商品名称' => 'require',
            'goods_price|商品价格' => 'require|float|egt:0',
            'goods_number|商品数量' => 'require|integer|egt:0'
        ];
        $msg = [
            'goods_price.float' => '商品价格必须是整数或者小数',
            'goods_price.egt:0' => '商品价格必须大于等于0',
            'goods_number.integer' => '商品数量必须是整数',
            'goods_number.egt:0' => '商品数量必须大于等于0',
        ];
        $validate = $this->validate($params,$rule,$msg);
        if ($validate !== true) {
            $this->error($validate);
        }

        //商品logo上传
        $params['goods_logo'] = $this->upload_logo();

        //过滤数据并添加数据库
        \app\admin\model\Goods::create($params, true);
        $this->success('添加成功','admin/goods/index');
    }

    /**
     * 文件上传
     */
    private function upload_logo()
    {
        $file = \request()->file('logo');
        if (empty($file)) {
            $this->error('没有上传商品logo');
        }
        $info = $file->validate(['size' => 10*1024*1024, 'ext' => 'jpg,png,gif,jpeg'])->move(ROOT_PATH.'public'.DS.'uploads');
        if ($info) {
            //返回图片路径
            $goods_logo = DS.'uploads'.DS.$info->getSaveName();
            return $goods_logo;
        }else {
            $error_msg = $file->getError();
            $this->error($error_msg);
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $goods = \app\admin\model\Goods::find($id);
        $this->assign('goods',$goods);
        return view();
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $goods = \app\admin\model\Goods::find($id);
        $this->assign('goods',$goods);
        return view();
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
        $params = $request->param();
        $rule = [
            'goods_name|商品名称' => 'require',
            'goods_price|商品价格' => 'require|float|egt:0',
            'goods_number|商品数量' => 'require|integer|egt:0'
        ];
        $msg = [
            'goods_price.float' => '商品价格必须是整数或者小数',
            'goods_price.egt:0' => '商品价格必须大于等于0',
            'goods_number.integer' => '商品数量必须是整数',
            'goods_number.egt:0' => '商品数量必须大于等于0',
        ];
        $validate = $this->validate($params, $rule, $msg);
        if ($validate !== true) {
            $this->error($validate);
        }

        //判断是否更新商品logo
        $file = \request()->file('logo');
        if (!empty($file)) {
            $params['goods_logo'] = $this->upload_logo();
            //查询原有的图片路径
            $goods = \app\admin\model\Goods::find($id);
            $old_goods_logo = $goods['goods_logo'];
        }

        \app\admin\model\Goods::update($params,['id' => $id], true);
        //删除旧商品logo
        if (isset($old_goods_logo)) {
            unlink('.'.$old_goods_logo);
        }
        $this->success('修改成功', 'admin/goods/index');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        \app\admin\model\Goods::destroy($id);
        $this->success('删除成功');
    }
}
