<?php

define("DELIMITER", "-");
define("DATE_EXAMPLE", "2014-01-01");

function GetOrderTime($itemAvail)
{
	$currentDate = substr( date(DATE_ATOM), 0, strlen(DATE_EXAMPLE));
	$today = explode(DELIMITER, $currentDate);
	$expectedDay = explode(DELIMITER, $itemAvail->DateExpectedNext);
	
	$differenceSecs = mktime(0, 0, 0, $expectedDay[1], $expectedDay[2], $expectedDay[0]) - 
					mktime(0, 0, 0, $today[1], $today[2], $today[0]);
	
	$differenceDays = $differenceSecs / (60 * 60 * 24);
	if($differenceDays > 0)
		$retval = $differenceDays;
	else
		$retval = -1;
	return $retval;
}
?>

