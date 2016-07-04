<?php
namespace Home\Controller;
use Think\Controller;


// 发布更新等功能

class MineSetPersonalController extends MethodController{





	public function __construct(){
		parent::__construct();
		$this->_login();
	}


	public function index(){
		if(isset($_COOKIE['school_name'])){
			$address=$_COOKIE['school_name'];
			$this->assign('address',$address);
		}
		$this->readDefault();
		$this->assign('setPersonal');//nav_item_active					
		$this->assign('title','个人信息设置');				
		$this->display();		
	}

	public function actSetPersonal(){
		//过滤为空的字段
		foreach ($_POST as $k=>$v) {
			if(empty($v)){
				unset($_POST[$k]);
			}
		}

		$rules = array(
			array('user_name','','该用户名已经存在。',3,'unique'),
			array('default_qq','number','QQ号只能是数字。',2),			
			array('default_phone','number','手机号只能是数字。',2),
			);
		if(!empty($_POST['user_name']) || !empty($_POST['default_phone']) || !empty($_POST['default_address'])|| !empty($_POST['default_qq']) ){
			$User=M('user');
			$rs=$User->validate($rules)->create();
			if(!$rs){
				$this->error($User->getError());
			}else{
				$rs_=$User->where(array('user_id'=>$_SESSION['user_id']))->save($_POST);
				if(!empty($_POST['user_name'])){
					setcookie('user_name',$rs['user_name'],time()+3600*24*300,'/');					
				}
				if($rs_){
					$this->success('设置成功。');
				}else{
					$this->error('抱歉, 设置失败, 至少修改一项。');
				}	
			}
		}else{
			$this->error('抱歉, 至少修改一项。');
		}

	}


}