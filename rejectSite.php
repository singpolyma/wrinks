<?php

if(!$_REQUEST['wrinkid'] || !$_REQUEST['siteid'])
   die("<b>ERROR : no wrink/site ids given</b>");

//update wrink
$wrink = XN_Content::load(intval($_REQUEST['wrinkid']));
if(XN_Profile::current()->screenName != $wrink->contributorName)
   die("<b>Only Wrink owner can reject sites!</b>");
$requestedsites = explode(' ',$wrink->my->requestedsites);
foreach($requestedsites as $id => $site) {
   if($site == $_REQUEST['siteid'] || !$site)
      unset($requestedsites[$id]);
}//end foreach
$wrink->my->requestedsites = implode(' ',$requestedsites);
$wrink->save();

//update site
$site = XN_Content::load(intval($_REQUEST['siteid']));
if($site->my->rejectedwrinks)
   $site->my->rejectedwrinks .= ' '.$_REQUEST['wrinkid'];
else
   $site->my->add('rejectedwrinks',$_REQUEST['wrinkid']);
if($site->my->wrinks) {
   $wrinks = array_unique(explode(' ',$site->my->wrinks));
   foreach($wrinks as $id => $wrink) {
      if($wrink == $_REQUEST['wrinkid'] || !$wrink)
         unset($wrinks[$id]);
   }//end foreach
   $site->my->wrinks = implode(' ',$wrinks);
}//end if wrinks
$site->save();

header('Content-Type: text/plain;');
header('Location: http://'.$_SERVER['HTTP_HOST'].'/wrink.php?id='.$_REQUEST['wrinkid'],TRUE,303);

?>