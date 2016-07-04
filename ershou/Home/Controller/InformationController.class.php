<?php
namespace Home\Controller;
use Think\Controller;

class InformationController extends Controller {


public function download(){
	$this->assign('title','下载');
	$this->assign('download');
  $this->display('download');
}

public function accusation(){
	$this->assign('title','举报');
	$this->assign('accusation');
  $this->display('accusation');
}
public function feedback(){
	$this->assign('title','反馈');
	$this->assign('feedback');
  $this->display('feedback');
}
public function we(){
	$this->assign('title','我们');
	$this->assign('we');
  $this->display('we');
}

public function follow(){
	$this->assign('title','关注');
	$this->assign('follow');
  $this->display('follow');
}

}