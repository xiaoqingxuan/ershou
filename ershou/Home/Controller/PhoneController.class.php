<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 接收类型GET
 * key：?receive={"key":"123"}
 */
class PhoneController extends MethodController {
	protected $key='123';
	protected $return=null;
	protected $receive=null;//存储get的receive的数组形式数据

	public function __construct(){

		parent::__construct();
		//验证是否有GET
		if(!isset($_GET['receive']) || empty($_GET['receive'])){
			$this->return=array('err'=>1,'reason'=>'no GET or GET data is empty');
			$this->rt($this->return);			
		}

		//验证key
		$get=$_GET['receive'];
		$get=json_decode($get,true);
		if(!($get['key']==$this->key)){
			$this->return=array('err'=>1,'reason'=>'key error or JSON format error(Some no value)');
			$this->rt($this->return);				
		}
		$this->receive=$get;
		
	}

/**
 * 返回json。若无参数，则返回err=0表示操作正确，有参数需要自己指定$arr
 * @param  array  $arr [description]
 * @return [type]      [description]
 */
	protected function rt($arr=array()){
		if(!isset($arr['err'])){
			$arr['err']=0;
		}
		$this->return=$arr;
		exit(json_encode($this->return));		
		//使用unicode替换为中文的方法 可能丢失字符。
		// exit(preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))",json_encode($this->return)));
	}


// 下架一个商品
// {"key":"123","code":"used","goods_id":"111","is_sale":"0"}
// is_sale是当前的状态 服务器会自动取反它
	public function isSale(){
		if(isset($this->receive['code']) || empty($this->receive['code']) || isset($this->receive['goods_id']) || !empty($this->receive['goods_id']) || isset($this->receive['is_sale']) || !empty($this->receive['is_sale'])){
			$re=$this->receive;
			if($re['code']=='used'){
				$id=$re['goods_id'];
				$is_sale=$re['is_sale'];			
				$m='goods_used';
				$k='goods_id';
				$k2='is_sale';
			}elseif($re['code']=='seek'){
				$id=$re['goods_id'];				
				$is_sale=$re['is_sale'];			
				$m='goods_seek';
				$k='seek_id';
				$k2='is_seek';			
			}else{
				$this->rt(array('err'=>'1','reason'=>'code参数只能为used或seek。'));
			}

		}else{
			$this->rt(array('err'=>'1','reason'=>'参数错误。'));
		}
		


		$Goods=M($m);

		if($is_sale==0){
			$is_sale=1;
		}elseif($is_sale==1){
			$is_sale=0;
		}else{
				$this->rt(array('err'=>'1','reason'=>'is_sale错误'));			
		}

		$where[$k]=$id;
		$save=array($k2=>$is_sale);
		$rs=$Goods->where($where)->save($save);
		if($rs){
			$this->rt();
		}else{
			$this->rt(array('err'=>1,'reason'=>'失败代码123。'));
		}

	}


/**
 * 查询是否收藏该商品        
 *{"key":"123","user_id":"6","type":"used","goods_id":"129"}
 * @return boolen        [description]
 */
public function queryLike(){
		if(isset($this->receive['goods_id']) || !empty($this->receive['goods_id']) || isset($this->receive['type']) || !empty($this->receive['type']) || isset($this->receive['user_id']) || !empty($this->receive['user_id'])){
			$where=array(
				'goods_id'=>$this->receive['goods_id'],
				'type'=>$this->receive['type'],
				'user_id'=>$this->receive['user_id']
				);
		}else{
			$this->rt(array('err'=>1,"reason"=>'json参数错误。'));			
		}


    $Like=M('collect');
    $rs=$Like->where($where)->find();
    if($rs){
    	$this->rt(array('is_collect'=>1));
    }else{
    	$this->rt(array('is_collect'=>0));
    }

}



/**
 * ajax删一个商品 可扩展
 * parm:goods_id，code
 * method:post
 * @return [type] [description]
 */
public function delete(){

	if(isset($_POST['code']) || !empty($_POST['code']) || isset($_POST['goods_id']) || !empty($_POST['goods_id'])){
    if($_POST['code']=='used'){ //删二手
    	$id=$_POST['goods_id'];
    	$m='goods_used';
    	$k='goods_id';
    }elseif($_POST['code']=='seek'){  //删求购
    	$id=$_POST['goods_id'];       
    	$m='goods_seek';
    	$k='seek_id';
    }elseif($_POST['code']=='collection'){  //删收藏
      $id=$_POST['goods_id'];//删的值
      $m='collect';//哪个表
      $k='id';//删的键
    }else{
    	$this->rt(array('err'=>1,'reason'=>'请指定code为used或seek。'));
    	exit();
    }
  }else{
  	$this->rt(array('err'=>1,'reason'=>'参数错误'));  	
  }

  $Goods=M($m);
  $where[$k]=$id;
  $rs=$Goods->where($where)->delete();    

    //若是删二手/求购，也删收藏表对应商品
  if(($m=='goods_used')||($m=='goods_seek')){
  	$Obj=M('collect');
  	$where['goods_id']=$id;
  	$where['type']=$_POST['code'];
  	print_r($where);
  	$rs_=$Obj->where($where)->delete();      
  }



  if($rs){
  	$this->rt();
  }else{
  	$this->rt(array('err'=>1,'reason'=>'删除失败'));
  }
} 


