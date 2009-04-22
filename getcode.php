<?php

header('Content-Type: text/plain;');
if(isset($_REQUEST['ring'])) {
   if($_REQUEST['lang'] == 'php') {
      $sitestr = $_REQUEST['site'] ? '&site='.$_REQUEST['site'] : '';
      $code = "<?php echo file_get_contents('http://".$_SERVER['HTTP_HOST']."/wrink.php?xn_auth=no&id=".$_REQUEST['wrinkid'].$sitestr."&ring&format=txt'); ?>";
   }//end if lang == php
   if($_REQUEST['lang'] == 'js') {
      $sitestr = $_REQUEST['site'] ? '&amp;site='.$_REQUEST['site'] : '';
      $code = '<script type="text/javascript" src="http://'.$_SERVER['HTTP_HOST'].'/wrink.php?xn_auth=no&amp;id='.$_REQUEST['wrinkid'].$sitestr.'&amp;ring&amp;format=js"></script>';
   }//end if lang == js
   if($_REQUEST['lang'] == 'xanga') {
      echo 'Insert this code into the "Website Stats" box in your Xanga look and feel settings:';
      $wrink = XN_Content::load(intval($_REQUEST['wrinkid']));
      $sitestr = $_REQUEST['site'] ? '&siteid='.$_REQUEST['site'] : '';
      $code = '<script type="text/javascript">'."\n";
      $code .= '//<![CDATA['."\n";
      $code .= "var tbls = document.getElementsByTagName('table');\n";
      $code .= "var wrinkmaindiv = document.createElement('div');\n";
      $code .= "wrinkmaindiv.className = 'wrink-ring';\n";
      $code .= "\n//do title\n";
      $code .= "var wrinktitle = document.createElement('b');\n";
      $code .= "wrinktitle.className = 'wrink-title';\n";
      $code .= "var wrinktitlelink = document.createElement('a');\n";
      $code .= "wrinktitlelink.href = 'http://".$_SERVER['HTTP_HOST']."/wrink.php?id=".$_REQUEST['wrinkid']."';\n";
      $code .= "wrinktitlelink.appendChild( document.createTextNode( '".$wrink->title."' ) );\n";
      $code .= "wrinktitle.appendChild(wrinktitlelink);\n";
      $code .= "wrinkmaindiv.appendChild(wrinktitle);\n";
      $code .= "\nwrinkmaindiv.appendChild(document.createElement('br'));\n";
      $code .= "\n//do other\n";
      $code .= "var wrinksmalltxt = document.createElement('span');\n";
      $code .= "wrinksmalltxt.className = 'smalltext';\n";
      $code .= "\n//do prevlink\n";
      $code .= "\nvar wrinkprevlink = document.createElement('a');\n";
      $code .= "\nwrinkprevlink.href = 'http://".$_SERVER['HTTP_HOST']."/ringlink.php?wrinkid=".$_REQUEST['wrinkid'].$sitestr."&move=-1';\n";
      $code .= "wrinkprevlink.appendChild( document.createTextNode( 'previous' ) );\n";
      $code .= "wrinksmalltxt.appendChild(wrinkprevlink);\n";
      $code .= "\nwrinksmalltxt.appendChild(document.createTextNode(' - '));\n";
      $code .= "\n//do randlink\n";
      $code .= "var wrinkrandlink = document.createElement('a');\n";
      $code .= "wrinkrandlink.href = 'http://".$_SERVER['HTTP_HOST']."/ringlink.php?wrinkid=".$_REQUEST['wrinkid'].$sitestr."&move=random';\n";
      $code .= "wrinkrandlink.appendChild( document.createTextNode( 'random' ) );\n";
      $code .= "wrinksmalltxt.appendChild(wrinkrandlink);\n";
      $code .= "\nwrinksmalltxt.appendChild(document.createTextNode(' - '));\n";
      $code .= "\n//do nextlink\n";
      $code .= "var wrinknextlink = document.createElement('a');\n";
      $code .= "wrinknextlink.href = 'http://".$_SERVER['HTTP_HOST']."/ringlink.php?wrinkid=".$_REQUEST['wrinkid'].$sitestr."&move=1';\n";
      $code .= "wrinknextlink.appendChild( document.createTextNode( 'next' ) );\n";
      $code .= "wrinksmalltxt.appendChild(wrinknextlink);\n";
      $code .= "\nwrinkmaindiv.appendChild(wrinksmalltxt);\n";
      $code .= "wrinkmaindiv.appendChild(document.createElement('br'));\n";
      $code .= "wrinkmaindiv.appendChild(document.createElement('br'));\n";
      $code .= "\ntbls[6].appendChild(wrinkmaindiv);\n";
      $code .= "//]]>\n";
      $code .= "</script>\n";
   }//end if lang == xanga
   if(isset($_REQUEST['ajax'])) {
      echo '<textarea style="width:400px;height:100px;" onclick="this.select();">';
      echo htmlentities($code);
      echo '</textarea>';
   } else
      echo $code;
}//end if isset ring

if(isset($_REQUEST['roll'])) {
   if($_REQUEST['lang'] == 'php') {
      $sitestr = $_REQUEST['site'] ? '&site='.$_REQUEST['site'] : '';
      $code = "<?php echo file_get_contents('http://".$_SERVER['HTTP_HOST']."/wrink.php?xn_auth=no&id=".$_REQUEST['wrinkid'].$sitestr."&roll&format=txt'); ?>";
   }//end if lang == php
   if($_REQUEST['lang'] == 'js') {
      $sitestr = $_REQUEST['site'] ? '&amp;site='.$_REQUEST['site'] : '';
      $code = '<script type="text/javascript" src="http://'.$_SERVER['HTTP_HOST'].'/wrink.php?xn_auth=no&amp;id='.$_REQUEST['wrinkid'].$sitestr.'&amp;roll&amp;format=js"></script>';
   }//end if lang == js
   if(isset($_REQUEST['ajax'])) {
      echo '<textarea style="width:400px;height:100px;" onclick="this.select();">';
      echo htmlentities($code);
      echo '</textarea>';
   } else
      echo $code;
}//end if isset roll

if(isset($_REQUEST['freshroll'])) {
      $sitestr = $_REQUEST['site'] ? '&amp;site='.$_REQUEST['site'] : '';
      $combostr = isset($_REQUEST['combo']) ? '&amp;combo' : '';
      $code = '<script type="text/javascript" src="http://'.$_SERVER['HTTP_HOST'].'/wrink.php?xn_auth=no&amp;id='.$_REQUEST['wrinkid'].$sitestr.'&amp;freshroll'.$combostr.'&amp;format=js"></script>';
   if(isset($_REQUEST['ajax'])) {
      echo '<textarea style="width:400px;height:100px;" onclick="this.select();">';
      echo htmlentities($code);
      echo '</textarea>';
   } else
      echo $code;
}//end if isset freshroll

?>