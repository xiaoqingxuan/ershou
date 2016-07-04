<?php
namespace Home\Controller;
use Think\Controller;

class LoginController extends MethodController {




	public function index(){

		if(isset($_SESSION['user_id'])){
			$this->redirect('Index/index');
			exit();
		}
		if(isset($_COOKIE['remember_user_email'])){
			$this->assign('user_email',$_COOKIE['remember_user_email']);
		}

		$this->display('html/login');
	}

	public function actLogin(){
		if(isset($_SESSION['user_id'])){
			$this->redirect('Index/index');
			exit();
		}

		$user_email=$_POST['user_email'];
		$user_pwd=$_POST['user_pwd'];

		$user = D("User");
		if(!$user->create()){
			$this->error($user->getError());
			exit();
		}
		$where['user_email']=$user_email;
		$where['user_pwd']=md5($user_pwd);
		$arr=$user->field('user_email,user_id,user_school,user_name,user_icon,school_id')->where($where)->find();


		if(!($arr==null)){
			$Register=new RegisterController();
			$Register->setCk(false,$arr);

			if(isset($_POST['remember'])){
				setcookie('remember_user_email',$arr['user_email'],time()+3600*24*7,'/');				
			}else{
				setcookie('remember_user_email','',0,'/');
			}

			if(isset($_GET['u'])){//因为success回到上页是登陆页
				$this->success("登录成功",U('Index/index?school_id='.$arr['school_id']));				
			}else{
				$this->success("登录成功");					
			}

		}else{
			$this->error("抱歉，登录失败，账号或密码错误");
		}

	}

	public function logout(){
		$rs=session_destroy();
			if(isset($_GET['u'])){//因为个人中心退出若再返回个人中心个人中心会检测是否登陆 所以直接主页
				$this->success("退出成功",U('Index/index?school_id='.$_COOKIE['school_id']));				
			}else{
				$this->success("退出成功");						
			}		
		}

	}