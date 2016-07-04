<?php
namespace Home\Model;
use Think\Model\RelationModel;

class GoodsUsedModel extends RelationModel{

	protected $_link = array(
		'user'=>array(
			'mapping_type'=>self::BELONGS_TO,
			'foreign_key'=>'user_id',
			'mapping_fields'=>'user_name'
			),
		'region'=>array(
			'mapping_type'=>self::BELONGS_TO,
			'foreign_key'=>'school_id',
			'mapping_fields'=>'region_name'
			),


		);






}



?>