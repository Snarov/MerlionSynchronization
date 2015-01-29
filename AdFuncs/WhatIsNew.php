<?php
function WhatIsNew($items, $catCode)
{
	$retval = array();
    foreach($items as $item) //проверяем какие элементы не сущестуют у нас в
                             //инфоблоке
    {
		$checkElem = CIBlockElement::GetList(array("SORT" => "ASC"),
											array("IBLOCK_ID" => IBLOCK_ID,
												  "SECTION_CODE" => $catCode,
												  "CODE" => CODE_FIRST_LETTER . "_" . $item->No,
												 )
											);
		if(!$checkElem->GetNextElement())
		{
			$retval[] = $item;
		}
	}
return $retval;       
}
?>