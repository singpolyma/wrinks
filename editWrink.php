<?php

if(!$_REQUEST['id'])
   die('<b>ERROR : No ID given</b>');

$item = XN_Content::load(intval($_REQUEST['id']));
if(isset($_REQUEST['title']))
   $item->title = $_REQUEST['title'];
if(isset($_REQUEST['description']))
   $item->description = $_REQUEST['description'];
if(isset($_REQUEST['sites']) && is_array($_REQUEST['sites'])) {
   asort($_REQUEST['sites']);
   $item->my->sites = implode(' ',array_keys($_REQUEST['sites']));
}//end if isset sites
$item->save();

header('Content-Type: text/plain;');
header('Location: http://'.$_SERVER['HTTP_HOST'].'/wrink.php?id='.$_REQUEST['id'],TRUE,303);

?>