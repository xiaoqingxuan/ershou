<?php
namespace Home\Controller;
use Think\Controller;





class RegisterController extends MethodController {


	public function index(){

		if(isset($_SESSION['user_email'])){
			redirect(U('Index/index'));
			exit();
		}		
		$this->display('html/register');
	}


	public $rules = array(
		array('user_name','require','抱歉，用户名必填',0),
		array('user_name','require','抱歉，该用户名已被注册',0,'unique'), 
		array('user_email','email','抱歉，邮箱格式错误或没填',1),
		array('user_email','require','抱歉，该邮箱已被注册',1,'unique'), 
		array('user_pwd','require','抱歉，密码还没填',1),	
		array('user_school','require','抱歉，请选择学校',0),
		array('verify','require','抱歉，验证码必须填',0),  
		array('verify','verify_check','抱歉，验证码错误',0,'function'),  
		);

	public function actRegister(){
		$user=M('User');
		$rs=$user->validate($this->rules)->create();

		if(!$rs){
			$this->error($user->getError());
		}else{
			$_POST['user_pwd']=md5($_POST['user_pwd']);
			$rs=$user->add($_POST);
			if($rs){
				$where['user_email']=$_POST['user_email'];
				$this->setCk(true,array(),$where);
				$this->success("注册成功");
			}
		}

	}



/**
 * 根据学校id查学校名字
 * @param  [type] $region_id [description]
 * @return [type]            [description]
 */
  public function getSchoolName($region_id){
      $Region=M('region');
      $field='region_name';
      $school_find=$Region->where(array('region_id'=>$region_id))->field($field)->find();
      return $school_find['region_name'];
  }



/**
 * 注册或登录要查询并设置相关的session和cookie
 * @param array $where [description]
 */
public function setCk($rg=true,$arr=array(),$where=array()){
	if($rg){
		$user=M('User');
		$arr=$user->field('user_email,user_id,user_name,user_icon,school_id')->where($where)->find();			
	}


	$school_name=$this->getSchoolName($arr['school_id']);
	setcookie('school_name',$school_name,time()+3600*24*300,'/');  	


	$_SESSION['user_email']=$arr['user_email'];		
	$_SESSION['user_id']=$arr['user_id'];
	setcookie('school_id',$arr['school_id'],time()+3600*24*300,'/');  	
	setcookie('user_email',$arr['user_email'],time()+3600*24*7,'/');				
	setcookie('user_name',$arr['user_name'],time()+3600*24*300,'/');
	setcookie('user_icon',$arr['user_icon'],time()+3600*24*300,'/');
	return $arr;
}

// 验证码产生 
public function verify(){

	$config=array(
			'fontSize'=>30, // 验证码字体大小
			'length'=>3, // 验证码位数
			'useNoise' =>false, // 关闭验证码杂点
			'useCurve'=>false,
			);

	$verify=new \Think\Verify($config);
	$verify->entry();
}




}



