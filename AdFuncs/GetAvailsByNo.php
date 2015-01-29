<?php
function GetAvailsByNo($no, $subgroupCode)
{
	global $client;
	
	static $availsArr;
	static $prevSubgroupCode = "";
	static $shipmentDate;
	
	if(empty($shipmentDate))
		$shipmentDate = $client->getShipmentDates()->item[EARLIEST_DATE_INDEX];
		
	$retval = array();
	
	if($subgroupCode != $prevSubgroupCode)
	{
		$availsArr = $client->getItemsAvail($subgroupCode,
											iconv("UTF-8","windows-1251", SHIPMENT),
											$shipmentDate->Date,
											'0');
		$prevSubgroupCode = $subgroupCode;
	}
	
	foreach($availsArr->item as $item)
		if($item->No == $no)
		{
			$retval = $item;
			break;
		}
	return $retval;
}