<?php

foreach (glob(__DIR__ . '/*.php') as $helper) 
{
    if (basename($helper) === basename(__FILE__)) {
        continue;
    }
    require_once $helper;
}

?>