<?php if(!$_REQUEST['format']) { ?>
<xn:head>
<script type="text/javascript">
//<![CDATA[
function createRequestObject(){
	var request_o; //declare the variable to hold the object.
	var browser = navigator.appName; //find the browser name
	if(browser == "Microsoft Internet Explorer"){
		/* Create the object using MSIE's method */
		request_o = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		/* Create the object using other browser's method */
		request_o = new XMLHttpRequest();
	}
	return request_o; //return the object
}

var respid = '';

function getCode(form,type) {
   http = createRequestObject();//get http object
   http.open('get', 'getcode.php?ajax&' + type + '&lang=' + form.lang.value + '&site=' + form.site.value + '&wrinkid=' + form.wrinkid.value + (form.combo.checked ? '&combo' : ''));
   http.onreadystatechange = handleGetCode;//set readystatechange function
   http.send(null);//send request
}//end getRingCode

function handleGetCode() {
   /* Make sure that the transaction has finished. The XMLHttpRequest object 
		has a property called readyState with several states:
		0: Uninitialized
		1: Loading
		2: Loaded
		3: Interactive
		4: Finished */
   out = document.getElementById(respid);
   if(http.readyState == 0) {out.innerHTML = "<i>Uninitialized</i>";}
   if(http.readyState == 1) {out.innerHTML = "<i>Loading...</i>";}
   if(http.readyState == 4){ //Finished loading the response
      out.innerHTML = http.responseText;
   }//end if readyState == 4
}//end handleGet
//]]>
</script>
<style type="text/css">
  .xnc_html_map {
     display:block;
     width:250px;
     float:right;
     margin: 0px;
   }
</style>
</xn:head>
<?php
}//end if !format

if(!$_REQUEST['id']) die('NO ID SPECIFIED!');

if($_REQUEST['format'] == 'js')
   header('Content-Type: text/javascript;charset=utf-8');
if($_REQUEST['format'] == 'txt')
   header('Content-Type: text/plain;charset=utf-8');

require_once 'tagFunctions.php';
require_once 'XNC/HTML.php';

$item = XN_Content::load(intval($_REQUEST['id']));
$sites = array_unique(explode(' ',$item->my->sites));
if($sites == array('')) $sites = array();
$_REQUEST['tag'] = $_REQUEST['tag'] ? strtolower($_REQUEST['tag']) : '';
if($_REQUEST['tag']) {
   foreach($sites as $id => $site) {
      if(!$site) continue;
      $sitedata = XN_Content::load(intval($site));
      $tags = fetchTags($sitedata);
      foreach($tags as $id2 => $tag) $tags[$id2] = strtolower($tag);
      $keepit = true;
      foreach(explode(' ',$_REQUEST['tag']) as $tag)
         $keepit = $keepit && in_array($tag,$tags);
      if(!$keepit)
         unset($sites[$id]);
   }//end foreach sites
}//end if tag

$overalltags = array();
foreach($sites as $id => $site) {
   $sitedata = XN_Content::load(intval($site));
   $tags = fetchTags($sitedata);
   foreach($tags as $id => $tag) $tags[$id] = strtolower($tag);
   $overalltags = array_merge($overalltags,$tags);
}//end foreach sites
$overalltags = array_unique($overalltags);

if(isset($_REQUEST['freshroll'])) {
   header('Content-Type: text/javascript;charset=utf-8');
   $sitestr = $_REQUEST['site'] ? '&amp;site='.$_REQUEST['site'] : '';
   $combostr = isset($_REQUEST['combo']) ? '&amp;combo' : '';
   echo file_get_contents('freshroll.js');
   echo "\n\nvar freshroll_tags = freshroll_fetchTags('','',['".implode("','",$overalltags)."']);";
   if(isset($_REQUEST['combo'])) echo "if(freshroll_tags)\n   ";
   echo "document.writeln('<script type=\"text/javascript\" src=\"http://".$_SERVER['HTTP_HOST']."/wrink.php?xn_auth=no&amp;id=".$item->id."&amp;roll".$combostr."&amp;format=js&amp;tag='+freshroll_tags+'".$sitestr."\"><\/script>');";
   exit;
}//end if isset overalltags

