<?php

/**
 * Created by PhpStorm.
 * User: zyablik
 * Date: 13.10.16
 * Time: 17:55
 */
class Link extends Soldier
{
    public $part2CarLink = [];
    public function store()
    {
        print_r(__CLASS__);
    }

    public function readXml()
    {
        foreach($this->xml->getList() AS $item)
        {
            if(array_key_exists('CarList',$item) && count($item['CarList']) > 0)
            {
                $this->part2CarLink[$item['Code1C']] = $item['CarList'];
            }
        }
        return $this->part2CarLink;
    }
}