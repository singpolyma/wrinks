<?php

if(!$_REQUEST['wrinkid'] || !$_REQUEST['siteid'])
   die("<b>ERROR : no wrink/site ids given</b>");

//update wrink
$wrink = XN_Content::load(intval($_REQUEST['wrinkid']));
if(XN_Profile::current()->screenName != $wrink->contributorName)
   die("<b>Only Wrink owner can approve sites!</b>");
$requestedsites = array_unique(explode(' ',$wrink->my->requestedsites));
foreach($requestedsites as $id => $site) {
   if($site == $_REQUEST['siteid'] || !$site)
      unset($requestedsites[$id]);
}//end foreach
$wrink->my->requestedsites = implode(' ',$requestedsites);
$wrink->my->sites .= ' '.$_REQUEST['siteid'];
$wrink->save();

//update site
$site = XN_Content::load(intval($_REQUEST['siteid']));
if($site->my->wrinks)
   $site->my->wrinks .= ' '.$_REQUEST['wrinkid'];
else
   $site->my->add('wrinks',$_REQUEST['wrinkid']);
if($site->my->rejectedwrinks) {
   $rejects = array_unique(explode(' ',$site->my->rejectedwrinks));
   foreach($rejects as $id => $reject) {
      if($reject == $_REQUEST['wrinkid'] || !$reject)
         unset($rejects[$id]);
   }//end foreach
   $site->my->rejectedwrinks = implode(' ',$rejects);
}//end if rejectedwrinks
$site->save();

header('Content-Type: text/plain;');
header('Location: http://'.$_SERVER['HTTP_HOST'].'/wrink.php?id='.$_REQUEST['wrinkid'],TRUE,303);

?>