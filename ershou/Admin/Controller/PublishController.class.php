<?php
namespace Admin\Controller;
use Think\Controller;

class PublishController extends InitController{


	public function edit(){
		$edit_id=$_GET['id'];

		$News=M('news');
		$rs=$News->where(array('id'=>$edit_id))->field('*')->find();

		$this->assign('publishList','1');
		$this->assign('title','修改《'.$rs['title'].'》');
		$this->assign('list',$rs);
		$this->display('publish');
	}
	public function actEdit(){
		$rules=array(
			array('title','require','抱歉，标题必填',1),
			array('content','require','抱歉，内容必填',1),
			);
		$_POST['add_time']=time();
		$_POST['add_user']=$_SESSION['user_name'];
		$News=M('news');
		$rs=$News->validate($rules)->create();
		if(!$rs){
			$this->error($News->getError());
		}else{
			$rs=$News->where(array('id'=>$_GET['edit_id']))->save($_POST);
			if($rs){
				$this->success('更新成功',U('Publish/newsList'));
			}else{
				$this->error('更新失败');
			}

		}

	}

	public function publish(){
		$this->assign('title','发布公告');		
		$this->assign('publishNews','1');		
		$this->display();
	}



	public function newsList(){
		$News=M('news');
		$count=$News->where('1')->count();        
    $page=new \Think\Page($count,7);//分页类。参数总条数和每页条数。
    $show=$page->show();//返回分页块的html代码        
    $rs=$News->field('*')->limit($page->firstRow.','.$page->listRows)->order('add_time DESC')->select();
    $this->assign('page',$show);
    $this->assign('title','修改公告');
    $this->assign('publishList','1');
    $this->assign('list',$rs);
    $this->display();
  }



  public function delNews(){
  	$id=$_GET['del_id'];
  	$User=M('news');
  	$rs=$User->where(array('id'=>$id))->delete();
  	if($rs){
  		$this->success('删除成功');
  	}else{
  		$this->error('删除失败');
  	}
  }


  public function actAdd(){
  	$rules=array(
  		array('title','require','抱歉，请输入标题',1),
  		array('content','require','抱歉，请输入内容',1),
  		);
  	$_POST['add_time']=time();
  	$_POST['add_user']=$_SESSION['user_name'];
  	$News=M('news');
  	$rs=$News->validate($rules)->create();
  	if(!$rs){
  		$this->error($News->getError());
  	}else{
  		$rs=$News->add();
  		if($rs){
  			$this->success('发布成功');
  		}else{
  			$this->eroor('发布失败');
  		}

  	}

  }

}