<?php
require_once 'SyncClasses.php';

function CatDifference(array $cat1, array $cat2) //находит элементы каталога 1
                                                //которых нету в каталоге 2
{
	$tmpArr = array();
		
    foreach($cat2 as $elem)
        $tmpArr[] = new SyncElem($elem->code, $elem->description);
    foreach($cat1 as $elem)
		if(!in_array(new SyncElem($elem->code, $elem->description), $tmpArr))
            $retval[] = $elem;
	return $retval;
}