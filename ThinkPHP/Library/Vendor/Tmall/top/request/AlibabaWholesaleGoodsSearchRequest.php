<?php
/**
 * TOP API: alibaba.wholesale.goods.search request
 * 
 * @author auto create
 * @since 1.0, 2015.07.08
 */
class AlibabaWholesaleGoodsSearchRequest
{
	/** 
	 * SearchGoodsOption
	 **/
	private $paramSearchGoodsOption;
	
	private $apiParas = array();
	
	public function setParamSearchGoodsOption($paramSearchGoodsOption)
	{
		$this->paramSearchGoodsOption = $paramSearchGoodsOption;
		$this->apiParas["param_search_goods_option"] = $paramSearchGoodsOption;
	}

	public function getParamSearchGoodsOption()
	{
		return $this->paramSearchGoodsOption;
	}

	public function getApiMethodName()
	{
		return "alibaba.wholesale.goods.search";
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
