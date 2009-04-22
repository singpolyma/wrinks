<?php

if(!$_REQUEST['id'])
   die('<b>ERROR : No ID given</b>');

$item = XN_Content::load(intval($_REQUEST['id']));

if(XN_Profile::current()->screenName == $item->contributorName) {

if(isset($_REQUEST['title']))
   $item->title = $_REQUEST['title'];
if(isset($_REQUEST['description']))
   $item->description = $_REQUEST['description'];
if(isset($_REQUEST['url']))
   $item->my->url = $_REQUEST['url'];
if(isset($_REQUEST['feedurl']))
   $item->my->feedurl = $_REQUEST['feedurl'];

}//end if sn == cn

$item->save();

if(isset($_REQUEST['tags']) && XN_Profile::current()->isLoggedIn()) {
   $_REQUEST['tags'] = str_replace(' ',',',$_REQUEST['tags']);
   XN_Tag::checkTags($_REQUEST['tags']);
   XN_Tag::addTags($item, $_REQUEST['tags']);
}//end if tags && loggedin

header('Content-Type: text/plain;');
header('Location: http://'.$_SERVER['HTTP_HOST'].'/site.php?id='.$_REQUEST['id'],TRUE,303);

?>