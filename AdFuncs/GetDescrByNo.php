<?php
function GetDescrByNo($no, $subgroupCode)	//возвращает описание товара мерлиона по номеру товара и
													// номеру его подгруппы
{
	global $client;
	
	static $descrArr = array();
	static $prevSubgroupCode = "";
		
	$retval = array();
		
	if($subgroupCode != $prevSubgroupCode)
	{
		$descrArr = array();
		$pageNum = 1;
		do
		{
			$nextPropsPage = $client->getItemsProperties($subgroupCode, "" , $pageNum++, ROWS_ON_PAGE);
			$descrArr = array_merge($descrArr, $nextPropsPage->item);
			unset($nextPropsPage);
		}while(count($nextPropsPage->item) == ROWS_ON_PAGE);
		
		$prevSubgroupCode = $subgroupCode;
	}
	
	foreach($descrArr as $item)
	{
		if($item->No == $no)
			$retval[] = $item;
		else if(!empty($retval))
			break;
	}
	
	return $retval;
}
?>