	/**
	 * 收藏
	 * {"key":"123","user_id":"5","type":"used","goods_id":"125","now":"1"}
	 * 如果now发来为1，说明要取消收藏。
	 * now为0，则添加收藏
	 */
	public function likeAct(){
		//$add是要存的
		if(isset($this->receive['goods_id']) || !empty($this->receive['goods_id'])){
			$where['goods_id']=$this->receive['goods_id'];
			$add['goods_id']=$this->receive['goods_id'];			
		}else{
			$this->rt(array('err'=>1));
		}

		if(isset($this->receive['type']) || !empty($this->receive['type']) || isset($this->receive['user_id']) || !empty($this->receive['user_id'])){
			$add['type']=$this->receive['type'];
			$add['add_time']=time();
			$add['user_id']=$this->receive['user_id'];	
			$where['type']=$this->receive['type'];			
			$where['user_id']=$this->receive['user_id'];					
		}else{
			$this->rt(array('err'=>1));
		}		

   	$Goods=M('collect');
    // $find=$Goods->where($where)->find(); 目前采用前端发来是否已经收藏减轻服务器压力所有屏蔽
    // now为1则要删除某收藏
    if($this->receive['now']==1){
            //已经有则取消收藏
        $rs_=$Goods->where($where)->delete();
        if($rs_){
        	$this->rt(array('reason'=>'取消收藏操作成功'));
        }else{
				$this->rt(array('err'=>1,'reason'=>'删除失败'));
        }
    }else{
	    //now不为1则是添加收藏
	    $rs=$Goods->add($add);
	    if($rs){
        	$this->rt(array('reason'=>'收藏成功'));
	    }else{
				$this->rt(array('err'=>1,'reason'=>'收藏成功'));
	    }		
	  }
	}

	/**
	 * 我的收藏列表
	 * ?receive={"key":"123","user_id":"5","current_page":2,"page_num":15}
	 */
	public function myCollection(){


		if(isset($this->receive['current_page']) || !empty($this->receive['current_page'])){
			$current_page=$this->receive['current_page'];
		}else{
			$current_page=1;
		}
		if(isset($this->receive['page_num']) || !empty($this->receive['page_num'])){
			$page_num=$this->receive['page_num'];
		}else{
			$page_num=15;
		}		
		if(!isset($this->receive['user_id'])){
			$this->rt(array('err'=>1,'reason'=>'no user_id!'));
		}else{
			$user_id=$this->receive['user_id'];			
		}
	
		$field='*';
		$list=$this->actIns('collect',array('user_id'=>$user_id),$field,$page_num,true,$current_page);


		if(empty($list)){
			$this->rt(array('err'=>'1','collection_list'=>'1','reason'=>'list empty!'));
		}
		$this->rt(array('collection_list'=>$list));

	}


