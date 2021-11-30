<?php 
require_once("php/page.class.php");
$page = new Page();

$items = $page->getCart()->getItems();
var_dump($items);
?>