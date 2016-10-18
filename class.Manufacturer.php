<?php

/**
 * Created by PhpStorm.
 * User: zyablik
 * Date: 13.10.16
 * Time: 17:53
 */
class Manufacturer extends Soldier
{
    public $manufacturerList = [];
    public $dbList = [];
    public static $storage;
    public function store()
    {
        if(sizeOf($this->manufacturerList) > 0)
        {
            $fm = new filtersModel();
            $fm->setParentID(19);
            $fm->setItem(7);
            $fm->is_virtual = '2';
            foreach($this->manufacturerList AS $k=>$ElementName)
            {
                $params = [];
                $fm->setParams($params);
                $fm->setElementName($ElementName);
                $results = $fm->save();
                $this->dbList[$ElementName] = $results['ElementID'];
            }
        }

    }

    public function readXml()
    {
        foreach($this->xml->getList() AS $item)
        {
            if(!array_key_exists($item['Manufacturer'],$this->manufacturerList))
            {
                $name = trim(strip_tags($item['Manufacturer']));
                $this->manufacturerList[$name] = $name;
            }
        }
//        sort($this->manufacturerList);
        return $this->manufacturerList;
    }

    public function getFromDB()
    {
        $itemSQL = $this->DB->query("SELECT t.ElementID, t.ElementName FROM {$this->wrConf->item_prefix}term AS t WHERE ParentID = 19");
        while($item = $this->DB->arr($itemSQL))
        {
            $name = trim(strip_tags($item['ElementName']));
            $this->dbList[$name] = $item['ElementID'];
            if (array_key_exists($name,$this->manufacturerList)) {
                unset($this->manufacturerList[$name]);
            }
        }
        self::$storage = $this->dbList;
        return $this->dbList;
    }


    public static function getDbList()
    {
        return self::$storage;
    }
}