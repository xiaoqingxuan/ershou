<?php
namespace Home\Model;
use Think\Model;

class UserModel extends Model{

	//登录的。注册的采用动态验证
  protected $_validate=array(
  		array('user_email','email','抱歉，请输入正确的邮箱格式',1),
  		array('user_pwd','require','抱歉，密码必填',1)
  	);



    
}