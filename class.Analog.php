<?php

/**
 * Created by PhpStorm.
 * User: zyablik
 * Date: 13.10.16
 * Time: 17:54
 */
class Analog extends Soldier
{
    public $AnalogList = [];
    public function store()
    {
        print_r(__CLASS__);
    }

    public function readXml()
    {
        foreach($this->xml->getList() AS $item)
        {
            if(array_key_exists('AnalogList',$item) && count($item['AnalogList']) > 0)
            {
                $this->AnalogList[$item['Code1C']] = $item['AnalogList'];
            }
        }
        //kernel::pre($this->AnalogList);
        return $this->AnalogList;
    }
}