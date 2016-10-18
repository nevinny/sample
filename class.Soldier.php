<?php

/**
 * Created by PhpStorm.
 * User: zyablik
 * Date: 13.10.16
 * Time: 17:39
 */
abstract class Soldier
{
    protected $xml;

    /**
     * @var STORAGE_MYSQLi;
     */
    protected $DB;

    public function __construct(XmlParser $xml)
    {
//        kernel::pre($this);
        global $WR, $wrConf;
        $this->DB = $WR->DB;
        $this->wrConf = $wrConf;
        $this->xml = $xml;
        $this->readXml();
        $this->getFromDB();
    }

    abstract public function store();
    abstract public function readXml();
    abstract public function getFromDB();
}