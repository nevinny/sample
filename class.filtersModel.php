<?php
//include_once './_common/setup.php';
//namespace wrmain;


/*
*		TODO: 
*		1. PHPDoc
*		2. PHPUnit
*/
class filtersModel {
	
	protected $_tblName;
	
	private $ElementID;
	private $_parentRecord;
	private $_params;
	private $_pathPrefix;
	private $_item;
	private $_isNode;
	private $_validFields;
	private $ItemID;
	private $ItemName;
	private $ParentID;
	private $isNode;
	private $ElementPath;
	private $ShortName;

    /**
     * @var STORAGE_MYSQLi
     */
    private $DB;
	
	public $ElementScript;
	public $ElementName;
	public $ElementOrder;
	public $is_virtual;
	public $HostName;
	public $owner;
	public $grown;
	public $rights;
	public $DEBUG = false;
	
	public function __construct()
	{
		// dependency injection
		global $wrConf,$WR;
		$this->wrConf = $wrConf;
		$this->DB = $WR->DB;
		
		$this->_tblName = $this->wrConf->prefix."main";
		
		$this->_validFields = [];
		$this->ParentID = 0;
		$this->is_virtual = 0;
		$this->ElementOrder = 0;
		$this->HostName = "*";
		$this->owner = "0";
		$this->grown = "1";
		$this->rights = "770";
	}
	
	private function link() {
		$query = "INSERT INTO {$this->_tblName} (
				ParentID,
				ElementName,
				ItemID,
				ItemName,
				isNode,
				ElementOrder,
				ElementPath,
				ElementScript,
				ShortName,
				is_virtual,
				HostName
			) VALUES (
				'".$this->ParentID."',
				'".$this->ElementName."',
				'".$this->ItemID."',
				'".$this->ItemName."',
				'".$this->_isNode."',
				'".$this->ElementOrder."',
				'".$this->ElementPath."',
				'".$this->ElementScript."',
				'".$this->ShortName."',
				'".$this->is_virtual."',
				'".$this->HostName."'
			);";
			try {
				if(!$this->DB->query($query))
				{
					throw new Exception($this->DB->lastQuery);	
				}
			} catch (Exception $e) {
				return false;
			}
			$this->ElementID = $this->DB->last_insert_id();
			return $this->ElementID;
	}
	
	public  function save() {
		try {
			$this->DB->beginTransaction();
			
			$id = $this->link();
			if(!$id)
			{
				throw new Exception($this->DB->lastQuery);	
			}
			
			$this->_params['ElementID'] = $id;
			$this->_params['ElementName'] = $this->ElementName;
			$this->_params['ParentID'] = $this->ParentID;
			$this->_params['ElementOrder'] = $this->ElementOrder;
			$this->_params['created'] = time();
			$this->_params['modified'] = time();
			$this->_params['owner'] = $this->owner;
			$this->_params['grown'] = $this->grown;
			$this->_params['rights'] = $this->rights;
			
			$query = "INSERT INTO ".$this->ItemName." (" . implode(",",array_keys($this->_params)) .  ") VALUES ('" . implode("','",array_values($this->_params)) . "');";
//            kernel::pre($query);
			if(!$this->DB->query($query))
            {
                throw new Exception($this->DB->lastQuery);
            }
			$this->DB->commit();
		} catch (Exception $e) {
			$this->DB->rollback();
			return $e->getMessage();
		}
		$result = array();
		$result['ElementID'] = $id;
		$result['ElementPath'] = $this->ElementPath;
		$result['ShortName'] = $this->ShortName;
		return $result;
	}
	
	
	
	public function setElementName($name)
	{
		$this->ElementName = $name;
	}
	
	public function setParentID($id = 0)
	{
		if($id > 0) {
			$this->_parentRecord = kernel::getElementByID($id,"main");
			$this->ParentID = $id;
		} else {
			$this->_parentRecord = array('ElementPath' => '/', 'HostName' => $this->HostName);
		}
		$this->_pathPrefix = $this->_parentRecord['ElementPath'];
		//$this->ShortName = $this->
		$this->setPath();
	}
	
	public function setItem($item = 0)
	{
		if(is_int($item))
		{
			$this->_item = items::getByID($item);
		} else {
			$this->_item = items::getByName($item);
		}
		$this->ItemID = $this->_item['ElementID'];
		$this->ItemName = $this->_item['itemname'];
		
		if(empty($this->_isNode))
		{
			$this->_isNode = $this->_item['isnode'];
			$this->isNode = ($this->_isNode == '1' ? true : false);
		}
		if(empty($this->ElementScript))
			$this->ElementScript = $this->_item['webscript'];
		
		$this->setItemFields();
		$this->setPath();
	}
	
	private function setItemFields()
	{
		$this->_validFields = items::getParams($this->ItemName);
		return $this->_validFields;
	}
	
	public function setNodeType($type = false)
	{
		$this->isNode = $type;
		$cType = $type ? '1' : '2';
		if($this->_isNode != $cType)
		{
			$this->_isNode = $cType;
			$this->setPath();
		}
		return $this->isNode ;
	}
	public function setPath($prefix = "", $path = "")
	{
		if(empty($path)) {
			if(empty($this->ElementPath) && $this->_pathPrefix)
				$this->ElementPath = $this->_pathPrefix . $this->setShortName() . ($this->isNode ? '/' : '');
			
		} else {
			$this->setShortName($path);
			$this->ElementPath =$prefix . $path;
		}
	}
	
	public function setShortName($name = "")
	{
		if(empty($name))
			$this->ShortName = $this->generateUniqueID();
		 else {
		 	$this->ShortName = $name;
			$this->ElementPath = $this->_pathPrefix . $name;
		 }
		 return $this->ShortName;
	}
	
	private function generateUniqueID() {
		return date("YmdHis").'-'.mt_rand(1000,10000).($this->isNode ? '':'.html');
	}
	
	public function setParams(array $params = [])
	{
		/*
		  *		TODO: сделать валидатор полей. подключить класс объекта, или получить из базы список полей, и удалить лишние параметры.
		  */
		try {
			if(count($this->_validFields) == 0)
				throw new Exception("Item Fields is Empty, do setItem() first");
			
			foreach($params AS $k=>$v)
			{
				if(substr($k,0,3) == '___')
				{
					$cKey = substr($k,3);
					if(array_key_exists($cKey,$this->_validFields))
						$this->_params[$cKey] = $v;
				}
			}
		} catch (Exception $e)
		{
			kernel::pre($e->getMessage()."<br>Line #".$e->getLine());
		}
	}
	
	public function update()
	{
		
	}
	
	public function __destruct() {
		//kernel::pre(__METHOD__);
	}
}

/*
$params = [];
$params['___metatitle'] = "meta title";
$params['___metakeywords'] = "meta keywords";
$params['___metadescription'] = "meta description";
$params['___descr'] = "Описание текстового раздела";
$params['___pic'] = "super picture";
$fm = new filtersModel();
$fm->setElementName("Новый раздел сайта");
$fm->setItem('section');
$fm->setParentID(1);
$fm->is_virtual = '2';
$fm->setParams($params);
*/