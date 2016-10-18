<?php

/**
 * Created by PhpStorm.
 * User: zyablik
 * Date: 13.10.16
 * Time: 17:54
 */
class Car extends Soldier
{
    public $carList = [];
    public $dbList = [];

    public function store()
    {
        if(sizeOf($this->carList) > 0)
        {
            $fm = new filtersModel();
            $fm->setParentID(18);
            $fm->setItem(7);
            $fm->is_virtual = '2';
            foreach($this->carList AS $k=>$ElementName)
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
        //$this->carList = [];
        foreach($this->xml->getList() AS $item)
        {
            if(array_key_exists('CarList',$item))
            {
                foreach($item['CarList'] AS $car)
                {
                    $car = trim($car);
                    if(!array_key_exists($car,$this->carList))
                    {
                        $this->carList[$car] = $car;
                    }
                }
            }
        }
        return $this->carList;
    }

    public function getFromDB()
    {
        $itemSQL = $this->DB->query("SELECT t.ElementID, t.ElementName FROM {$this->wrConf->item_prefix}term AS t WHERE ParentID = 18");
        while($item = $this->DB->arr($itemSQL))
        {
            $name = trim(strip_tags($item['ElementName']));
            $this->dbList[$name] = $item['ElementID'];
            $name = trim($name);
            if(array_key_exists($name,$this->carList))
            {
                unset($this->carList[$name]);
            }
        }
        return $this->dbList;
    }
}