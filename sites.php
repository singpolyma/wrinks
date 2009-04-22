<?php 

require_once 'tagFunctions.php';

$query = XN_Query::create('Content')
         ->filter('owner','=')
         ->filter('type','eic','Website')
         ->alwaysReturnTotalCount(true)
         ->order('title');
if($_REQUEST['user'])
   $query->filter('contributorName','=',$_REQUEST['user']);
if($_REQUEST['tag'])
   $query->filter('tag.value','eic',$_REQUEST['tag']);

// handle pagination
$_REQUEST['maxitems'] = $_REQUEST['maxitems'] ? $_REQUEST['maxitems'] : 20;
$start = (isset($_GET['start']) && ctype_digit($_GET['start']) ? $_GET['start'] : 0);
$end = $start + $_REQUEST['maxitems'];
$query->begin($start)->end($end);

$items = $query->execute();

// prepare pagination numbers for displaying result set info
$total = $query->getTotalCount();
$from = ($query->getResultFrom() != 0 ? 1+$query->getResultFrom() : 1);
$to = $query->getResultTo();

$title = 'Sites ';
if($_REQUEST['tag'])
   $title .= ' in '.$_REQUEST['tag'];
if($_REQUEST['user'])
   $title .= ' from '.$_REQUEST['user'];
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
echo '</div>';

echo '<ul>'."\n";
foreach($items as $item) {
   echo '   <li>';
   echo ' <a href="site.php?id='.$item->id.'" title="'.$item->description.'">'.$item->title.'</a> ';
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

?>