<?php
namespace Home\Controller;
use Think\Controller;


// 发布更新等功能

class MineSetSafeController extends MethodController{

	public function __construct(){
		parent::__construct();
		$this->_login();
	}

	public function index(){
		$this->assign('setSafe');//nav_item_active					
		$this->assign('title','安全设置');		
		$this->display();		
	}


	public function actSetPwd(){
		if(!isset($_POST['user_pwd']) || !empty($_POST['user_pwd'])){
			$save['user_pwd']=md5($_POST['user_pwd']);
			$User=M('user');
			$rs=$User->where(array('user_id'=>$_SESSION['user_id']))->save($save);
			if($rs){
				$this->success('设置成功。');
			}else{
				$this->error('抱歉, 设置失败。');
			}
		}else{
			$this->error('抱歉，请输入密码后操作。');
		}
	}



}