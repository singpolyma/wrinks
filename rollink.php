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

header('Content-Type: text/plain;');
header('Location: '.$_REQUEST['url'],TRUE,303);

?>