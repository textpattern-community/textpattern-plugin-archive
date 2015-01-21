a:11:{s:4:"name";s:11:"upm_textile";s:11:"description";s:28:"Parse any text with Textile.";s:7:"version";s:3:"0.3";s:4:"type";i:1;s:6:"author";s:13:"Mary Fredborg";s:10:"author_uri";s:45:"http://utterplush.com/txp-plugins/upm-textile";s:4:"code";s:1551:"function upm_textile($atts = array(), $thing = '')
	{
		global $prefs;

		extract(lAtts(array(
			'lite'		   => '',
			'no_image'   => '',
			'parse_tags' => 1,
			'rel'			   => '',
			'strict'	   => '',
		), $atts));

		if ($thing)
		{
			@include_once(txpath.'/lib/classTextile.php');

			if (class_exists('Textile'))
			{
				$textile = new Textile();

				if ($parse_tags)
				{
					$thing = parse($thing);
				}

				return $textile->TextileThis($thing, $lite, false, $no_image, $strict, $rel);
			}

			elseif ($prefs['production_status'] != 'live')
			{
				return upm_textile_gTxt('textile_missing');
			}
		}
	}

// -------------------------------------------------------------

	function upm_textile_restricted($atts = array(), $thing = '')
	{
		global $prefs;

		extract(lAtts(array(
			'lite'		   => 1,
			'no_image'   => 1,
			'parse_tags' => 1,
			'rel'			   => 'nofollow',
		), $atts));

		if ($thing)
		{
			@include_once(txpath.'/lib/classTextile.php');

			if (class_exists('Textile'))
			{
				$textile = new Textile();

				if ($parse_tags)
				{
					$thing = parse($thing);
				}

				return $textile->TextileRestricted($thing, $lite, $no_image, $rel);
			}

			elseif ($prefs['production_status'] != 'live')
			{
				return upm_textile_gTxt('textile_missing');
			}
		}
	}

// -------------------------------------------------------------

	function upm_textile_gTxt($what, $atts = array())
	{
		$lang = array(
			'textile_missing' => 'upm_textile: Textile appears to be missing.',
		);

		return strtr($lang[$what], $atts);
	}";s:4:"help";s:5814:"<style type="text/css" media="screen,projection">
<!--
var {
padding: 0.1em;
font-style: normal;
font-weight: bold;
font-family: monospace;
background-color: #eee;
}

table.upm_tag_atts {
margin: 0 0 15px 0;
border: 1px solid #eee;
border-collapse: collapse;
}

table.upm_tag_atts caption {
padding: 0.5em;
font-weight: bold;
text-align: left;
background-color: #ddd;
}

table.upm_tag_atts tr {
border-bottom: 1px solid #eee;
}

table.upm_tag_atts th, table.upm_tag_atts td {
margin: 0;
padding: 0.5em 1em;
}

table.upm_tag_atts th {
font-weight: normal;
text-align: left;
background-color: #eee;
}
-->
</style>

<h1>upm_textile</h1>

<ol id="start">
	<li><a href="#introduction">Introduction</a></li>
	<li><a href="#tags">Tags</a></li>
	<li><a href="#usage-examples">Usage Examples</a></li>
	<li><a href="#footnotes">Footnotes</a></li>
</ol>



<h2 id="introduction">Introduction</h2>

<p>This plugin tag allows you to parse any text with Textile. Simply wrap it around
	whatever text you'd like to parse.</p>

<p><strong>Please note:</strong> this uses the same Textile version supplied with your
	copy of Textpattern and I only provide support for this plugin, not Textile
	itself. The attributes below do not add any new/improved functionality to Textile,
	merely the ability to use in-built Textile parameters.</p>

<p><a href="#start" title="Jump back contents list">&#8617;</a></p>



<h2 id="tags">Tags</h2>

<table class="upm_tag_atts" cellpadding="0" cellspacing="0">

	<caption>upm_textile</caption>

	<tr>
		<th>Name</th>
		<th>Description</th>
		<th>Required?</th>
		<th>Available Values</th>
		<th>Default Value</th>
	</tr>

	<tr>
		<td>encode</td>
		<td>Dropped. Deprecated in Textile and doesn&#8217;t make much sense. Do not use.</td>
	</tr>

	<tr>
		<td>lite</td>
		<td>Whether to exclude all code and block tags.</td>
		<td>No</td>
		<td><var>0</var> (no) or <var>1</var> (yes)</td>
		<td><var>0</var></td>
	</tr>

	<tr>
		<td>no_image</td>
		<td>Whether to strip images.</td>
		<td>No</td>
		<td><var>0</var> (leave alone) or <var>1</var> (strip images)</td>
		<td><var>0</var></td>
	</tr>

	<tr>
		<td>parse_tags</td>
		<td>Whether to parse any contained Textpattern tags.</td>
		<td>No</td>
		<td><var>0</var> (no) or <var>1</var> (yes)</td>
		<td><var>1</var></td>
	</tr>

	<tr>
		<td>rel</td>
		<td>(X)HTML <var>rel</var> attribute to apply to links.</td>
		<td>No</td>
		<td>Any valid <a href="http://www.w3.org/TR/REC-html40/types.html#type-links">(X)HTML link relationship type</a>.</td>
		<td>Unset</td>
	</tr>

	<tr>
		<td>strict</td>
		<td>Whether to strip extra whitespace.</td>
		<td>No</td>
		<td><var>0</var> (yes) or <var>1</var> (leave alone)</td>
		<td><var>0</var></td>
	</tr>
</table>

<p><a href="#start" title="Jump back contents list">&#8617;</a></p>

<table class="upm_tag_atts" cellpadding="0" cellspacing="0">

	<caption>upm_textile_restricted</caption>

	<tr>
		<th>Name</th>
		<th>Description</th>
		<th>Required?</th>
		<th>Available Values</th>
		<th>Default Value</th>
	</tr>

	<tr>
		<td>lite</td>
		<td>Whether to exclude all code and all block tags, except for paragraphs and blockquotes.</td>
		<td>No</td>
		<td><var>0</var> (leave alone) or <var>1</var> (yes)</td>
		<td><var>1</var></td>
	</tr>

	<tr>
		<td>no_image</td>
		<td>Whether to strip images.</td>
		<td>No</td>
		<td><var>0</var> (leave alone) or <var>1</var> (yes)</td>
		<td><var>1</var></td>
	</tr>

	<tr>
		<td>parse_tags</td>
		<td>Whether to parse any contained Textpattern tags.</td>
		<td>No</td>
		<td><var>0</var> (no) or <var>1</var> (yes)</td>
		<td><var>1</var></td>
	</tr>

	<tr>
		<td>rel</td>
		<td>(X)HTML <var>rel</var> attribute to apply to links.</td>
		<td>No</td>
		<td>Any valid <a href="http://www.w3.org/TR/REC-html40/types.html#type-links">(X)HTML link relationship type</a>.</td>
		<td><var>nofollow</var><sup><a href="#fn1">1</a></sup></td>
	</tr>

</table>

<p><a href="#start" title="Jump back contents list">&#8617;</a></p>


<h3>Notes On Restricted Textile</h3>

<p>Version 4.0.4 of Textpattern introduced a new function to Textile, named
	TextileRestricted. This new function does several things:</p>

<ul>
	<li>Escapes all raw (X)HTML and attempts to clean all possible XSS attempts. This requires
		stripping all <var>style</var>, <var>class</var>, <var>id</var>, <var>colspan</var> and
		<var>rowspan</var> (X)THML attributes. The <var>lang</var> attribute is still allowed.</li>

	<li>Automatically converts all plain-text urls into links.</li>

	<li>Only <var>http</var>, <var>https</var>, <var>ftp</var>, and <var>mailto</var> URI schemes
		are permitted<sup><a href="#fn1">1</a></sup>.</li>

	<li>By default, applies <var>nofollow</var> relationship
		attribute<sup><a href="#fn2">2</a></sup> to all links.</li>

	<li>By default, enforces stricter use of Textile, but still allows for the essential
		basics of paragraphs and blockquotes.</li>
</ul>

<p><a href="#start" title="Jump back contents list">&#8617;</a></p>



<h2 id="usage-examples">Usage Examples</h2>

<p><code>&lt;txp:upm_textile&gt;The text _you_ would like
	"Textiled":http://textism.com/tools/textile/.&lt;/txp:upm_textile&gt;</code></p>

<p><code>&lt;txp:upm_textile_restricted&gt;Even the output of Textpattern tags,
	such as &lt;txp:site_slogan /&gt;, can be Textiled.&lt;/txp:upm_textile_restricted&gt;</code></p>

<p><a href="#start" title="Jump back contents list">&#8617;</a></p>



<h2 id="footnotes">Footnotes</h2>

<ol>
	<li id="fn1">Wikipedia: <a href="http://en.wikipedia.org/wiki/URI_scheme">URI scheme</a></li>
	<li id="fn2">A <a href="http://google.com/search?q=nofollow">Google search query for &#8220;nofollow&#8221;</a> can
		provide all the necessary information on what this is used for.</li>
</ol>

<p><a href="#start" title="Jump back contents list">&#8617;</a></p>";s:8:"help_raw";s:5814:"<style type="text/css" media="screen,projection">
<!--
var {
padding: 0.1em;
font-style: normal;
font-weight: bold;
font-family: monospace;
background-color: #eee;
}

table.upm_tag_atts {
margin: 0 0 15px 0;
border: 1px solid #eee;
border-collapse: collapse;
}

table.upm_tag_atts caption {
padding: 0.5em;
font-weight: bold;
text-align: left;
background-color: #ddd;
}

table.upm_tag_atts tr {
border-bottom: 1px solid #eee;
}

table.upm_tag_atts th, table.upm_tag_atts td {
margin: 0;
padding: 0.5em 1em;
}

table.upm_tag_atts th {
font-weight: normal;
text-align: left;
background-color: #eee;
}
-->
</style>

<h1>upm_textile</h1>

<ol id="start">
	<li><a href="#introduction">Introduction</a></li>
	<li><a href="#tags">Tags</a></li>
	<li><a href="#usage-examples">Usage Examples</a></li>
	<li><a href="#footnotes">Footnotes</a></li>
</ol>



<h2 id="introduction">Introduction</h2>

<p>This plugin tag allows you to parse any text with Textile. Simply wrap it around
	whatever text you'd like to parse.</p>

<p><strong>Please note:</strong> this uses the same Textile version supplied with your
	copy of Textpattern and I only provide support for this plugin, not Textile
	itself. The attributes below do not add any new/improved functionality to Textile,
	merely the ability to use in-built Textile parameters.</p>

<p><a href="#start" title="Jump back contents list">&#8617;</a></p>



<h2 id="tags">Tags</h2>

<table class="upm_tag_atts" cellpadding="0" cellspacing="0">

	<caption>upm_textile</caption>

	<tr>
		<th>Name</th>
		<th>Description</th>
		<th>Required?</th>
		<th>Available Values</th>
		<th>Default Value</th>
	</tr>

	<tr>
		<td>encode</td>
		<td>Dropped. Deprecated in Textile and doesn&#8217;t make much sense. Do not use.</td>
	</tr>

	<tr>
		<td>lite</td>
		<td>Whether to exclude all code and block tags.</td>
		<td>No</td>
		<td><var>0</var> (no) or <var>1</var> (yes)</td>
		<td><var>0</var></td>
	</tr>

	<tr>
		<td>no_image</td>
		<td>Whether to strip images.</td>
		<td>No</td>
		<td><var>0</var> (leave alone) or <var>1</var> (strip images)</td>
		<td><var>0</var></td>
	</tr>

	<tr>
		<td>parse_tags</td>
		<td>Whether to parse any contained Textpattern tags.</td>
		<td>No</td>
		<td><var>0</var> (no) or <var>1</var> (yes)</td>
		<td><var>1</var></td>
	</tr>

	<tr>
		<td>rel</td>
		<td>(X)HTML <var>rel</var> attribute to apply to links.</td>
		<td>No</td>
		<td>Any valid <a href="http://www.w3.org/TR/REC-html40/types.html#type-links">(X)HTML link relationship type</a>.</td>
		<td>Unset</td>
	</tr>

	<tr>
		<td>strict</td>
		<td>Whether to strip extra whitespace.</td>
		<td>No</td>
		<td><var>0</var> (yes) or <var>1</var> (leave alone)</td>
		<td><var>0</var></td>
	</tr>
</table>

<p><a href="#start" title="Jump back contents list">&#8617;</a></p>

<table class="upm_tag_atts" cellpadding="0" cellspacing="0">

	<caption>upm_textile_restricted</caption>

	<tr>
		<th>Name</th>
		<th>Description</th>
		<th>Required?</th>
		<th>Available Values</th>
		<th>Default Value</th>
	</tr>

	<tr>
		<td>lite</td>
		<td>Whether to exclude all code and all block tags, except for paragraphs and blockquotes.</td>
		<td>No</td>
		<td><var>0</var> (leave alone) or <var>1</var> (yes)</td>
		<td><var>1</var></td>
	</tr>

	<tr>
		<td>no_image</td>
		<td>Whether to strip images.</td>
		<td>No</td>
		<td><var>0</var> (leave alone) or <var>1</var> (yes)</td>
		<td><var>1</var></td>
	</tr>

	<tr>
		<td>parse_tags</td>
		<td>Whether to parse any contained Textpattern tags.</td>
		<td>No</td>
		<td><var>0</var> (no) or <var>1</var> (yes)</td>
		<td><var>1</var></td>
	</tr>

	<tr>
		<td>rel</td>
		<td>(X)HTML <var>rel</var> attribute to apply to links.</td>
		<td>No</td>
		<td>Any valid <a href="http://www.w3.org/TR/REC-html40/types.html#type-links">(X)HTML link relationship type</a>.</td>
		<td><var>nofollow</var><sup><a href="#fn1">1</a></sup></td>
	</tr>

</table>

<p><a href="#start" title="Jump back contents list">&#8617;</a></p>


<h3>Notes On Restricted Textile</h3>

<p>Version 4.0.4 of Textpattern introduced a new function to Textile, named
	TextileRestricted. This new function does several things:</p>

<ul>
	<li>Escapes all raw (X)HTML and attempts to clean all possible XSS attempts. This requires
		stripping all <var>style</var>, <var>class</var>, <var>id</var>, <var>colspan</var> and
		<var>rowspan</var> (X)THML attributes. The <var>lang</var> attribute is still allowed.</li>

	<li>Automatically converts all plain-text urls into links.</li>

	<li>Only <var>http</var>, <var>https</var>, <var>ftp</var>, and <var>mailto</var> URI schemes
		are permitted<sup><a href="#fn1">1</a></sup>.</li>

	<li>By default, applies <var>nofollow</var> relationship
		attribute<sup><a href="#fn2">2</a></sup> to all links.</li>

	<li>By default, enforces stricter use of Textile, but still allows for the essential
		basics of paragraphs and blockquotes.</li>
</ul>

<p><a href="#start" title="Jump back contents list">&#8617;</a></p>



<h2 id="usage-examples">Usage Examples</h2>

<p><code>&lt;txp:upm_textile&gt;The text _you_ would like
	"Textiled":http://textism.com/tools/textile/.&lt;/txp:upm_textile&gt;</code></p>

<p><code>&lt;txp:upm_textile_restricted&gt;Even the output of Textpattern tags,
	such as &lt;txp:site_slogan /&gt;, can be Textiled.&lt;/txp:upm_textile_restricted&gt;</code></p>

<p><a href="#start" title="Jump back contents list">&#8617;</a></p>



<h2 id="footnotes">Footnotes</h2>

<ol>
	<li id="fn1">Wikipedia: <a href="http://en.wikipedia.org/wiki/URI_scheme">URI scheme</a></li>
	<li id="fn2">A <a href="http://google.com/search?q=nofollow">Google search query for &#8220;nofollow&#8221;</a> can
		provide all the necessary information on what this is used for.</li>
</ol>

<p><a href="#start" title="Jump back contents list">&#8617;</a></p>";s:15:"allow_html_help";i:1;s:3:"md5";s:32:"8d71ab28b2cf21e23531c5c4cbac91f3";}