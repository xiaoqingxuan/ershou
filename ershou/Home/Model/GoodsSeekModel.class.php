<?php
namespace Home\Model;
use Think\Model\RelationModel;

class GoodsSeekModel extends RelationModel{

	protected $_link = array(
		'user'=>array(
			'mapping_type'=>self::BELONGS_TO,
			'foreign_key'=>'user_id',
			'mapping_fields'=>'user_name'
			)

		);






}



?>