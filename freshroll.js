//borrowed code from FreshTags - get tags from current/referrer URL

function freshroll_filterTags( tags, script_tags )
{
// returns filtered tagstring, dropping non-tags (or unlikely tags, for generic mode)

tags=tags.replace(/[+]/g, ' ');
var black = 'also another could every find from have here into I just many more most much next only really same should show still such that their them then there these they thing this those very well were what when where which while will with without would'; 

var white = 'art bbc css diy irc job fun law log mac map net osx pda php rdf rss tag tax tv web win xml';
var match = [], i, rexp, stop;

tags = tags.replace(/[!?\"#]/g,'');					// knock out punctuation

if (script_tags)
{
	for(i in script_tags )
		if( (new RegExp('\\b('+ script_tags[i] +')\\b', 'i')).test(tags) )
			match.push(script_tags[i]);
	return match.join('+');
}

// stop list is: known black words + (lowercase words shorter than 4) - known white words

rexp = new RegExp('\\b([a-z]{1,3})\\b','g'); 				// lowercase words <4

if (tags.match(rexp))
	black += ' '+tags.match(rexp).join(' ');

rexp = new RegExp('\\b('+white.replace(/ +/g,'|')+')\\b','ig');	// | separted white list
black = black.replace(rexp,'');

stop = new RegExp('\\b('+black.replace(/ +/g,'|')+')\\b', 'ig');
tags = tags.replace(stop, '').replace(/ +/g, '+');
return tags.replace(/^\+|\+$/g, '');
}

function freshroll_fetchTags( source, names, script_tags )
{
// try matching tag in query string; 
// failing that try looking in referrer query string;
// if that fails, try looking in referrer pathname.

var ref=document.referrer.indexOf('?');					// separate out referrer query string
var ref_path=document.referrer;
var ref_query='';

if (ref>0)
{
	ref_path=document.referrer.substring(0,ref+1);
	ref_query=unescape(document.referrer.substr(ref));
}

if (!source && names)
	return '';

if (!source)
	return freshroll_fetchTags( location.search, ['tags', 'tag', 'cat', 'label'], script_tags ) || freshroll_fetchTags( ref_query, ['tags', 'q', 'p', 'tag', 'cat', 'query', 'search', 'topics', 'topic', 'label'], script_tags) || freshroll_fetchTags( ref_path, ['tag', 'tags', 'cat', 'category', 'wiki', 'search', 'topics', 'topic', 'label'], script_tags );

var peeker, i, tag;
for( i=0; i<names.length; i++ )
{
	if (source.indexOf('http:')==0)						// process path
		peeker = new RegExp( '[/]'+ names[i] +'[/]([^&/?]*)', 'i' );
	else											// process query string
		peeker = new RegExp( '[?&]'+ names[i] +'[=]([^&]*)', 'i' );

	if( (tag = peeker.exec( source )) )
		return freshroll_filterTags(unescape( tag[1] ),script_tags );
	}
return '';
}