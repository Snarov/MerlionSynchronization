<?php
function GetEarliestShipmentDate($ID)		//возвращает ближайшую дату с ненулевой отгрузкой
{
	global $client;
	
	$shipmentDates = $client->getShipmentDates();
	$retval = $shipmentDates->item[1];
	foreach($shipmentDates->item as $shipmentDate)//поиск ближайшей даты
	{
		$avail = $client->getItemsAvail("",
									  iconv('UTF-8', 'windows-1251', SHIPMENT),
									  $shipmentDate->Date,
									  "0",
									  $ID
									 );
		if($avail->item[0]->AvailableClient > 0)
		{
			$retval = $shipmentDate;
			break;
		}
	}
	return $retval;
}
?>