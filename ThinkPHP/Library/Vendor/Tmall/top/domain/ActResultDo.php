<?php

/**
 * result结果对象
 * @author auto create
 */
class ActResultDo
{
	
	/** 
	 * errorMessage，错误的时候才有
	 **/
	public $error_message;
	
	/** 
	 * result，返回结果的数据体
	 **/
	public $result;
	
	/** 
	 * success，true 位操作成功，否则为失败
	 **/
	public $success;	
}
?>