<?php
namespace Home\Controller;
use Think\Controller;



// 后台主页 条目

class MineController extends MethodController {
	public function __construct(){
		parent::__construct();
		$this->_login();
	}



// 我的二手条目也是个人中心主页
	public function index(){
		$list=$this->actIns('goods_used',array('user_id'=>$_SESSION['user_id']),'goods_id,goods_thumb,goods_name,goods_price,add_time,is_sale,user_id');

		$this->assign('used_list',$list);
		$this->assign('index');//nav_item_active		
		$this->assign('title','个人中心');
		$this->display();


	}

	public function listSeek(){


		$field='user_id,seek_name,expect_price,add_time,is_seek,seek_id';
		$list=$this->actIns('goods_seek',array('user_id'=>$_SESSION['user_id']),$field);

		$this->assign('title','我的求购');
		$this->assign('listSeek');//nav_item_active			
		$this->assign('seek_list',$list);
		$this->display('mine_list_seek');

	}





	public function setSafe(){
		$this->assign('setSafe');//nav_item_active					
		$this->display('mine_set_safe');
	}




}