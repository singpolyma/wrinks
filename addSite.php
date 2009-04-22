<?php

if(!XN_Profile::current()->isLoggedIn())
   die('<b>Please log in</b>');

if(!isset($_REQUEST['submit'])) {
   ?>
   <h2 class="pagetitle">Add Site<?php if($_REQUEST['towrink']) echo ' to Wrink'; ?></h2>
   <form method="get" action="addSite.php"><div>
      <input type="hidden" name="towrink" value="<?php echo $_REQUEST['towrink']; ?>" />
      <label for="title">Site title:</label> <input type="text" name="title" value="" /><br />
      <label for="description">Site description:</label> <input type="text" name="description" value="" /><br />
      <label for="url">Site URL:</label> <input type="text" name="url" value="" /><br />
      <label for="feedurl">Feed URL:</label> <input type="text" name="feedurl" value="" /><br />
      <label for="tags">Tags:</label> <input type="text" name="tags" value="" /><br />
      <input type="submit" name="submit" value="Add" />
   </div></form>
   <?php
} else {
    header('Content-Type: text/plain;');
    $item = XN_Query::create('Content')
            ->filter('owner','=')
            ->filter('type','eic','Website')
            ->filter('my.url','=',$_REQUEST['url']);
    $item = $item->execute();
    if(count($item)) {
       $item = $item[0];
    } else {
       $item = XN_Content::create('Website',$_REQUEST['title'],$_REQUEST['description'])
                  ->my->add('url',$_REQUEST['url'])
                  ->my->add('feedurl',$_REQUEST['feedurl']);
       $item->save();
       XN_Tag::checkTags($_REQUEST['tags']);
       XN_Tag::addTags($item, $_REQUEST['tags']);
    }//end if-else count item
    if($_REQUEST['towrink']) {
       $wrink = XN_Content::load(intval($_REQUEST['towrink']));
       if(XN_Profile::current()->screenName == $wrink->contributorName) {
          if($wrink->my->sites)
             $wrink->my->sites .= ' '.$item->id;
          else
             $wrink->my->add('sites',$item->id);
          if($item->my->wrinks)
             $item->my->wrinks .= ' '.$_REQUEST['towrink'];
          else
             $item->my->add('wrinks',$_REQUEST['towrink']);
          $item->save();
       } else {
          if($wrink->my->requestedsites)
             $wrink->my->requestedsites .= ' '.$item->id;
          else
             $wrink->my->add('requestedsites',$item->id);
       }//end if-else current == contributorName
       $wrink->save();
       header('Location: http://'.$_SERVER['HTTP_HOST'].'/wrink.php?id='.$_REQUEST['towrink'],TRUE,303);
    } else {
       header('Location: http://'.$_SERVER['HTTP_HOST'].'/site.php?id='.$item->id,TRUE,303);
    }//end if-else towrink
}//end if-else ! isset submit

?>