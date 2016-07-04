<?php
namespace Admin\Controller;
use Think\Controller;

class AdminController extends InitController{

	/**
	 * 登陆后的主页
	 * @return [type] [description]
	 */
	public function index(){
		$this->assign('title','管理');
		$this->display();
	}


	public function logout(){
		session_destroy();
		$this->redirect('Index/index');          
	}



}