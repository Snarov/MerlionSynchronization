<?php

require_once 'AdFuncs/GetSectionIdByCode.php';
require_once 'AdFuncs/GetOrderTime.php';
require_once 'AdFuncs/MakeName.php';

function AddToIB($items, $avail, $descr, $imgs)
{
    global $errMsg;
    global $DB;
	global $GENERAL_FIELDS;
	global $GENERAL_EXCEPTION_FIELDS;
	global $blackList;
	
    static $ib;
    static $elemPrice;
	static $cat;
	
	if(empty($ib) || empty($price) || empty($cat))
	{
		$ib = new CIBlockElement();
		$elemPrice = new CPrice();
		$cat = new CCatalogProduct();
	}
    
    $added = 0;
    $itemsCount = count($items);
	
    for($i = 0; $i < $itemsCount; $i++)
    {
		//цена
        $price = CCurrencyRates::ConvertCurrency($avail[$i]->PriceClient,
                                                "USD",
                                                "BYR"
                                                );
        //брэнд
        $brand = $items[$i]->Brand;
        //модель
        $model = empty($descr[$i][MODEL]->Value) ? $items[$i]->Vendor_part : $descr[$i][MODEL]->Value;
        //имя
		$name = MakeName($descr[$i], $items[$i]);
		//количество
		$count = $avail[$i]->AvailableClient;
		//срок выполнения заказа
		if($count === 0)
			$orderTime = GetOrderTime($avail[$i]);
		else if(empty($count))
			$orderTime = -1;
		else
			$orderTime = 0;
		
		if(!(empty($name) || empty($price)))
		{
			//код товара
			$elemCode = CODE_FIRST_LETTER . '_' . $items[$i]->No;
			//Партномер товара
			$vendorPart = $items[$i]->Vendor_part;
			//гарантия
			$warranty = $items[$i]->Warranty;
			//ID раздела
			$sectionID = GetSectionIdByCode($items[$i]->GroupCode3);
			//тип товара
			$type = $descr[$i][TYPE]->Value;
			//серия
			$series = $descr[$i][SERIES];
			//количество
			$count = $avail[$i]->AvailableClient;
			//вес(грамм)
			$weight = $items[$i]->Weight * 1000;
			//фотки
			$photos = array();
			foreach($imgs[$i] as $photo)
				$photos[] = CFile::MakeFileArray(MER_IMG_URL . $photo->FileName);
			$generalPropsAr = array(				//транслитерация названий полей навеяна Мерлионом
										'BREND' => $brand,
										'TIP' => $type,
										'SERIYA' => $series,
										'MODEL' => $model,
										'PATRNUMBER_ARTIKUL_PROIZVODITELYA' => $vendorPart,
										'MORE_PHOTO' => $photos,
										'WARRANTY' => $warranty,
										'ORDER_TIME' => $orderTime
										);
			$MerPropsAr = array();
			foreach($descr[$i] as $property)
			{   
				if(!in_array(iconv("windows-1251", "UTF-8", $property->PropertyName), $GENERAL_EXCEPTION_FIELDS))
				{
				$newKey = substr(CUtil::translit($property->PropertyName,
												"ru",
												array("change_case" => "U")
												),
												0,
												PROP_CODE_MAX_LEN
							);		
				if(!in_array(iconv("windows-1251", "UTF-8", $property->PropertyName), $GENERAL_FIELDS))
					$newKey .= '_' . $sectionID;
				$MerPropsAr[$newKey] = $property->Value;
				}
			}
			$propsAr = array_merge($generalPropsAr, $MerPropsAr);
			$arElem = array(                    
				'CODE' => $elemCode,
				'XML_ID' => $$elemCode,
				'NAME' => $name,
				'IBLOCK_SECTION_ID' => $sectionID,
				'IBLOCK_ID' => IBLOCK_ID,
				'ACTIVE' => 'Y',
				'SORT' => 500,
				'PREVIEW_PICTURE' => $photos[0],
			   // 'PREVIEW_TEXT' => $elem['descr'], думаем
				'PREVIEW_TEXT_TYPE' => 'html',
				'DETAIL_PICTURE' => $photos[1],
				//'DETAIL_TEXT' => $elem['text'], думаем
				'DETAIL_TEXT_TYPE' => 'html',
				'TIMESTAMP_X' => ConvertTimeStamp(),
				'PROPERTY_VALUES' => $propsAr
			);
			//добавление товара вместе с ценой
			$DB->StartTransaction();
			if($elid = $ib->Add($arElem, false, false))
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
									'QUANTITY' => $count,
									'PURCHASING_PRICE' => $price,
									'PURCHASING_CURRENCY' => "BYR",
									'WEIGHT' => $weight
								   );
			}
			 if($cat->Add($elemParams) && $elemPrice->Add($priceElem, true))
			 {
				 ++$added;
				 $DB->Commit();
			 }
			else
			{
				$errMsg .= "ошибка добавления $name: {$ib->LAST_ERROR}\n";
				$DB->Rollback();
			}
		}
		else
			$blackList->add($items[$i]->No);
    }
    return $added;
}
?>