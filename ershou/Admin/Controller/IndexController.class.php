<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function __construct(){
        parent::__construct();
        if(isset($_SESSION['user_name'])){
            $this->redirect('Admin/index');            
        }
    }

    /**
     * 登陆界面
     * @return [type] [description]
     */
    public function index(){
    	$this->display();
    }

    /**
     * 处理登陆
     * @return [type] [description]
     */
    public function actLog(){
    	$Admin=M('admin');
    	$where=array('user_name'=>$_POST['user_name'],'user_pwd'=>md5($_POST['pwd']));
    	$rs=$Admin->where($where)->find();
    	if($rs){
            session_start();
            $_SESSION['user_name']=$rs['user_name'];
            $this->redirect('Admin/index');
        }else{
          $this->error('账号/密码错误');
        }
    }



}