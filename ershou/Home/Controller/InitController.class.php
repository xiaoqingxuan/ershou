<?php
namespace Home\Controller;
use Think\Controller;

class MethodController extends Controller {


/**
 * 读取栏目
 * @return [type] [description]
 */
	static public function readCat(){
		$Cat=M('category');
		$cat_list=$Cat->field('cat_name,cat_id')->select();
		$this->assign('cat_list',$cat_list);
	}



}