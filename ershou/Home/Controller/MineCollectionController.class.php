<?php
namespace Home\Controller;
use Think\Controller;


// 发布更新等功能

class MineCollectionController extends MethodController{







	public function index(){		
		$list=$this->actIns('collect',array('user_id'=>$_SESSION['user_id']));
    $this->assign('list',$list);
    $this->assign('title','我的收藏');

		$this->assign('listCollection');//nav_item_active					
		$this->display('index');
	}

}