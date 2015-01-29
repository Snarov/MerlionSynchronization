<?php
function GetImgsByNo($no, $subgroupCode)
{
	global $client;
	
	static $imgsArr = array();
	static $prevSubgroupCode = "";
	
	$retval = array();
	
	if($subgroupCode != $prevSubgroupCode)
	{
		$imgsArr = array();
		$pageNum = 1;
		do
		{	
			$nextImgsPage = $client->getItemsImages($subgroupCode, "", $pageNum++, ROWS_ON_PAGE);
			$imgsArr = array_merge($imgsArr, $nextImgsPage->item);
			unset($nextImgsPage);
		}while(count($nextImgsPage->item) == ROWS_ON_PAGE);
		$prevSubgroupCode = $subgroupCode;
	}
	
	foreach($imgsArr as $item)
	{
		if($item->No == $no)
			$retval[] = $item;
		else if(!empty($retval))
			break;
	}
	
	return $retval;
}
?>