if(isset($_REQUEST['ring'])) {
   if($_REQUEST['format'] == 'txt') {
      $sitestr = $_REQUEST['site'] ? '&amp;siteid='.$_REQUEST['site'] : '';
      echo '<div class="wrink-ring">'."\n";
      echo '   <h2 class="wrink-title"><a href="http://'.$_SERVER['HTTP_HOST'].'/wrink.php?id='.$item->id.'">'.$item->title.'</a></h2>'."\n";
      echo '   <span class="wrink-prev"><a href="http://'.$_SERVER['HTTP_HOST'].'/ringlink.php?wrinkid='.$item->id.$sitestr.'&amp;move=-1">&lt; Prev</a></span> | '."\n";
      echo '   <span class="wrink-rand"><a href="http://'.$_SERVER['HTTP_HOST'].'/ringlink.php?wrinkid='.$item->id.'&amp;move=random">Random</a></span> | '."\n";
      echo '   <span class="wrink-next"><a href="http://'.$_SERVER['HTTP_HOST'].'/ringlink.php?wrinkid='.$item->id.$sitestr.'&amp;move=1">Next &gt;</a></span>'."\n";
      echo '</div>'."\n";
   }//end if format == txt
   if($_REQUEST['format'] == 'js') {
      $sitestr = $_REQUEST['site'] ? '&amp;siteid='.$_REQUEST['site'] : '';
      echo "document.write('<div class=\"wrink-ring\">"."');\n";
      echo "document.write('   <h2 class=\"wrink-title\"><a href=\"http://".$_SERVER['HTTP_HOST'].'/wrink.php?id='.$item->id.'">'.$item->title.'</a></h2>'."');\n";
      echo "document.write('   <span class=\"wrink-prev\"><a href=\"http://".$_SERVER['HTTP_HOST'].'/ringlink.php?wrinkid='.$item->id.$sitestr.'&amp;move=-1">&lt; Prev</a></span> | '."');\n";
      echo "document.write('   <span class=\"wrink-rand\"><a href=\"http://".$_SERVER['HTTP_HOST'].'/ringlink.php?wrinkid='.$item->id.'&amp;move=random">Random</a></span> | '."');\n";
      echo "document.write('   <span class=\"wrink-next\"><a href=\"http://".$_SERVER['HTTP_HOST'].'/ringlink.php?wrinkid='.$item->id.$sitestr.'&amp;move=1">Next &gt;</a></span>'."');\n";
      echo "document.write('".'</div>'."');\n";
   }//end if format == js
   exit;
}//end if isset ring

