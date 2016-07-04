<?php
namespace Home\Controller;
use Think\Controller;



// 后台主页 条目

class MineSelectSchoolController extends MethodController {

	public function __construct(){
		parent::__construct();
		$this->_login();
	}


	public function index()
	{
		$this->assign('setSchool','改变学校');//nav_item_active	
		$this->assign('title','改变学校');
		$this->display();
	}






}