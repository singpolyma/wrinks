<?php

if(!$_REQUEST['wrinkid'])
   die('<b>ERROR : No Wrink specified</b>');

$wrink = XN_Content::load(intval($_REQUEST['wrinkid']));
$wrink->my->traffic += 1;
if($wrink->my->traffictime < (time() - 604800)) {
   $wrink->my->traffic = 0;
   $wrink->my->traffictime = time();
}//end if traffictime over
$wrink->save();
$sites = explode(' ',$wrink->my->sites);
if(!$_REQUEST['siteid']) {
   $_REQUEST['siteid'] = XN_Query::create('Content')
         ->filter('owner','=')
         ->filter('type','eic','Website')
         ->filter('my.url','=',$_SERVER['HTTP_REFERER']);
   $_REQUEST['siteid'] = $_REQUEST['siteid']->execute();
   $_REQUEST['siteid'] = $_REQUEST['siteid'][0]->id;
}//end if ! siteid
$key = array_search($_REQUEST['siteid'],$sites);

$_REQUEST['move'] = $_REQUEST['move'] ? $_REQUEST['move'] : 1;

if($_REQUEST['move'] == 'random') {
   $key = 0;
   shuffle($sites);
} else {
   $key += $_REQUEST['move'];
   if($key > count($sites))
      $key -= count($sites);
   if($key < 0)
      $key += count($sites);
}//end if-else random

$site = XN_Content::load(intval($sites[$key]));
header('Content-Type: text/plain;');
header('Location: '.$site->my->url,TRUE,303);

?>