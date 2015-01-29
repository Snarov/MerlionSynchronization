<?php

function GetElemIdByCode($code)
{
	$fields = CIBlockElement::GetList(
							array("SORT" => "ASC"),
							array('CODE' => $code)
							)->getNextElement()->GetFields();
	return $fields["ID"];
}

?>