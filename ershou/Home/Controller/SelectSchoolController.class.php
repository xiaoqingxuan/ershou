<?php 
namespace Home\Controller;
use Think\Controller;



/**
* 选择学校
*/
class SelectSchoolController extends MethodController{
	
	public function index(){
		if(isset($_SESSION['user_id'])){
			$this->redirect('MineSelectSchool/index');
			exit();
		}
		$this->display();
	}

	public function actSchool(){

		if(isset($_POST['school_id']) && !empty($_POST['school_id'])){
	    $region_id=$_POST['school_id'];
	    $Region=M('region');
	    $field='region_name';
	    $school_find=$Region->where(array('region_id'=>$region_id))->field($field)->find();
	    setcookie('school_name',$school_find['region_name'],time()+3600*24*300,'/');        
	    $rs=setcookie('school_id',$region_id,time()+3600*24*300,'/');
	    $this->school=array('school_name'=>$school_find['region_name'],'school_id'=>$region_id);

			// 如果是从个人中心点的改变学校
			if(isset($_SESSION['user_id'])){
				$User=M('user');
				$save=array('user_school'=>$school_find['region_name'],'school_id'=>$_POST['school_id']);
				$rs=$User->where(array('user_id'=>$_SESSION['user_id']))->save($save);	
			}

			if($rs){
				$redirect=U('Index/index');
				$this->success('设置成功',$redirect);
				exit();
	    	$this->redirect($redirect); 				
			}else{
				$this->error('抱歉, 设置失败。');
			}
		}else{
			$this->error('抱歉, 请选择学校后再点击切换。');			
		}
	}



}





 ?>