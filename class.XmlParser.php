<?php

/**
 * Created by PhpStorm.
 * User: zyablik
 * Date: 13.10.16
 * Time: 17:09
 */
class XmlParser
{
    private $_list;
    public function __construct($file = '../zimport/export.XML')
    {
        global $WR, $wrConf;
        $this->DB = $WR->DB;
        $this->wrConf = $wrConf;
        $this->file = $file;
        $this->dom = new DOMDocument('1.0','windows-1251');
        $this->dom->load($this->file);
        $this->root = $this->dom->documentElement;
        $childrens = $this->root->childNodes;
        $this->_list = [];
        foreach($childrens AS $children)
        {
            $currentItem = [];
            $itemNodes = $children->childNodes;
            foreach($itemNodes AS $it)
            {
                switch($it->nodeName)
                {
                    case 'AnalogList':
                    case 'CarList':
                        $sublist = $it->childNodes;
                        foreach($sublist AS $subchild)
                        {
                            $currentItem[$it->nodeName][] = $subchild->nodeValue;
                        }
                        break;
                    case 'Weight':
                        $it->nodeValue = str_replace(",",".",$it->nodeValue);
                    default:
                        $currentItem[$it->nodeName] = $it->nodeValue;
                        break;
                }
            }
            //$item = $this->parseNode($children);
            //kernel::pre($currentItem);
            $this->_list[] = $currentItem;
            //die();
        }
    }

    public function getList()
    {
        return $this->_list;
    }
}