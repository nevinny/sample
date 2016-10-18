<?php
/**
 * Created by PhpStorm.
 * User: zyablik
 * Date: 13.10.16
 * Time: 17:06
 */
include_once '../_common/setup.php';

$xml = new XmlParser();
//kernel::pre($xml->getList());

$cmd = new Commander($xml);