<?php 
namespace Admin\Controller;
use Think\Controller;


class InitController extends Controller{
	public function __construct(){
		parent::__construct();
		if(!isset($_SESSION['user_name'])){
			$this->error('未登陆,请先登陆',U('Index/index'));
		}
	}









}



<textarea cols="50" rows="15" name="code" class="php">&lt;?php 
start matlab first $cmd="matlab -nodisplay -r /"addpath /home/ligl/matlabsrc/matrixadd//",/"
matlabcalc()/""; print $cmd;
$wline = system($cmd,$retval); ?&gt;</textarea> 
 
 ?>