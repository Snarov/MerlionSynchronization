<?php
function FilterStuffs($items)
{
	global $blackList;
	$retval = array();
	
	foreach($items as $item)
		if(!(empty($item->No) || $blackList->exists($item->No)))
			$retval[] = $item;
	return $retval;
}
?>

