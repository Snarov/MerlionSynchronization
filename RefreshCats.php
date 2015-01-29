<?php
require_once 'SyncClasses.php';
require_once 'AdFuncs/CatDifference.php';
require_once 'AdFuncs/GetSectionIdByCode.php';
require_once 'AdFuncs/ScanGroupProps.php';

function RefreshCats()
{
    global $errMsg;
    global $DB;
	global $client;
	
    $errState = false;
	
	$merCat = array();
	$BXCat = array();
    
    $MerCatalogList = $client->getCatalog();  
    //?????? ???? ???? ??????? ???????? ????????
    foreach($MerCatalogList->item as $item)
        $merCat[] = new MerSyncElem(
                                $item->ID_PARENT,
                                $item->ID,
                                $item->Description
                                );
    
    $BXCatalogList = CIBlockSection::GetList();
    while($elem = $BXCatalogList->GetNextElement())
    {
       $elemFields = $elem->GetFields();
       $BXCat[] = new BXSyncElem(
                                $elemFields['ID'],
                                $elemFields['CODE'],
                                $elemFields['NAME']
                                );
	   $BXCat[count($BXCat) - 1]->NameUnquote();
    }
    
    //$DB->StartTransaction();
    
    if($diff = CatDifference($BXCat, $merCat))//??????? ? ????? ????????
                                           //??? ?? ???????????? ???????
                                           //? ??????? ?? ? ?? ????????
    {
		foreach($diff as $delElem)
		{
			$sectionProps = CIBlockSectionPropertyLink::GetArray(IBLOCK_ID, $delElem->ID);
			foreach($sectionProps as $sectionProp)
			{
				if($sectionProp["INHERITED"] == "N")			//???? ???????? ?? ???????????
				{
					if(!CIBlockProperty::Delete($sectionProp['PROPERTY_ID']))
					{
						$errState = true;
						break;
					}
				}
			}
            if(!CIBlockSection::Delete($delElem->ID))
            {
                $errState = true;
                break;
            }
		}
    }
	
	$ibs = new CIBlockSection;
	$ibp = new CIBlockProperty;
	$pLink = new CIBlockSectionPropertyLink;
    
    if($diff = CatDifference($merCat, $BXCat)) //??????? ??? ?? ???????????
                                               //???????? ?? ????????
                                               //? ????????? ?? ? ?? ???????? ? ???
    {
        foreach($diff as $addElem)
        {
			if($addElem->parentCode != MER_TOP_CAT_ID)
			{
				$parentFields = CIBlockSection::GetList(
													array("SORT" => "ASC"),
													array('CODE' => $addElem->parentCode)
												 )->getNextElement();
				if($parentFields != false)
				{
					$parentID = $parentFields->GetFields()['ID'];
					$active = $parentFields->GetFields()['ACTIVE'];
				}
				else
				{
					$diff[] = $addElem;
					continue;
				}
			}
			else
			{
				$parentID = 0;
				$active = 'N';
			}
            if(!$lastAddedSectionID = $ibs->Add(array
                                    (  
                                    'IBLOCK_ID' => IBLOCK_ID,
                                    'IBLOCK_SECTION_ID' => $parentID,
                                    'NAME' => $addElem->description,
                                    'CODE' => $addElem->code,
                                    'ACTIVE' => $active
                                    )
                                   )
              )
            {  
                $errState = true;
                break;
            }
			
			$props = ScanGroupProps($client, $addElem);
			foreach($props as $prop)
			{
				$code = substr(CUtil::translit($prop,
                                      "ru",
                                      array("change_case" => "U")
								),
								0,
								PROP_CODE_MAX_LEN
							);
							  
				$code .= "_{$lastAddedSectionID}";
				$arFields = array(
								  'CODE' => $code,
								  'XML_ID' => $code,
								  'IBLOCK_ID' => IBLOCK_ID,
								  'NAME' => $prop
								 );
				if($prID = $ibp->Add($arFields))
				{
					$pLink->Add($lastAddedSectionID, $prID);
					$pLink->Delete(0, $prID);
				}
			}
        }
    }
    
    if($errState === true)
    {
        $errMsg .= "Ошибка обновления структуры каталога\n";
        //$DB->Rollback();
    }
    else
	{
        //$DB->Commit();
		return "Обновление структуры каталога прошло успешно\n";
	}
}
?>
