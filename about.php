<xn:head>
  <title>Wrinks - About</title>
</xn:head>
<h2 class="pagetitle">About Wrinks</h2>

<h3>What Are Wrinks?</h3>
<p>Wrinks are webrings, blogrings, linkrolls, and blogrolls all in one.  Since each of these different things is essentially the same -- an ordered list of websites -- combining them only requires different implementations for including them in webpages.</p>

<h3>How Do I Include Wrinks in my Site?</h3>
<p>You can include a wrink in your website as either a webring/blogring module, or as a linkroll/blogroll.</p>
<p>To get webring/blogring code click the 'Get Ring Code' link on the page for the Wrink you wish to include in your site.  Select the code type (if you want to include the Wrink in a Xanga choose 'Xanga', for sites where you can edit the HTML code choose 'JavaScript' and for sites where you can include PHP choose 'PHP') from the drop-down box.  You can leave the 'Site code will go on' drop-down set to automatic, but if you have problems getting the previous/next links to work right then choose your site from this box.  Click 'Get Code' and the code you need, along with any instructions, will be loaded below the form.</p>
<p>To get linkroll/blogroll code click the 'Get Roll Code' link on the page for the Wrink you wish to include in your site.  Select the code type and optional site to exclude from the list and click 'Get Code'.</p>

<h3>How is the Traffic Calculated?</h3>
<p>Every time someone clicks a link on a wrink embedded in someone's site, a signal is sent back here and one is added to the Traffic number.  The traffic number is automatically reset to zero about every week.</p>

<h3>Tips</h3>
<p>You can filter the contents of a Wrink by tag.  Tack &amp;tag=TAG onto the end of the URL for the Wrink and get back only sites from that wrink matching the tag.  Tag intersections are also supported.</p>
<p>You can customise the format of wrink-rolls by passing &amp;template[0..3].  [0] is the start code, [1] is the code for roll items, [2] is the code for the !iffeed! field, and [3] is the end code.  Fields are: [0], [1], and [3] !title! and !description!, [0] !titletags!, [1] !url! and !iffeed!, [2] !feedurl!</p>

<h3 id="freshroll">What's a FreshRoll?</h3>
<p>A FreshRoll is a Linkroll/Blogroll based on the concepts of the <a href="ghill.customer.netspace.net.au/freshtags/">FreshTags</a> system.  It only comes in a JavaScript flavour for now.  When someone visits a page, the script tries to pull in tags from the URL of the page you were just at (ie from a search engine query or a site running FreshTags).  If it detects some, it limits the list to only sites from that/those tag(s) and alters their URL to pass the tags to them as well (for if they run a FreshRoll or FreshTags), otherwise it may display the whole list or display nothing (depending on how you set it).</p>
<p>The settings are similar to that of regular roll code for a Wrink, but it also has a 'Used with ring?' checkbox to specify if you are going to be putting this code in after standard ring code (the title will then be hidden and if there is no detected tag no list will be displayed).</p>