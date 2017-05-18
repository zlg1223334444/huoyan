<?php
/**
 * TOP API: tmall.content.post.act.add request
 * 
 * @author auto create
 * @since 1.0, 2017.02.24
 */
class TmallContentPostActAddRequest
{
	/** 
	 * 参数集合对象
	 **/
	private $context;
	
	private $apiParas = array();
	
	public function setContext($context)
	{
		$this->context = $context;
		$this->apiParas["context"] = $context;
	}

	public function getContext()
	{
		return $this->context;
	}

	public function getApiMethodName()
	{
		return "tmall.content.post.act.add";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
