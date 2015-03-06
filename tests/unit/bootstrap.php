<?php
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR
    . dirname(__FILE__) . '/../../app' . PATH_SEPARATOR . dirname(__FILE__));
ini_set('memory_limit', '512M');
require_once 'Mage.php';

Mage::app('default');
session_start();