	/**
	 * 我发布的闲置
	 * 	 * 请求：?receive={"key":"123","user_id":"5","current_page":2,"page_num":15,"is_sale"}
	 * 	 返回类似请求selectUsed方法，多了reason字段。
	 * 	 		// is_sale为1读取在卖 为0无效 为2或不填读取全部
	 */
	public function myPublishUsed(){
		if(isset($this->receive['current_page']) || !empty($this->receive['current_page'])){
			$current_page=$this->receive['current_page'];
		}else{
			$current_page=1;
		}
		if(isset($this->receive['page_num']) || !empty($this->receive['page_num'])){
			$page_num=$this->receive['page_num'];
		}else{
			$page_num=15;
		}		
		if(!isset($this->receive['user_id'])){
			$this->rt(array('err'=>1,'reason'=>'no user_id!'));
		}else{
			$user_id=$this->receive['user_id'];			
		}
		// is_sale为1读取在卖 为0无效 为2或不填读取全部
		if(isset($this->receive['is_sale']) || !empty($this->receive['is_sale'])){
			$is_sale=$this->receive['is_sale'];
			if($is_sale==2){
				$where=array('user_id'=>$user_id);				
			}else{
				$where=array('user_id'=>$user_id,'is_sale'=>$is_sale);
			}
		}else{
			$where=array('user_id'=>$user_id);
		}		
		$field='goods_id,goods_thumb,goods_name,goods_price,add_time,is_sale,user_id';
		$list=$this->actIns('goods_used',$where,$field,$page_num,false,$current_page);

		if(empty($list)){
			$this->rt(array('err'=>'1','used_list'=>'1','reason'=>'list empty!'));
		}
		$this->rt(array('used_list'=>$list));

	}


	/**
	 * 发布二手
	 */
	public function publishUsed(){
		$Publish=new MinePublishController(true);
		$rules_comp=array(
			array('add_time','getTime',1,'function'),
		);

		$rules=$Publish->_rules;
		$user_id=array('user_id','require','抱歉,请指定用户',1);
		$goods_school=array('goods_school','require','抱歉,请指定发布学校',1);
		array_unshift($rules, $user_id);
		array_unshift($rules, $goods_school);



		$Goods=D('goods_used');

		$arr=$Goods->validate($rules)->auto($rules_comp)->create();
		if(!$arr){
			$this->rt(array('err'=>1,'reason'=>$Goods->getError()));
		}else{
			//暂时用....记得改~ 暂时取第一张图作为缩略图
			$arr['goods_thumb']=$arr['goods_img_1'];
			$rs=$Goods->add($arr);
			if($rs){
				$this->rt();				
			}else{
				$this->rt(array('err'=>1,'reason'=>'未知原因'));
			}
		}

	}
	/**
	 * 图片上传
	 * POST请求：/Phone/upload/?receive={"key":"123"}
	 * 
	 * @return [type] [description]
	 */
	public function uploadImg(){
		$Upload=new \Think\Upload;
		$Upload->maxSize=1024*1024;
		$Upload->exts=array('jpg','gif','png','jpeg');
		$img_root_path=C('TMPL_PARSE_STRING');
		$Upload->rootPath=$img_root_path['__UPLOAD_IMG__'].'/goods/img/';
		$Upload->autoSub=false;
		$info=$Upload->upload();

		if(!$info){
			$this->rt(array('err'=>1,'reason'=>$Upload->getError()));	
		}else{
			$this->rt(array('info'=>$info));				
		}
	}

	/**
	 * 头像上传
	 * POST请求：/Phone/upload/?receive={"key":"123"}
	 * 
	 * @return [type] [description]
	 */
	public function uploadImgUserIcon(){
			if(!isset($_POST['user_id']) || empty($_POST['user_id'])){
				$this->rt(array('err'=>'1','reason'=>'no user_id'));
			}

		$Upload=new \Think\Upload;
		$Upload->maxSize=1024*1024;
		$Upload->exts=array('jpg','gif','png','jpeg');
		$img_root_path=C('TMPL_PARSE_STRING');
		$Upload->rootPath=$img_root_path['__UPLOAD_IMG__'].'/user/icon/';
		$Upload->autoSub=false;
		$info=$Upload->upload();

		if(!$info){
			$this->rt(array('err'=>1,'reason'=>$Upload->getError()));	
		}else{
			//更新用户表
			$User=M('user');
			$save['user_icon']=$info['file']['savename'];
			$where['user_id']=$_POST['user_id'];
			$User->where($where)->save($save);
			if($User){
  			$img_url=C('TMPL_PARSE_STRING');				
				$img_url='http://'.$_SERVER['HTTP_HOST'].$img_url['__IMG__'].'/user/icon/';
				$this->rt(array('savename'=>$save['user_icon'],'savepath'=>$img_url));				
			}else{
				$this->rt(array('err'=>1,'reason'=>'上传成功后更新失败。'));					
			}
		}
	}


