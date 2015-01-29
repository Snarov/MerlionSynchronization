<?php

define('PROPS_COUNT', 200);		//количество свойствдля анализа

function ScanGroupProps($client, $group)
{
	global $GENERAL_FIELDS;
	$retval = array();
	if(preg_match(SG_REGEX, $group->code) == 1)
	{
		$props = $client->getItemsProperties($group->code, "", 1, PROPS_COUNT);
		foreach($props->item as $prop)
		{
			if(!empty($prop->PropertyName) && !in_array($prop->PropertyName, $retval) &&
														!in_array(iconv("windows-1251", "UTF-8", $prop->PropertyName),
														$GENERAL_FIELDS))
				$retval[] = $prop->PropertyName;
		}
	}
	return $retval;
}