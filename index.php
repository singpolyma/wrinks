<?php 

require_once 'tagFunctions.php';

$query = XN_Query::create('Content')
         ->filter('owner','=')
         ->filter('type','eic','Wrink')
         ->alwaysReturnTotalCount(true)
         ->order('my.traffic','desc',XN_Attribute::NUMBER);
if($_REQUEST['user'])
   $query->filter('contributorName','=',$_REQUEST['user']);
if($_REQUEST['tag'])
   $query->filter('tag.value','eic',$_REQUEST['tag']);

// handle pagination
$_REQUEST['maxitems'] = $_REQUEST['maxitems'] ? $_REQUEST['maxitems'] : 20;
$start = (isset($_REQUEST['start']) && ctype_digit($_REQUEST['start']) ? $_REQUEST['start'] : 0);
$end = $start + $_REQUEST['maxitems'];
$query->begin($start)->end($end);

$items = $query->execute();

// prepare pagination numbers for displaying result set info
$total = $query->getTotalCount();
$from = ($query->getResultFrom() != 0 ? 1+$query->getResultFrom() : 1);
$to = $query->getResultTo();

$title = 'Most Trafficked Wrinks';
if($_REQUEST['tag'])
   $title .= ' in '.$_REQUEST['tag'];
if($_REQUEST['user'])
   $title .= ' from '.$_REQUEST['user'];

if($_REQUEST['format'] == 'rss20') {
   header('Content-Type: application/xml;charset=utf-8');
   echo '<?xml version="1.0"?>'."\n";
   echo '<rss version="2.0">'."\n";
   echo '<channel>'."\n";
   echo '   <title>'.htmlspecialchars($title).'</title>'."\n";
   echo '   <link>http://'.$_SERVER['HTTP_HOST'].'/?'.($_REQUEST['tag'] ? 'tag='.$_REQUEST['tag'] : '').(($_REQUEST['tag'] && $_REQUEST['user']) ? '&amp;' : '').($_REQUEST['user'] ? 'user='.$_REQUEST['user'] : '').'</link>'."\n";
   echo '   <docs>http://blogs.law.harvard.edu/tech/rss</docs>'."\n";
   echo '   <generator>Wrinks (PHP Script)</generator>'."\n";
   echo '   <pubDate>'.date("D, d M Y H:i:s O", time()).'</pubDate>'."\n";
   echo '   <lastBuildDate>'.date("D, d M Y H:i:s O", time()).'</lastBuildDate>'."\n";
   foreach($items as $item) {
      echo "\n   <item>\n";
      echo '      <title>'.htmlspecialchars($item->title).'</title>'."\n";
      echo '      <description>'.htmlspecialchars($item->description).'</description>'."\n";
      echo '      <link>http://'.$_SERVER['HTTP_HOST'].'/wrink.php?id='.$item->id.'</link>'."\n";
      echo '      <guid>http://'.$_SERVER['HTTP_HOST'].'/wrink.php?id='.$item->id.'</guid>'."\n";
      echo "   </item>\n";
   }//end foreach
   echo "\n</channel>\n";
   echo '</rss>';
   exit;
}//end if format == rss20

if($_REQUEST['format'] == json) {
   header('Content-Type: text/javascript;charset=utf-8');
   if(!isset($_REQUEST['raw'])) {
      if(!$_REQUEST['callback']) {
         echo 'if(typeof(Wrinks) != "object") Wrinks = {};'."\n";
         echo 'Wrinks.wrinks = [';
      } else
         echo $_REQUEST['callback'].'({"wrinks":[';
   } else
      echo '{"wrinks":[';
   foreach($items as $id => $item) {
      if($id > 0)
         echo ',';
      echo '{';
      echo '"title":"'.$item->title.'",';
      echo '"description":"'.$item->description.'",';
      echo '"user":"'.$item->contributorName.'",';
      echo '"traffic":"'.$item->my->traffic.'",';
      echo '"sites":[';
      foreach(explode(' ',$item->my->sites) as $id => $site) {
         $sitedata = XN_Content::load(intval($site));
         if($id > 0)
            echo ',';
         echo '{';
         echo '"title":"'.$sitedata->title.'",';
         echo '"description":"'.$sitedata->description.'",';
         echo '"url":"'.$sitedata->my->url.'",';
         echo '"feedurl":"'.$sitedata->my->feedurl.'",';
         $tags = fetchTags($sitedata);
         if(count($tags))
            echo '"tags":["'.implode('","',$tags).'"]';
         else
            echo '"tags":[]';
         echo '}';
      }//end foreach sites
      echo '],';
      $tags = fetchTags($item);
      if(count($tags))
         echo '"tags":["'.implode('","',$tags).'"]';
      else
         echo '"tags":[]';
      echo '}';
   }//end foreach
   if(!isset($_REQUEST['raw'])) {
      if(!$_REQUEST['callback']) {
         echo '];'."\n";
         echo 'if(Wrinks.callbacks && Wrinks.callbacks.wrinks) Wrinks.callbacks.wrinks(Wrinks.wrinks);';
      } else
         echo ']});';
   } else
      echo ']}';
   exit;
}//end if format == json

echo '<xn:head><title>Wrinks - '.$title.'</title></xn:head>';
echo '<h2 class="pagetitle">';
echo $title;
echo ' ('.$from.' - '.$to.' of '.$total.')</h2>'."\n";

echo '<div style="float:right;width:150px;" id="sidebar">';
echo '<h2>Top 20 Tags</h2>';
echo '<ul>';
foreach(getTagCount(20,true) as $tag => $count) {
   echo '<li><a href="?tag='.$tag.'" class="tag">'.$tag.'</a> ('.$count.')</li>';
}//end foreach
echo '</ul>';
echo '<h2>Syndicate</h2>';
echo '<ul>';
echo '<li><a href="?xn_auth=no&amp;format=rss20&amp;'.$_SERVER['QUERY_STRING'].'">RSS 2.0</a></li>';
echo '<li><a href="?xn_auth=no&amp;format=json&amp;'.$_SERVER['QUERY_STRING'].'">JSON</a></li>';
echo '</ul>';
echo '</div>';

echo '<ul>'."\n";
foreach($items as $item) {
   echo '   <li>';
   echo ' <a href="wrink.php?id='.$item->id.'" title="'.$item->description.'">'.$item->title.'</a> ';
   echo ' (from <a href="?user='.$item->contributorName.'" class="user">'.$item->contributorName.'</a>';
   $tags = fetchTags($item);
   if(count($tags)) {
      echo ' in ';
      foreach($tags as $tag)
         echo ' <a href="?tag='.$tag.'" class="tag">'.$tag.'</a>';
   }//end if count tags
   echo ')';
   echo '</li>'."\n";
}//end foreach
echo '</ul>'."\n";

if($to < $total)
   echo '<p><a href="?'.$_SERVER['QUERY_STRING'].'&amp;start='.$to.'">More &raquo;</a></p>';

if($total < 20)
   echo '<p style="height:200px;"> </p>';
else
   echo '<p style="height:50px;"> </p>';
if(XN_Profile::current()->isLoggedIn()) require('addWrink.php');

?>