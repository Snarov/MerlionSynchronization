<?php

//хранит список товаров, не подлежащих добавлению в наш каталог по каким-либо причинам
class StuffsBlackList
{
	private $list = "";
	const FILE_NAME = './StuffBlackList';
		
	public function __construct()
	{
		$this->list = file_get_contents(self::FILE_NAME);
	}

	public function exists($stuffNum)
	{
		return (strpos($this->list, $stuffNum) !== false);
	}
	
	public function add($stuffNum)
	{
		$result = file_put_contents(self::FILE_NAME, "{$stuffNum};\n", FILE_APPEND);
		$this->list .= "{$stuffNum};\n";
	}
}
