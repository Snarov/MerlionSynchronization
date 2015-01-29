<?php

define("SG_REGEX", "#([A-Z|А-Я]){1,2}([0-9]){5,}#"); //регулярка, задающая ID подгрупп

function GetSubgroupList($groupList)
{
    foreach($groupList as $group)
        if(preg_match(SG_REGEX, $group->ID) == 1)
            $retval[] = $group;
    return $retval;    
}
?>
