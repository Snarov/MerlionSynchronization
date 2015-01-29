<?php
define("ENGLISH_ALPHABET_LOWER", "abcdefghijklmnopqrstuvwxyz");
define("ENGLISH_ALPHABET_UPPER",  "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
define("DIGITS", "0123456789");

function Strpbrkpos($string, $charList) //возвращает позицию первого вхождения символа из заданного набора символов
{
	$strLen = strlen($string);
	$charListLen = strlen($charList);
	for($i = 0; $i < $strLen; $i++)
	{
		for($j = 0; $j < $charListLen; $j++)
		{
			if($string[$i] === $charList[$j])
				return $i;
		}
	}
	return false;
}

function Strrpbrkpos($string, $charList)
{
	$strLen = strlen($string);
	$charListLen = strlen($charList);
	for($i = $strLen - 1; $i >= 0; $i--)
	{
		for($j = 0; $j < $charListLen; $j++)
		{
			if($string[$i] === $charList[$j])
				return $i;
		}
	}
	return false;
}

function MakeName($descr, $item)
{
	$prefixLen = Strpbrkpos($item->Name, ENGLISH_ALPHABET_LOWER . ENGLISH_ALPHABET_UPPER); //префикс как кириллическое начало имени
	$prefix = substr($item->Name, 0, $prefixLen);
	
	$postfix = substr($item->Name, Strrpbrkpos($item->Name, ENGLISH_ALPHABET-LOWER) + 1);
	$postfix = substr($postfix, strpos($postfix, " "));
	
	if(!empty($descr))
		for($i = TYPE; $i <= MODEL; $i++)
		{	
			$name .= (mb_strtolower(iconv('windows-1251', 'UTF-8', $descr[$i]->Value), 'UTF-8') == 'нет'? "" : ($descr[$i]->Value) . " ");
		}
	else
		$name = $item->Brand . $item->Vendor_part;
	
	if(substr_count($name, $prefix) === 0)
	{
		$name = $prefix . $name;
	}
	$name .= $postfix;
	return $name;
}