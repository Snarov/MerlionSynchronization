<?php
require_once 'StuffsBlackList.php';

$blackList= new StuffsBlackList();

require_once 'AdFuncs/AddToIB.php';
require_once 'AdFuncs/GetSubgroupList.php';
require_once 'AdFuncs/GetEarliestShipmentDate.php';
require_once 'AdFuncs/WhatIsNew.php';
require_once 'AdFuncs/StuffDifference.php';
require_once 'AdFuncs/FilterStuffs.php';
require_once 'AdFuncs/GetDescrByNo.php';
require_once 'AdFuncs/GetImgsByNo.php';
require_once 'AdFuncs/GetAvailsByNo.php';

echo ini_get('memory_limit');

function RefreshStuffs()
{
	global $client;
	
    $added = 0;
	$updated = 0;
	    
    $MerSubgroupsList = GetSubgroupList($client->
                                        getCatalog()->
                                        item);
      
    foreach($MerSubgroupsList as $subgroup)
	{
		$i = 1;
        while(true)
        {  
			$requestRes = $client->getItems($subgroup->ID, false, false, $i++, ROWS_ON_PAGE);
            if(!empty($items = FilterStuffs($requestRes->item)))  //если на странице есть записи
            {
				$newStuffs = WhatIsNew($items, $subgroup->ID);
				if(!empty($newStuffs))
				{
					$avail = array();
					$descr = array();
					$imgs = array();
					foreach($newStuffs as $newStuff) //для каждого товара находим:
					 //Цену и количество, описание и картинки.
					{
						//Цена и количество
						//$shipmentDate = GetEarliestShipmentDate($newStuff->No); очень дорогая по времени
						$avail[] = GetAvailsByNo($newStuff->No, $subgroup->ID);

						//Описание
						$descr[] = GetDescrByNo($newStuff->No, $subgroup->ID);

						//изображения
						$imgs[] =  GetImgsByNo($newStuff->No, $subgroup->ID);
					}
					$added += AddToIB($newStuffs, $avail, $descr, $imgs);
				}
//				$toUpdateStuffs = StuffDifference($items, $newStuffs);
//				if(!empty($toUpdateStuffs))
//				{
//					foreach($toUpdateStuffs as $toUpdateStuff)
//					{
//						$shipmentDate = GetEarliestShipmentDate($client, $toUpdateStuff->ID);
//						$avail[] = $client->GetItemsAvail("",
//														  SHIPMENT,
//														  $shipmentDate,
//														  "0",
//														  $toUpdateStuff->ID
//														 );
//					}
//					$updated += UpdateIB($toUpdateStuffs, $avail);
//				}
            }
            else
                break;
       }
	}
	return "added: $added; updated: $updated";
}
?>