<?php

function ObjInArray($needle, $haystack)
{
	foreach($haystack as $elem)
		if($needle == $elem)
			return true;
	return false;
}