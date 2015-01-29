<?php

require_once 'AdFuncs/GetElemIdByCode.php';
require_once 'AdFuncs/GetOrderTime.php';

function UpdateIB($items, $avail)
{
	global $errMsg;
	global $DB;
	
	$ib = new CIBlockElement();
	$price = new CPrice();
    $cat = new CCatalogProduct();
	
	$updated = 0;
	$itemsCount = count($items);
	 
	for($i = 0; $i < $itemsCount; $i++)
	{
		//id элемента инфоблока
		$elid = GetElemIdByCode($items[$i]->ID);
		//новое количество 
		$newCount = $avail[$i]->item[0]->AvailableClient;
		//новая цена
		$price = CCurrencyRates::ConvertCurrency($avail[$i]->item[0]->PriceClient,
                                                "USD",
                                                "BYR"
                                                );
		if($newCount == 0)
			$orderTime = GetOrderTime($avail[$i]);
		else
			$orderTime = -1;
		
		 $arElem = array(
						'TIMESTAMP_X' => ConvertTimeStamp(),
						'PROPERTY_VALUES' => array('ORDER_TIME' => $orderTime)
						);
		 
		$DB->StartTransaction();
		if($ib->Update($elid, $arElem, false, false))
		{
			 $priceElem = array(
                               'PRODUCT_ID' => $elid,
                               'CATALOG_GROUP_ID' => 1,
                               'PRICE' => $price,
                               'CURRENCY' => 'BYR'
                              );

            $elemParams = array(
                                'ID' => $elid,
                                'VAT_INCLUDED' => "N",
                                'QUANTITY' => $newCount,
								'PURCHASING_PRICE' => $price,
								'PURCHASING_CURRENCY' => "BYR",
								);
		}
		 if($cat->Update($elid, $elemParams) && $price->Update($priceElem, true))
         {
             ++$updated;
             $DB->Commit();
         }
         else
         {
             $errMsg .= "ошибка обновления $elid: {$ib->LAST_ERROR}\n";
             $DB->Rollback();
         }
	}
	return $updated;
}

?>