if(isset($_REQUEST['roll'])) {
   if(!count($sites) || (count($sites) == 1 && $sites[0] == $_REQUEST['site'])) exit;
   if(!$_REQUEST['template']) {
      //template [0] => startcode, [1] => itemcode, [2] => iffeed, [3] => endcode
      $_REQUEST['template'] = array('<h2 class="wrink-title" title="!description!">!title!!titletags!</h2> <ul class="wrink-list">'."\n",'   <li><a href="!url!" onclick="window.location=&quot;http://'.$_SERVER['HTTP_HOST'].'/rollink.php?wrinkid='.$item->id.'&amp;url=!url!&quot;;return false;" title="!description!">!title!</a>!iffeed!</li>'."\n",' <a href="!feedurl!" rel="alternate"><img src="http://'.$_SERVER['HTTP_HOST'].'/feedicon12x12.png" alt="[feed]" /></a>','</ul>'."\n");
      if(isset($_REQUEST['combo']))
         $_REQUEST['template'][0] = '<ul class="wrink-list">';
   }//end if ! template
   if($_REQUEST['format'] == 'js') {
      $_REQUEST['template'][0] = str_replace("\n",'\n',addslashes($_REQUEST['template'][0]));
      $_REQUEST['template'][1] = str_replace("\n",'\n',addslashes($_REQUEST['template'][1]));
      $_REQUEST['template'][2] = str_replace("\n",'\n',addslashes($_REQUEST['template'][2]));
      $_REQUEST['template'][3] = str_replace("\n",'\n',addslashes($_REQUEST['template'][3]));
      echo "document.write('";
   }//end if format == js
   if($_REQUEST['format'] == 'js')
      echo str_replace('!titletags!',($_REQUEST['tag'] ? ' matching "'.$_REQUEST['tag'].'"' : ''),str_replace('!description!',addslashes($item->description),str_replace('!title!',addslashes($item->title),$_REQUEST['template'][0])));
   else
      echo str_replace('!description!',$item->description,str_replace('!title!',$item->title,$_REQUEST['template'][0]));
   if($_REQUEST['format'] == 'js')
      echo "');\n";
   foreach($sites as $site) {
      if($_REQUEST['site'] == $site) continue;
      $sitedata = XN_Content::load(intval($site));
      if($_REQUEST['tag']) {
         if(strstr($sitedata->my->url,'?'))
            $sitedata->my->url = $sitedata->my->url.'&tags='.str_replace(' ','+',$_REQUEST['tag']);
         else
            $sitedata->my->url = $sitedata->my->url.'?tags='.str_replace(' ','+',$_REQUEST['tag']);
      }//end if tag
      if($_REQUEST['format'] == 'js')
         echo "document.write('";
      $feedstr = '';
      if($sitedata->my->feedurl)
         $feedstr = str_replace('!feedurl!',$sitedata->my->feedurl,$_REQUEST['template'][2]);
      if($_REQUEST['format'] == 'js')
         echo str_replace('!iffeed!',$feedstr,str_replace('!url!',addslashes($sitedata->my->url),str_replace('!description!',addslashes($sitedata->description),str_replace('!title!',addslashes($sitedata->title),$_REQUEST['template'][1]))));
      else
         echo str_replace('!iffeed!',$feedstr,str_replace('!url!',$sitedata->my->url,str_replace('!description!',$sitedata->description,str_replace('!title!',$sitedata->title,$_REQUEST['template'][1]))));
      if($_REQUEST['format'] == 'js')
         echo "');\n";
   }//end foreach sites
   if($_REQUEST['format'] == 'js')
      echo "document.write('";
   if($_REQUEST['format'] == 'js')
      echo str_replace('!description!',addslashes($item->description),str_replace('!title!',addslashes($item->title),$_REQUEST['template'][3]));
   else
      echo str_replace('!description!',$item->description,str_replace('!title!',$item->title,$_REQUEST['template'][3]));
   if($_REQUEST['format'] == 'js')
      echo "');\n";
   exit;
}//end if isset roll

