<?php

/**
 * 参数集合对象
 * @author auto create
 */
class ActBaseContext
{
	
	/** 
	 * 天猫内容平台的活动主建，唯一标示一条内容
	 **/
	public $act_id;
	
	/** 
	 * 内容标签，多个用空格分隔
	 **/
	public $act_keys;
	
	/** 
	 * 调用此接口的应用的名称
	 **/
	public $app_name;
	
	/** 
	 * 活动描述，100个字以内
	 **/
	public $description;
	
	/** 
	 * DO 的根据type选择相关的字段填写
	 **/
	public $element_ext_d_os;
	
	/** 
	 * 活动结束时间
	 **/
	public $end_time;
	
	/** 
	 * 录入数据的页面类型， 0 商家  22 粉丝趴  65 媒体商
	 **/
	public $page_id;
	
	/** 
	 * 外部业务的业务主键
	 **/
	public $refer_id;
	
	/** 
	 * 活动对应的资源位id,资源位id 需要提前申请，资源位确定后资源位需要的素材也确定了
	 **/
	public $res_id_list;
	
	/** 
	 * 资源位具体素材信息，key 格式为 资源位Id#资源位模板id#素材key   value 为素材信息，图片仅保存文件名，不需要全路径
	 **/
	public $res_info_map;
	
	/** 
	 * 活动开始时间
	 **/
	public $start_time;
	
	/** 
	 * 活动标题，100个字以内
	 **/
	public $title;
	
	/** 
	 * 用户id，必填
	 **/
	public $user_id;	
}
?>