	// 移动端注册
	/**
	 * [rg description]
	 * @return [type] [description]
	 *?receive={"key":123,"user_email":"123@ab.com","user_pwd":"fwfa","user_name":"小明"}
	 */
	public function rg(){
		$User=M('User');		

		$Rg=new RegisterController();
		$rs=$User->validate($Rg->rules)->create();


		if(!$rs){
			$this->return=array('err'=>1,'reason'=>$User->getError());
			$this->rt($this->return);		
		}else{
			$_POST['user_pwd']=md5($_POST['user_pwd']);
			$rs=$User->add($_POST);
			if($rs){
				$this->rt($this->getLogin($_POST['user_email']));		
			}else{
				$this->return=array('err'=>1,'reason'=>'错误失败，错误代码33');
				$this->rt($this->return);					
			}
		}

	}



	// /**
	//  * 登陆状态直接返回数据
	//  */
	// protected function is_login($User){
	// 	if(isset($_SESSION['user_id'])){
	// 		$where['user_id']=$_SESSION['user_id'];		
	// 		$arr=$User->field('user_email,user_id,user_school,user_city,user_name,user_icon')->where($where)->find();						
	// 		$arr['login']=1;		
	// 		$this->rt($this->rt($arr));
	// 	}

	// }

	/*
	登陆
	post 
	 */
	public function lg(){
		$User = D("User");	

		if(!$User->create()){
			$this->return=array('err'=>1,'reason'=>$User->getError());
			$this->rt($this->return);					
		}

		$where['user_email']=$_POST['user_email'];
		$where['user_pwd']=md5($_POST['user_pwd']);
		$arr=$User->field('user_email,user_id,user_school,user_city,user_name,user_icon')->where($where)->find();
		if(!($arr==null)){
			$this->rt($this->getLogin());			
		}else{
			$this->return=array('err'=>1,'reason'=>'抱歉,账号或密码错误。','login'=>0);			
			$this->rt($this->return);								
		}

	}

	/**
	 * 登陆成功后查询登陆信息 对于$_POST的
	 * @return [type] [description]
	 */
	protected function getLogin($user_email){
		$User=M('user');
		if(isset($user_email) && !empty($user_email)){
			$where['user_email']=$user_email;			
		}else{
			$where['user_email']=$_POST['user_email'];			
		}
		$arr=$User->field('user_email,user_id,user_school,user_city,user_name,user_icon')->where($where)->find();						
		$arr['login']=1;
		return $arr;	
	}

	public function logout(){
		$rs=session_destroy();
		if($rs){
			$this->return=array('login'=>0);						
			$this->rt($this->return);							
		}	
	}

	/**
 * 查询当前登陆的用户信息
 * @return 若登陆返回login=1字段的用户信息
 *         若不登陆状态返回login=0
 *请求示例：http://localhost/ershou/index.php/Home/Phone/selectUser/?receive={%22key%22:%22123%22}
 */
	public function selectUser(){
		if(!isset($_SESSION['user_id'])){
			$this->rt(array('login'=>0,'err'=>1));
		}else{
			$User=M('user');
			$where['user_email']=$_SESSION['user_email'];
			$arr=$User->field('user_email,user_id,user_school,user_city,user_name,user_icon')->where($where)->find();
			$this->rt($arr);				
		}



	}

	/**
	 * 查询一条商品详细信息
	 * 参数为json有used_id。如	
	 * 正确返回：如：{"0":{"goods_id":"99","user_id":"5","cat_id":"5","goods_name":"\u6c34\u679c\u624b\u673a\u51fa\u552e","goods_price":"5555.00","goods_price_pre":"6666.00","goods_desc":"1\u6210\u65b0\u3002\u3002\u3002\u3002\u3002\u3002\u3002\u4f7f\u7528\u4e861\u4e07\u5e74\u3002\u3002\u3002\u3002","goods_thumb":"http:\/\/localhost\/ershou\/ershou\/Common\/images\/goods\/img\/get.jpg","goods_img_1":"get.jpg","goods_img_2":null,"goods_img_3":null,"goods_img_4":null,"original_img":null,"click_count":"0","trade_address":"\u5c0f\u8857\u9053","phone_number":"12222223333","add_time":"10\u5c0f\u65f6\u524d","is_dis":"0","is_sale":"1","goods_school":"\u4e94\u9091\u5927\u5b66","city":"\u6c5f\u95e8\u5e02","user":{"user_name":"\u8986\u76d6","user_city":"\u6c5f\u95e8\u5e02"}},"err":0}
	 * 错误返回：{"err":0} 可能错误，该商品不存在/参数不正确
	 */
	public function selectUsedContent(){
		if(isset($this->receive['used_id']) || !empty($this->receive['used_id'])){
			$goods_id=$this->receive['used_id'];
		}else{
			return $this->rt(array('err'=>'1'));
		}

		$field=null;
		$where=array('goods_id'=>$goods_id);
		$detail=$this->actIns('GoodsUsed',$where,$field,1,true);
		// 将多张图片放到一个数组
		$goods_img=array($detail[0]['goods_img_1'],$detail[0]['goods_img_2'],$detail[0]['goods_img_3'],$detail[0]['goods_img_4']);
		unset($detail[0]['goods_img_1'],$detail[0]['goods_img_2'],$detail[0]['goods_img_3'],$detail[0]['goods_img_4']);			
		$img_count=count($goods_img);
		for($i=0;$i<$img_count;$i++){
			if(empty($goods_img[$i])){
				unset($goods_img[$i]);
			}
		}
		$detail[0]['goods_img']=$goods_img;


		if(empty($detail)){
			$this->rt(array('err'=>1,'reason'=>'the goods no data'));
		}
		$this->rt(array('used_content'=>$detail));
	}