if($_REQUEST['format'] == json) {
   header('Content-Type: text/javascript;charset=utf-8');
   if(!isset($_REQUEST['raw'])) {
      if(!$_REQUEST['callback']) {
         echo 'if(typeof(Wrinks) != "object") Wrinks = {};'."\n";
         echo 'Wrinks.wrink = {';
      } else
         echo $_REQUEST['callback'].'({';
   } else
      echo '{';
   echo '"title":"'.$item->title.'",';
   echo '"description":"'.$item->description.'",';
   echo '"user":"'.$item->contributorName.'",';
   echo '"traffic":"'.$item->my->traffic.'",';
   echo '"sites":[';
   foreach($sites as $id => $site) {
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
   if(!isset($_REQUEST['raw'])) {
      if(!$_REQUEST['callback']) {
         echo '};'."\n";
         echo 'if(Wrinks.callbacks && Wrinks.callbacks.wrink) Wrinks.callbacks.wrink(Wrinks.wrink);';
      } else
         echo '});';
   } else
      echo '}';
   exit;
}//end if format == json

$item->focus();

echo '<xn:head><title>Wrinks - '.$item->title.' Wrink'.($_REQUEST['tag'] ? ' (Filtered on '.str_replace(' ','+',$_REQUEST['tag']).')' : '').'</title></xn:head>';
echo '<h2 class="pagetitle">'.$item->title.' Wrink'.($_REQUEST['tag'] ? ' (Filtered on '.str_replace(' ','+',$_REQUEST['tag']).')' : '').'</h2>'."\n";
echo XNC_HTML::buildMap(fetchTagsWithCount($item), '/?tag=%s','',true,70,120)."\n";
echo '<p style="float:right;font-style:italic;"> Traffic - '.$item->my->traffic.'</p>';
echo '<p style="font-style:italic;">'.$item->description.'</p>'."\n";

if(XN_Profile::current()->screenName == $item->contributorName) {
   echo '<p><a href="javascript:toggleitem(&quot;editwrink&quot;);">Edit</a></p>'."\n";
   echo '<form style="display:none;" id="editwrink" method="get" action="editWrink.php"><div>'."\n";
   echo '   <input type="hidden" name="id" value="'.$item->id.'" />'."\n";
   echo '   <label for="title">Wrink Title:</label> <input type="text" name="title" value="'.$item->title.'" /><br />'."\n";
   echo '   <label for="description">Wrink Description:</label> <input type="text" name="description" value="'.$item->description.'" /><br />'."\n";
   echo '<i>To edit tags, use the sidebar.</i><br />'."\n";
   echo '<input type="submit" name="submit" value="Update" />'."\n";
   echo '</div></form>'."\n";
}//end if owner

$sitesdata = array();
if($item->my->sites) {
   echo '<h3>Sites</h3>'."\n";
   if(XN_Profile::current()->screenName == $item->contributorName)
      echo '<form method="get" action="editWrink.php"><input type="hidden" name="id" value="'.$item->id.'" />'."\n";
   echo '<ul>'."\n";
   foreach($sites as $id => $site) {
      $sitedata = XN_Content::load(intval($site));
      $sitesdata[] = $sitedata;
      echo '<li>';
      if(XN_Profile::current()->screenName == $item->contributorName)
         echo '<input type="text" size="2" name="sites['.$site.']" value="'.$id.'" /> ';
      echo '<a href="'.$sitedata->my->url.'" title="'.$sitedata->description.'">'.$sitedata->title.'</a>';
      if($sitedata->my->feedurl)
         echo ' <a href="'.$sitedata->my->feedurl.'" rel="alternate"><img src="feedicon12x12.png" alt="[feed]" /></a>';
      echo ' - <a href="site.php?id='.$sitedata->id.'">tag</a>';
      if(XN_Profile::current()->screenName == $item->contributorName)
         echo ' - <a href="rejectSite.php?wrinkid='.$item->id.'&amp;sideid='.$site.'">remove</a>';
      echo '</li>'."\n";
   }//end foreach sites
   echo '</ul>'."\n";
   if(XN_Profile::current()->screenName == $item->contributorName)
      echo '<input style="margin-left:40px;" type="submit" name="submit" value="Update" /> </form>'."\n";
}//end if isset sites

if($item->my->requestedsites) {
   echo '<h3>Requested Site Additions</h3>'."\n";
   echo '<ul>'."\n";
   $sites = array_unique(explode(' ',$item->my->requestedsites));
   foreach($sites as $site) {
      $sitedata = XN_Content::load(intval($site));
      echo '<li><a href="'.$sitedata->my->url.'" title="'.$sitedata->description.'">'.$sitedata->title.'</a>';
      if(XN_Profile::current()->screenName == $item->contributorName)
         echo ' - <a href="acceptSite.php?wrinkid='.$item->id.'&amp;siteid='.$site.'">accept</a> / <a href="rejectSite.php?wrinkid='.$item->id.'&amp;siteid='.$site.'">reject</a>';
      echo '</li>'."\n";
   }//end foreach sites
   echo '</ul>'."\n";
}//end if isset sites

echo '<p><a href="javascript:toggleitem(&quot;ringcode&quot;);">Get Ring Code</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="javascript:toggleitem(&quot;rollcode&quot;);">Get Roll Code</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="javascript:toggleitem(&quot;freshrollcode&quot;);">Get FreshRoll Code</a> <a href="about.php#freshroll">(?)</a></p>'."\n";

echo '<div id="ringcode" style="display:none;">'."\n";
echo '<form action="getcode.php" onsubmit="respid = &quot;ringcoderesp&quot;;getCode(this,&quot;ring&quot;);return false;"><div>'."\n";
echo '   <label for="lang">Code Type:</label> <select name="lang">'."\n";
echo '      <option value="xanga">Xanga</option>'."\n";
echo '      <option value="js">JavaScript</option>'."\n";
echo '      <option value="php">PHP</option>'."\n";
echo '   </select><br />'."\n";
echo '   <label for="site">Site code will go on:</label> <select name="site">'."\n";
echo '      <option value="" title="Have your site determined by the code -- do not choose this if you are having problems getting it to work properly">Automatic</option>'."\n";
foreach($sitesdata as $site) {
   echo '<option value="'.$site->id.'">'.$site->title.'</option>';
}//end foreach
echo '   </select><br />'."\n";
echo '   <input type="hidden" name="wrinkid" value="'.$item->id.'" />'."\n";
echo '   <input type="hidden" name="ring" value="" />'."\n";
echo '   <input type="submit" name="submit" value="Get Code" />'."\n";
echo '</div></form>'."\n";
echo '<div id="ringcoderesp"></div>';
echo '</div>'."\n";

echo '<div id="rollcode" style="display:none;">'."\n";
echo '<form action="getcode.php" onsubmit="respid = &quot;rollcoderesp&quot;;getCode(this,&quot;roll&quot;);return false;"><div>'."\n";
echo '   <label for="lang">Code Type:</label> <select name="lang">'."\n";
echo '      <option value="js">JavaScript</option>'."\n";
echo '      <option value="php">PHP</option>'."\n";
echo '   </select><br />'."\n";
echo '   <label for="site">Site to exclude:</label> <select name="site">'."\n";
echo '      <option value="">None</option>'."\n";
foreach($sitesdata as $site) {
   echo '<option value="'.$site->id.'">'.$site->title.'</option>';
}//end foreach
echo '   </select><br />'."\n";
echo '   <input type="hidden" name="wrinkid" value="'.$item->id.'" />'."\n";
echo '   <input type="hidden" name="roll" value="" />'."\n";
echo '   <input type="submit" name="submit" value="Get Code" />'."\n";
echo '</div></form>'."\n";
echo '<div id="rollcoderesp"></div>';
echo '</div>'."\n";

echo '<div id="freshrollcode" style="display:none;">'."\n";
echo '<form action="getcode.php" onsubmit="respid = &quot;freshrollcoderesp&quot;;getCode(this,&quot;freshroll&quot;);return false;"><div>'."\n";
echo '   <label for="combo">Used with ring?</label> <input type="checkbox" name="combo" /><br />';
echo '   <label for="site">Site to exclude:</label> <select name="site">'."\n";
echo '      <option value="">None</option>'."\n";
foreach($sitesdata as $site) {
   echo '<option value="'.$site->id.'">'.$site->title.'</option>';
}//end foreach
echo '   </select><br />'."\n";
echo '   <input type="hidden" name="wrinkid" value="'.$item->id.'" />'."\n";
echo '   <input type="hidden" name="freshroll" value="" />'."\n";
echo '   <input type="submit" name="submit" value="Get Code" />'."\n";
echo '</div></form>'."\n";
echo '<div id="freshrollcoderesp"></div>';
echo '</div>'."\n";


echo '<p style="float:right;"><a href="?xn_auth=no&amp;format=json&amp;'.$_SERVER['QUERY_STRING'].'">JSON</a></p>';

if(XN_Profile::current()->screenName == $item->contributorName)
   echo '<p><a href="javascript:toggleitem(&quot;addsite&quot;);">Add site to wrink</a></p>'."\n";
else
   echo '<p><a href="javascript:toggleitem(&quot;addsite&quot;);">Request to add site to wrink</a></p>'."\n";
$_REQUEST['towrink'] = $item->id;
echo '<div id="addsite" style="display:none;">';
require('addSite.php');
echo '</div>';

?>