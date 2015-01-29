<?php

ini_set('display_errors', '1');
set_include_path(get_include_path() . PATH_SEPARATOR . "/opt/lampp/htdocs/dev/MerlionSync");

//пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅ RPC
define('WSDL_URL', 'https://api.merlion.com/rl/mlservice2?wsdl');
define("IBLOCK_ID", 2);
define("SHIPMENT", "ДОСТАВКА");
define('ROWS_ON_PAGE', 2000); //пїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅ пїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ (пїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅ, пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅ)
define("MER_IMG_URL", "http://img.merlion.ru/items/"); //пїЅпїЅпїЅ пїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ
define("CODE_FIRST_LETTER", "M"); // РїРµСЂРІР°СЏ Р±СѓРєРІР° СЃРёРјРІРѕР»СЊРЅРѕРіРѕ РєРѕРґР° С‚РѕРІР°СЂРѕРІ РјРµСЂР»РёРѕРЅР°
define("MER_TOP_CAT_ID", "Order");
define("EARLIEST_DATE_INDEX", 1);
define('PROP_CODE_MAX_LEN', 24);

//пїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅ
define("GROUP_NAME", 0);
define("TYPE", 1);
define("BRAND", 2);
define("SERIES", 3);
define("MODEL", 4);

$GENERAL_FIELDS = array("Вес","Бренд", "Брэнд", "Сайт производителя", "Размеры", "PatrNumber/Артикул Производителя",
						"Тип", "Серия", "Модель", "Назначение");
$GENERAL_EXCEPTION_FIELDS = array ("Вес","Бренд", "Брэнд", "PatrNumber/Артикул Производителя", "Тип", "Серия", "Модель",
								);

//СЃРІСЏР·Р°РЅРЅС‹Рµ С„Р°Р№Р»С‹

define("SYNC_ERROR_LOG", "err.log");
define("SYNC_OUTPUT", "out.log");
$errMsg = "";

require_once 'RefreshStuffs.php';
require_once 'RefreshCats.php';

//подключение  BITRIXAPI
$root = $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../../../../') . "/lampp/htdocs/dev/bx/www";
define('LANG', 'ru');
define("NO_KEEP_STATISTIC", true);
 
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
//необходимые модули
CModule::IncludeModule('main');
CModule::IncludeModule('sale');
CModule::IncludeModule('iblock');
CModule::IncludeModule('currency');
CModule::IncludeModule('catalog');

     
$logParams = array
                   (                         //пїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ
                        'login' => "TC0030622|STALIN14-88",
                        'password' => "STALIN14-88",
                        'encoding' => "Windows-1251",
                        'features' => SOAP_SINGLE_ELEMENT_ARRAYS
                   );

try
{
    $client = new SoapClient(WSDL_URL, $logParams); //пїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅ WDSL
	$resultState = RefreshCats();       //пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ
    $resultState .= RefreshStuffs();      //пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅ
	
	if(CModule::IncludeModule("search")) // РїРµСЂРµРёРЅРґРµРєСЃР°С†РёСЏ РїРѕСЃР»Рµ РёР·РјРµРЅРµРЅРёР№
		CSearch::ReIndexAll();
	//РЎРѕР·РґР°РЅРёРµ РѕС‚С‡РµС‚Р°
	if(!empty($errMsg))
		file_put_contents(SYNC_ERROR_LOG, "\n\n" . date(DATE_RSS) . ":\n $errMSg", FILE_APPEND);
	file_put_contents(SYNC_OUTPUT, "\n\n" . date(DATE_RSS) . ":\n Result: $resultState", FILE_APPEND);
	
}
catch(SoapFault $exc)   //пїЅ пїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅ SOAP пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅ
{
    $errMsg = 'SOAP fault:' . $exc->getMessage() . '(' . $exc->getCode() . " " . $exc->getFile() . ')' .
            'on line' . $exc->getLine();
    error_log($errMsg);
    file_put_contents(SYNC_ERROR_LOG, "\n\n" . date(DATE_RSS) . ":\n $errMsg", FILE_APPEND);
}

?>