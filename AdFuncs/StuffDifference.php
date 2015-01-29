<?php
function StuffDifference(array $arr1,array $arr2)
{
	$retval = array();
	foreach($arr1 as $elem)
		if(!in_array($elem, $arr2))
			$retval[] = $elem;
	return $retval;
}
?>