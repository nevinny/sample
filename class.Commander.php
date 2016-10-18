<?php

/**
 * Created by PhpStorm.
 * User: zyablik
 * Date: 13.10.16
 * Time: 17:30
 */
class Commander
{
    /** @var Soldier[] */
    private $squad;

    public function __construct($xml)
    {
        $this->planSquad($xml);
        $this->execute();
    }

    /**
     * Prepare the squad
     */
    private function planSquad($xml)
    {
        $this->squad = [
            new Manufacturer($xml),
//            new Car($xml),
            new Item($xml),
//            new Analog($xml),
//            new Link($xml),
        ];
//        kernel::pre($this->squad);
    }

    /**
     * "Talk with that link"
     */
    private function execute()
    {
        foreach ($this->squad as $soldier) {
//            $soldier->readXml();
            $soldier->store();
        }
    }
}