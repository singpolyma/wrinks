<?php

if(!XN_Profile::current()->isLoggedIn())
   die('<b>Please log in</b>');

if(!isset($_REQUEST['submit'])) {
   ?>
   <h2 class="pagetitle">Add Wrink</h2>
   <form method="get" action="addWrink.php"><div>
      <label for="title">Wrink title:</label> <input type="text" name="title" value="" /><br />
      <label for="description">Wrink description:</label> <input type="text" name="description" value="" /><br />
      <label for="tags">Wrink tags:</label> <input type="text" name="tags" value="" /><br />
      <input type="submit" name="submit" value="Add" />
   </div></form>
   <?php
} else {
   header('Content-Type: text/plain;');
   $item = XN_Content::create('Wrink',$_REQUEST['title'],$_REQUEST['description'])
           ->my->add('traffic',0);
   $item->save();
   XN_Tag::checkTags($_REQUEST['tags']);
   XN_Tag::addTags($item, $_REQUEST['tags']);
   header('Location: http://'.$_SERVER['HTTP_HOST'].'/wrink.php?id='.$item->id,TRUE,303);
}//end if-else ! isset submit

?>