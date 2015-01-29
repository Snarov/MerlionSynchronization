<?php
class SyncElem
{ 
   public $code;        //код раздела
   public $description; //название раздела
   
   public function __construct($code, $description)
   {
       $this->code = $code;
       $this->description = $description;
   }
   
   public function NameUnquote()
   {
	   $search = array("&quot;");
	   $replace = array('"');
	   $this->description = str_replace($search, $replace, $this->description);
   }
}

class BXSyncElem extends SyncElem
{
    public $ID;             //ID раздела
    
    public function __construct($ID, $code, $description)
    {
        parent::__construct($code, $description);
        $this->ID = $ID;
    }
}

class MerSyncElem extends SyncElem
{
    public $parentCode;      //код родительского раздела
    
    public function __construct($parentCode, $code, $description)
    {
        parent::__construct($code, $description);
        $this->parentCode = $parentCode;
    }
}
