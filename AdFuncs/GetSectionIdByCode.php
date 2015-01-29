<?php
function GetSectionIdByCode($code)
{
    static $prevCode = "";
	$retval = false;
    if($prevCode == $code)
		$retval = $prevCode;
    else
    {
	$fields = CIBlockSection::GetList(
                                                array("SORT" => "ASC"),
                                                array('CODE' => $code)
									 )->getNextElement();
	if($fields)
		$retval =  $fields->GetFields()["ID"];
	}
	return $retval;	
}