	/**
	 * 查询某学校的数据
	 * 请求：?receive={"key":"123","user_school":"小学","current_page":2,"page_num":15,"cat_id":"3"}
	 * current_page是当前页码。page_num是请求条数 cat_id：栏目id 非必须
	 * @return 有信息返回user_list 无信息user_list值为空
	 */
	public function selectUsed(){
		if(isset($this->receive['user_school']) || !empty($this->receive['user_school'])){
			$school=$this->receive['user_school'];
		}else{
			$school=$this->school;		
		}
		if(isset($this->receive['current_page']) || !empty($this->receive['current_page'])){
			$current_page=$this->receive['current_page'];
		}else{
			$current_page=1;
		}
		if(isset($this->receive['page_num']) || !empty($this->receive['page_num'])){
			$page_num=$this->receive['page_num'];
		}else{
			$page_num=15;
		}
		if(isset($this->receive['cat_id']) || !empty($this->receive['cat_id'])){
			$cat_id=$this->receive['cat_id'];
			$where['cat_id']=$cat_id;
		}	

		$field='user_id,goods_id,goods_thumb,goods_price,goods_name,add_time,trade_address,goods_desc';
		$where['is_sale']=1;
		$where['goods_school']=$school;
		$list=$this->actIns('GoodsUsed',$where,$field,$page_num,false,$current_page);
		if(empty($list)){
			$this->rt(array('err'=>'1','used_list'=>'1'));
		}
		$this->rt(array('used_list'=>$list));
	}

	/**
	 * 根据省id查询学校
	 * 需要省id。如：?receive={"key":"123","province_id":"6"}
	 * 返回：
	 * 失败：
	 * err=1时：字段错误 或 查询数据为空
	 *如：{"err":"1","empty":"1"}
	 * 正确如：
	 * {"school_list":[{"region_id":"3413","region_name":"\u5e7f\u4e1c\u6d77\u6d0b\u5927\u5b66"},{"region_id":"3412","region_name":"\u66a8\u5357\u5927\u5b66"},{"region_id":"3411","region_name":"\u534e\u5357\u519c\u4e1a\u5927\u5b66"},{"region_id":"3410","region_name":"\u534e\u5357\u7406\u5de5\u5927\u5b66"},{"region_id":"3409","region_name":"\u4e94\u9091\u5927\u5b66"}],"err":0}
	 */
	public function selectSchool(){
		if(isset($this->receive['province_id']) || !empty($this->receive['province_id'])){
			$region_id=$this->receive['province_id'];
		}else{
			return $this->rt(array('err'=>'1','reason'=>'no province_id key or key is empty'));
		}

		$School=M('region');
		$school_list=$School->where(array('parent_id'=>$region_id))->field('region_id,region_name')->select();

		if(empty($school_list)){
			$this->rt(array('err'=>'1','empty'=>'1'));			
		}

		$this->rt(array('school_list'=>$school_list));

	}

	/**
	*查询省名字和id
	* 请求：/ershou/index.php/Home/Phone/selectProvince/?receive={"key":"123"} 无其他请求参数
	*返回json
	*
	* 
	 */
	public function selectProvince(){
		$School=M('region');
		$school_list=$School->where(array('region_type'=>1))->field('region_id,region_name')->select();
		if(empty($school_list)){
			$this->rt(array('err'=>'1','empty'=>'1'));			
		}
		$this->rt(array('school_list'=>$school_list));
	}




	/**
	 * 读取分类
	 * ?receive={"key":"123"}
	 */
	public function getCat(){
		$list=$this->readCat();
		$this->rt(array('cat'=>$list));
	}






}


