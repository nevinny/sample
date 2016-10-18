<?php

/**
 * Created by PhpStorm.
 * User: zyablik
 * Date: 13.10.16
 * Time: 17:54
 */
class Item extends Soldier
{
    public $dbList = [];
    public $itemList = [];
    public $updateList = [];
    public $createList = [];
    public $deleteList = [];

    public function store()
    {
//        print_r(__CLASS__);
//        kernel::pre();
        $manufacturers = Manufacturer::$storage;

        foreach ($this->itemList as $index => $item)
        {
            $mfName = trim(strip_tags($item['Manufacturer']));
            if (!array_key_exists($mfName,$manufacturers)) {
                kernel::pre($mfName);
            }
            $kod = $index.'='.$manufacturers[$mfName];
            if (array_key_exists($kod,$this->dbList)) {
                $this->updateList[$kod] = $item;
                unset($this->dbList[$kod]);
            } else {
                $this->createList[$kod] = $item;
                unset($this->dbList[$kod]);
            }
        }
        kernel::pre(count($this->createList));
        kernel::pre(array_shift($this->createList));
        kernel::pre(count($this->updateList));
        kernel::pre(array_shift($this->updateList));
        kernel::pre(count($this->dbList));
        kernel::pre(array_shift($this->dbList));

    }

    public function readXml()
    {
//        kernel::pre($this->xml->getList());
        foreach ($this->xml->getList() as $item) {
            $this->itemList[$item['Code1C']] = $item;
        }
//        kernel::pre($this->itemList);
        return $this->itemList;
    }

    public function getFromDB()
    {
        $itemSQL = $this->DB->query("SELECT * FROM {$this->wrConf->item_prefix}cat_item AS t");
        while($item = $this->DB->arr($itemSQL))
        {
            $keyProduct = $item['kod'].'='.$item['brand'];
            $this->dbList[$keyProduct] = $item;
        }
        return $this->dbList;
    }
}