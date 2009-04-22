<xn:head>
<style type="text/css">
  .xnc_html_map,#tag_form {
     display:block;
     width:250px;
     float:right;
     clear:both;
     margin: 0px;
   }
</style>
</xn:head>
<?php

require_once 'tagFunctions.php';
require_once 'XNC/HTML.php';

$item = XN_Content::load(intval($_REQUEST['id']));
$item->focus();

echo '<xn:head><title>Wrinks - Site : '.$item->title.'</title></xn:head>';
echo '<h2 class="pagetitle">Site : '.$item->title.'</h2>'."\n";

echo XNC_HTML::buildMap(fetchTagsWithCount($item), 'sites.php?tag=%s','',true,70,120)."\n";

if(XN_Profile::current()->isLoggedIn()) {
   ?>
<form method="get" action="editSite.php"><div id="tag_form" style="margin:0px;background-color:transparent;padding:0px;width:250px;"><br />
<?php echo '   <input type="hidden" name="id" value="'.$item->id.'" />'; ?>
   <input type="text" name="tags" />
   <input type="submit" value="Add Tags" />
</div></form>
   <?php
}//end if isLoggedIn

echo '<p style="font-style:italic;">'.$item->description.'</p>'."\n";

if(XN_Profile::current()->screenName == $item->contributorName) {
   echo '<p><a href="javascript:toggleitem(&quot;editsite&quot;);">Edit</a></p>'."\n";
   echo '<form style="display:none;" id="editsite" method="get" action="editSite.php"><div>'."\n";
   echo '   <input type="hidden" name="id" value="'.$item->id.'" />'."\n";
   echo '   <label for="title">Site Title:</label> <input type="text" name="title" value="'.$item->title.'" /><br />'."\n";
   echo '   <label for="description">Site Description:</label> <input type="text" name="description" value="'.$item->description.'" /><br />'."\n";
   echo '   <label for="url">Site URL:</label> <input type="text" name="url" value="'.$item->my->url.'" /><br />'."\n";
   echo '   <label for="feedurl">Feed URL 
(optional):</label> <input type="text" name="feedurl" value="'.$item->my->feedurl.'" /><br />'."\n";
   echo '<input type="submit" name="submit" value="Update" />'."\n";
   echo '</div></form>'."\n";
}//end if owner

if($item->my->wrinks) {
   echo '<h3>In Wrinks</h3>'."\n";
   echo '<ul>'."\n";
   $wrinks = array_unique(explode(' ',$item->my->wrinks));
   foreach($wrinks as $wrink) {
      $wrinkdata = XN_Content::load(intval($wrink));
      echo '<li>';
      echo '<a href="/wrink.php?id='.$wrink.'" title="'.$wrinkdata->description.'">'.$wrinkdata->title.'</a>';
      echo '</li>'."\n";
   }//end foreach sites
   echo '</ul>'."\n";
}//end if isset wrinks

if($item->my->rejectedwrinks && XN_Profile::current()->screenName == $item->contributorName) {
   echo '<h3>Rejected From Wrinks</h3>'."\n";
   echo '<ul>'."\n";
   $reject = array_unique(explode(' ',$item->my->rejectedwrinks));
   foreach($reject as $wrink) {
      $wrinkdata = XN_Content::load(intval($wrink));
      echo '<li><a href="/wrink.php?id='.$wrink.'" title="'.$wrinkdata->description.'">'.$wrinkdata->title.'</a>';
      echo '</li>'."\n";
   }//end foreach sites
   echo '</ul>'."\n";
}//end if rejectedwrinks

?>