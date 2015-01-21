a:11:{s:4:"name";s:13:"upm_quicktags";s:11:"description";s:62:"Implement Alex King's Quicktags for articles, forms and pages.";s:7:"version";s:3:"0.5";s:4:"type";i:1;s:6:"author";s:13:"Mary Fredborg";s:10:"author_uri";s:47:"http://utterplush.com/txp-plugins/upm-quicktags";s:4:"code";s:1754:"if (txpinterface == 'admin')
	{
		register_callback('upm_quicktags_article', 'article');
		register_callback('upm_quicktags_form', 'form');
		register_callback('upm_quicktags_page', 'page');
	}

// -------------------------------------------------------------

	function upm_quicktags_article($event)
	{
		echo n.'<script id="write_tab" type="text/javascript" src="upm_quicktags/lib/quicktags.js"></script>';
		echo n.'<script type="text/javascript" src="upm_quicktags/write.js"></script>';
		echo n.'<script type="text/javascript" src="upm_quicktags/lib/txp.js"></script>';
		echo n.'<script type="text/javascript" src="upm_quicktags/custom.js"></script>';
	}

// -------------------------------------------------------------

	function upm_quicktags_form($event)
	{
		echo n.'<script id="forms_tab" type="text/javascript" src="upm_quicktags/lib/quicktags.js"></script>';
		echo n.'<script type="text/javascript">var toolbar_width = \'368px\';</script>';
		echo n.'<script type="text/javascript" src="upm_quicktags/forms.js"></script>';
		echo n.'<script type="text/javascript" src="upm_quicktags/lib/txp.js"></script>';
		echo n.'<script type="text/javascript" src="upm_quicktags/custom.js"></script>';
	}

// -------------------------------------------------------------

	function upm_quicktags_page($event)
	{
		echo n.'<script id="pages_tab" type="text/javascript" src="upm_quicktags/lib/quicktags.js"></script>';
		echo n.'<script type="text/javascript">var toolbar_width = \'600px\';</script>';
		echo n.'<script type="text/javascript" src="upm_quicktags/pages.js"></script>';
		echo n.'<script type="text/javascript" src="upm_quicktags/lib/txp.js"></script>';
		echo n.'<script type="text/javascript" src="upm_quicktags/custom.js"></script>';
	}";s:4:"help";s:11490:"<style type="text/css" media="screen,projection">
<!--
pre, code {
background-color: #eee;
}
-->
</style>

<h1>upm_quicktags</h1>

<p>This plugin is comprised of a folder labelled &#8220;upm_quicktags&#8221;, along with the plugin code
itself. Since you&#8217;ve already installed this plugin, make sure that the &#8220;upm_quicktags&#8221;
folder is sitting inside the &#8220;textpattern&#8221; admin folder, and that you have made the plugin
active.</p>

<h2>Usage</h2>

<p>upm_quicktags can output any kind of text, be it Textile syntax, Textpattern tags, HTML, or other
bits of text you use often.</p>

<p>The plugin ships with the buttons configured to return HTML. Using HTML in Textpattern articles/posts
(&#8220;write&#8221; tab) usually means disabling, adjusting, or escaping Textile. To stop Textile
from wrapping paragraph tags around a line, you need to start the line with a space. The default buttons
for the &#8220;write&#8221; tab have been adjusted accordingly. When running the plugin with the default
settings, it should return valid xhtml markup, that still plays well with Textile.</p>

<p>It is <strong>not</strong> currently possible to switch upm_quicktags on and off per user.</p>

<h2>Further Customization (Optional)</h2>

<p>Quicktags allows easy customization of its buttons, and this plugin allows you to maintain a different
set of buttons for articles (write.js), forms (forms.js), and pages (pages.js). Additionally, you are
provided with a stylesheet (style.css), which will allow you to customize the look of your buttons and
toolbar. Together, these four files (found inside the &#8220;upm_quicktags&#8221; folder) are a powerful
combination.</p>

<h3>Button Template</h3>

<pre><code>edButtons[edButtons.length] = new edButton(id, label, tagStart, tagEnd, tooltip);
</code></pre>

<h3>Button Example</h3>

<pre><code>edButtons[edButtons.length] = new edButton('qt-str', 'str', '<strong>', '</strong>', 'Strongly Emphasize');
</code></pre>

<h3>Button Argument Explanation</h3>

<table cellpadding="2" cellspacing="2">
	<tr>
		<th>id</th>
		<td>unique id (used internally by Quicktags), any text you wish (be careful about special characters
		or really long strings)</td>
	</tr>

	<tr>
		<th>label</th>
		<td>any text you wish</td>
	</tr>

	<tr>
		<th>tagStart</th>
		<td>Opening tag, any text or html you wish (does not have to be strictly a single html tag). May be
		left empty.</td>
	</tr>

	<tr>
		<th>tagEnd</th>
		<td>Closing tag, any text or html you wish (does not have to be strictly a single html tag). May be
		left empty.</td>
	</tr>

	<tr>
		<th>tooltip</th>
		<td>Text that appears when the mouse cursor hovers over the button. May be left empty.</td>
	</tr>
</table>

<p>Adding an image instead of a button is very simple, and uses nearly identical settings.</p>

<h3>Image Button Template</h3>

<pre><code>edImgButtons[edImgButtons.length] = new edImgButton(id, src, tagStart, tagEnd, tooltip);
</code></pre>

<h3>Image Button Example</h3>

<pre><code>edImgButtons[edImgButtons.length] = new edImgButton('qt-bold', 'bold.gif', '<strong>', '</strong>', 'Bold');
</code></pre>

<h3>Button Argument Explanation</h3>

<p>The only difference in setting is that it takes the argument, &#8220;src&#8221; in place of the
argument &#8220;label&#8221;.</p>

<table cellpadding="2" cellspacing="2">
	<tr>
		<th>src</th>
		<td>image filename. Images need to be be placed into the &#8220;img&#8221; sub-folder of &#8220;quicktags&#8221;.</td>
	</tr>
</table>

<h3>Link Template</h3>

<pre><code>edLinks[edLinks.length] = new edLink(text, url, target, rel, tooltip);
</code></pre>

<h3>Link Example</h3>

<pre><code>edLinks[edLinks.length] = new edLink('Textpattern', 'http://textpattern.com/', '', 'I &#38;#9829; Textpattern');
</code></pre>

h3. Link Argument Explanation

<table cellpadding="2" cellspacing="2">
	<tr>
		<th>text</th>
		<td>Link title, any text you wish.</td>
	</tr>

	<tr>
		<th>url</th>
		<td>Link url.</td>
	</tr>

	<tr>
		<th>target</th>
		<td>Link target window. May be left empty.</td>
	</tr>

	<tr>
		<th>rel</th>
		<td>Define link relationship. May be left empty.</td>
	</tr>

	<tr>
		<th>tooltip</th>
		<td>Text that appears when the mouse cursor hovers over the button. May be left empty.</td>
	</tr>
</table>

<p>Look through the Quicktag configuration files (write.js, forms.js and pages.js) for further
examples.</p>

<h3>&#8220;Special&#8221; Buttons</h3>

<p>Seven of the default buttons are marked &#8220;special&#8221;. This means that they are programmed
to do special user interaction, and therefore only their label and/or tooltip may be customized.</p>

<h4>Further Info</h4>

<ul>
	<li><strong>qt-spacer</strong>: its purpose is to cause visual separation of buttons, so you may have the
	buttons appear in groups, rather than immediately side-by-side.
	<li><strong>qt-spacer-br</strong>: its purpose is to cause visual separation of buttons, starts a new row
	(thanks Joshua!)</li>
	<li><strong>qt-image</strong>: inserts an image.</li>
	<li><strong>qt-img-popper</strong>: ties upm_quicktags to upm_img_popper plugin (you have to have upm_img_popper
	installed and activated for this to work)</li>
	<li><strong>qt-link</strong>: inserts a link.</li>
	<li><strong>qt-spell</strong>: looks up a word in the dictionary.</li>
	<li><strong>qt-close</strong>: closes any and all open tags.</li>
</ul>

<h3>Entities Gotcha</h3>

<p>Should you desire to use numeric entities (special characters, like a copyright sign, for example) in
a button&#8217;s label, you will need to enter them as their ISO 10646 (&#8220;Unicode&#8221;) character
numbers (for the label only). A short list of common entities has been provided below.
<a href="http://www.w3.org/TR/html4/sgml/entities.html">A more complete list is available</a>, and a
converter has been included here to help you as well. This is not required for the actual text that the
buttons insert.</p>

<h4>Character and Entity Convertor</h4>

<script type="text/javascript" src="upm_quicktags/lib/converter.js"></script>

<form action="">
<fieldset>
	<legend>Character to Unicode</legend>

	<p>Example: &#169; (a copyright sign)</p>
	<label for="symbol">Character</label>: <input type="text" id="symbol" name="symbol" value="©" /><br />

	<label for="symbol_out">Unicode</label>: <input type="text" id="symbol_out" name="symbol_out" value="" /><br />
	<input type="submit" value="Convert" onclick="fromChar(); return false;" />
</fieldset>
</form>

<form action="">
<fieldset>
	<legend>ASCII to Unicode</legend>

	<p>Example: 169 (a copyright sign)</p>
	<label for="asc">Entity</label>: <input type="text" id="asc" name="asc" value="169" /><br />
	<label for="asc_out">Unicode</label>: <input type="text" id="asc_out" name="asc_out" value="" /><br />

	<input type="submit" value="Convert" onclick="fromAsc(); return false;" />
</fieldset>
</form>

<h4>Common Entities</h4>

<table cellpadding="5" cellspacing="5" border="0" id="list">
	<tr>
		<th>Name</th>
		<th>Character</th>
		<th>Numeric Entity</th>
		<th>Unicode</th>
	</tr>

	<tr>
		<td>ampersand</td>
		<td>&#38;</td>
		<td><code>&#38;#38;</code></td>
		<td><code>\u0026</code></td>
	</tr>

	<tr>
		<td>copyright</td>
		<td>&#169;</td>
		<td><code>&#38;#169;</code></td>
		<td><code>\u00a9</code></td>
	</tr>

	<tr>
		<td>en dash</td>
		<td>&#8211;</td>
		<td><code>&#38;#8211;</code></td>
		<td><code>\u2013</code></td>
	</tr>

	<tr>
		<td>em dash</td>
		<td>&#8212;</td>
		<td><code>&#38;#8212;</code></td>
		<td><code>\u2014</code></td>
	</tr>

	<tr>
		<td>left single quotation mark</td>
		<td>&#8216;</td>
		<td>&#38;#8216;</td>
		<td><code>\u2018</code></td>
	</tr>

	<tr>
		<td>right single quotation mark</td>
		<td>&#8217;</td>
		<td><code>&#38;#8217;</code></td>
		<td><code>\u2019</code></td>
	</tr>

	<tr>
		<td>left double quotation mark</td>
		<td>&#8220;</td>
		<td><code>&#38;#8220;</code></td>
		<td><code>\u201c</code></td>
	</tr>

	<tr>
		<td>right double quotation mark</td>
		<td>&#8221;</td>
		<td><code>&#38;#8221;</code></td>
		<td><code>\u201d</code></td>
	</tr>
</table>

<h2>Extra Settings</h2>

<p>You may leave these extra setting lines out of your config files altogether, and the plugin will use
default values.</p>

<h3>xhtml</h3>

<p>All three config. files contain this line:</p>

<pre><code>xhtml = 'yes';
</code></pre>

<p>By default, the special insert image button outputs xhtml. If you are using html in your site, simply
change this setting to &#8216;no&#8217;.</p>

<h3>showLinks</h3>

<p>All three config. files contain a similar line:</p>

<pre><code>showLinks = 'yes';
</code></pre>

<p>One of Quicktag&#8217;s features is the ability to quickly add markup for frequently referenced urls,
which appear in a dropdown menu. Should you wish to turn this feature off, change the value to
&#8216;no&#8217;.</p>

<h3>toolbar_width</h3>

<p>The form (forms.js) and page (pages.js) config. files contain a similar line:</p>

<pre><code>var toolbar_width = '600px';
</code></pre>

<p>Its function is to wrap the toolbar to match the textarea&#8217;s size. While I could hard-code this
information, I decided to make it a setting, in case of a user mod or plugin changing the textarea&#8217;s
size. If this happens, change the width to match the new size of the textarea.</p>

<h2>Advanced Enhancements</h2>

<p>upm_quicktags allows you to extend it without hacks. Simply add your own JavaScript to the supplied
custom.js file. You can use your own functions and create your own buttons, but still make use of the
generic functions supplied. You can use any, all, or none of the default buttons. It&#8217;s all up to
your imagination and your scripting skills.</p>

<p>This is considered advanced, and is intended to be used by those who know JavaScript. I can&#8217;t
promise to support whatever changes you make, so don&#8217;t do this unless you&#8217;re prepared to
handle it yourself.</p>

<h2>Supported Browsers</h2>

<p>Please notify me of any browsers this plugin does or does not work in, if it has not already been
listed below.</p>

<p>This plugin is known to work in: Firefox 1.0.2 (Win) and Internet Explorer 6 SP2 (Win). It has almost
seamless support in Opera 8 (Win), but limited support in the versions preceeding it.</p>

<h2>Suggestions</h2>

<p>I am always willing to take suggestions for this plugin. Suggested changes for the default settings and
buttons shipped with this plugin may be used, if it is felt that a large number of users would desire it.
Nevertheless, feel free to share your Quicktags configuration with other users.</p>

<h2>Credits</h2>

<p><a href="http://www.alexking.org/">Alex King</a> wrote Quicktags, and released it under the
<a href="http://gnu.org/copyleft/lesser.html">LGPL license</a>.</p>

<p>Much thanks goes to:</p>

<ul>
	<li><a href="http://www.hakjoon.com/">Patrick Woods</a> (aka &#8220;hakjoon&#8221;)</li>
	<li><a href="http://thresholdstate.com/">Alex Shiels</a> (aka &#8220;zem&#8221;)</li>
	<li><a href="http://micampe.it/log">Michele Campeotto</a> (aka &#8220;micampe&#8221;)</li>
	<li><a href="http://www.wilshireone.com/">Rob Sable</a> (aka &#8220;wilshire&#8221;)</li>
</ul>

<p>I studied their examples and plugins in order to make this one. Particular thanks goes to Patrick:
I&#8217;ve made use of his modified version of Quicktags.</p>

<p>Additional thanks goes to the several users who &#8220;beta-tested&#8221; this plugin, offered their
ideas, and helped me iron out the wrinkles.&#8212;Mary</p>";s:8:"help_raw";s:11490:"<style type="text/css" media="screen,projection">
<!--
pre, code {
background-color: #eee;
}
-->
</style>

<h1>upm_quicktags</h1>

<p>This plugin is comprised of a folder labelled &#8220;upm_quicktags&#8221;, along with the plugin code
itself. Since you&#8217;ve already installed this plugin, make sure that the &#8220;upm_quicktags&#8221;
folder is sitting inside the &#8220;textpattern&#8221; admin folder, and that you have made the plugin
active.</p>

<h2>Usage</h2>

<p>upm_quicktags can output any kind of text, be it Textile syntax, Textpattern tags, HTML, or other
bits of text you use often.</p>

<p>The plugin ships with the buttons configured to return HTML. Using HTML in Textpattern articles/posts
(&#8220;write&#8221; tab) usually means disabling, adjusting, or escaping Textile. To stop Textile
from wrapping paragraph tags around a line, you need to start the line with a space. The default buttons
for the &#8220;write&#8221; tab have been adjusted accordingly. When running the plugin with the default
settings, it should return valid xhtml markup, that still plays well with Textile.</p>

<p>It is <strong>not</strong> currently possible to switch upm_quicktags on and off per user.</p>

<h2>Further Customization (Optional)</h2>

<p>Quicktags allows easy customization of its buttons, and this plugin allows you to maintain a different
set of buttons for articles (write.js), forms (forms.js), and pages (pages.js). Additionally, you are
provided with a stylesheet (style.css), which will allow you to customize the look of your buttons and
toolbar. Together, these four files (found inside the &#8220;upm_quicktags&#8221; folder) are a powerful
combination.</p>

<h3>Button Template</h3>

<pre><code>edButtons[edButtons.length] = new edButton(id, label, tagStart, tagEnd, tooltip);
</code></pre>

<h3>Button Example</h3>

<pre><code>edButtons[edButtons.length] = new edButton('qt-str', 'str', '<strong>', '</strong>', 'Strongly Emphasize');
</code></pre>

<h3>Button Argument Explanation</h3>

<table cellpadding="2" cellspacing="2">
	<tr>
		<th>id</th>
		<td>unique id (used internally by Quicktags), any text you wish (be careful about special characters
		or really long strings)</td>
	</tr>

	<tr>
		<th>label</th>
		<td>any text you wish</td>
	</tr>

	<tr>
		<th>tagStart</th>
		<td>Opening tag, any text or html you wish (does not have to be strictly a single html tag). May be
		left empty.</td>
	</tr>

	<tr>
		<th>tagEnd</th>
		<td>Closing tag, any text or html you wish (does not have to be strictly a single html tag). May be
		left empty.</td>
	</tr>

	<tr>
		<th>tooltip</th>
		<td>Text that appears when the mouse cursor hovers over the button. May be left empty.</td>
	</tr>
</table>

<p>Adding an image instead of a button is very simple, and uses nearly identical settings.</p>

<h3>Image Button Template</h3>

<pre><code>edImgButtons[edImgButtons.length] = new edImgButton(id, src, tagStart, tagEnd, tooltip);
</code></pre>

<h3>Image Button Example</h3>

<pre><code>edImgButtons[edImgButtons.length] = new edImgButton('qt-bold', 'bold.gif', '<strong>', '</strong>', 'Bold');
</code></pre>

<h3>Button Argument Explanation</h3>

<p>The only difference in setting is that it takes the argument, &#8220;src&#8221; in place of the
argument &#8220;label&#8221;.</p>

<table cellpadding="2" cellspacing="2">
	<tr>
		<th>src</th>
		<td>image filename. Images need to be be placed into the &#8220;img&#8221; sub-folder of &#8220;quicktags&#8221;.</td>
	</tr>
</table>

<h3>Link Template</h3>

<pre><code>edLinks[edLinks.length] = new edLink(text, url, target, rel, tooltip);
</code></pre>

<h3>Link Example</h3>

<pre><code>edLinks[edLinks.length] = new edLink('Textpattern', 'http://textpattern.com/', '', 'I &#38;#9829; Textpattern');
</code></pre>

h3. Link Argument Explanation

<table cellpadding="2" cellspacing="2">
	<tr>
		<th>text</th>
		<td>Link title, any text you wish.</td>
	</tr>

	<tr>
		<th>url</th>
		<td>Link url.</td>
	</tr>

	<tr>
		<th>target</th>
		<td>Link target window. May be left empty.</td>
	</tr>

	<tr>
		<th>rel</th>
		<td>Define link relationship. May be left empty.</td>
	</tr>

	<tr>
		<th>tooltip</th>
		<td>Text that appears when the mouse cursor hovers over the button. May be left empty.</td>
	</tr>
</table>

<p>Look through the Quicktag configuration files (write.js, forms.js and pages.js) for further
examples.</p>

<h3>&#8220;Special&#8221; Buttons</h3>

<p>Seven of the default buttons are marked &#8220;special&#8221;. This means that they are programmed
to do special user interaction, and therefore only their label and/or tooltip may be customized.</p>

<h4>Further Info</h4>

<ul>
	<li><strong>qt-spacer</strong>: its purpose is to cause visual separation of buttons, so you may have the
	buttons appear in groups, rather than immediately side-by-side.
	<li><strong>qt-spacer-br</strong>: its purpose is to cause visual separation of buttons, starts a new row
	(thanks Joshua!)</li>
	<li><strong>qt-image</strong>: inserts an image.</li>
	<li><strong>qt-img-popper</strong>: ties upm_quicktags to upm_img_popper plugin (you have to have upm_img_popper
	installed and activated for this to work)</li>
	<li><strong>qt-link</strong>: inserts a link.</li>
	<li><strong>qt-spell</strong>: looks up a word in the dictionary.</li>
	<li><strong>qt-close</strong>: closes any and all open tags.</li>
</ul>

<h3>Entities Gotcha</h3>

<p>Should you desire to use numeric entities (special characters, like a copyright sign, for example) in
a button&#8217;s label, you will need to enter them as their ISO 10646 (&#8220;Unicode&#8221;) character
numbers (for the label only). A short list of common entities has been provided below.
<a href="http://www.w3.org/TR/html4/sgml/entities.html">A more complete list is available</a>, and a
converter has been included here to help you as well. This is not required for the actual text that the
buttons insert.</p>

<h4>Character and Entity Convertor</h4>

<script type="text/javascript" src="upm_quicktags/lib/converter.js"></script>

<form action="">
<fieldset>
	<legend>Character to Unicode</legend>

	<p>Example: &#169; (a copyright sign)</p>
	<label for="symbol">Character</label>: <input type="text" id="symbol" name="symbol" value="©" /><br />

	<label for="symbol_out">Unicode</label>: <input type="text" id="symbol_out" name="symbol_out" value="" /><br />
	<input type="submit" value="Convert" onclick="fromChar(); return false;" />
</fieldset>
</form>

<form action="">
<fieldset>
	<legend>ASCII to Unicode</legend>

	<p>Example: 169 (a copyright sign)</p>
	<label for="asc">Entity</label>: <input type="text" id="asc" name="asc" value="169" /><br />
	<label for="asc_out">Unicode</label>: <input type="text" id="asc_out" name="asc_out" value="" /><br />

	<input type="submit" value="Convert" onclick="fromAsc(); return false;" />
</fieldset>
</form>

<h4>Common Entities</h4>

<table cellpadding="5" cellspacing="5" border="0" id="list">
	<tr>
		<th>Name</th>
		<th>Character</th>
		<th>Numeric Entity</th>
		<th>Unicode</th>
	</tr>

	<tr>
		<td>ampersand</td>
		<td>&#38;</td>
		<td><code>&#38;#38;</code></td>
		<td><code>\u0026</code></td>
	</tr>

	<tr>
		<td>copyright</td>
		<td>&#169;</td>
		<td><code>&#38;#169;</code></td>
		<td><code>\u00a9</code></td>
	</tr>

	<tr>
		<td>en dash</td>
		<td>&#8211;</td>
		<td><code>&#38;#8211;</code></td>
		<td><code>\u2013</code></td>
	</tr>

	<tr>
		<td>em dash</td>
		<td>&#8212;</td>
		<td><code>&#38;#8212;</code></td>
		<td><code>\u2014</code></td>
	</tr>

	<tr>
		<td>left single quotation mark</td>
		<td>&#8216;</td>
		<td>&#38;#8216;</td>
		<td><code>\u2018</code></td>
	</tr>

	<tr>
		<td>right single quotation mark</td>
		<td>&#8217;</td>
		<td><code>&#38;#8217;</code></td>
		<td><code>\u2019</code></td>
	</tr>

	<tr>
		<td>left double quotation mark</td>
		<td>&#8220;</td>
		<td><code>&#38;#8220;</code></td>
		<td><code>\u201c</code></td>
	</tr>

	<tr>
		<td>right double quotation mark</td>
		<td>&#8221;</td>
		<td><code>&#38;#8221;</code></td>
		<td><code>\u201d</code></td>
	</tr>
</table>

<h2>Extra Settings</h2>

<p>You may leave these extra setting lines out of your config files altogether, and the plugin will use
default values.</p>

<h3>xhtml</h3>

<p>All three config. files contain this line:</p>

<pre><code>xhtml = 'yes';
</code></pre>

<p>By default, the special insert image button outputs xhtml. If you are using html in your site, simply
change this setting to &#8216;no&#8217;.</p>

<h3>showLinks</h3>

<p>All three config. files contain a similar line:</p>

<pre><code>showLinks = 'yes';
</code></pre>

<p>One of Quicktag&#8217;s features is the ability to quickly add markup for frequently referenced urls,
which appear in a dropdown menu. Should you wish to turn this feature off, change the value to
&#8216;no&#8217;.</p>

<h3>toolbar_width</h3>

<p>The form (forms.js) and page (pages.js) config. files contain a similar line:</p>

<pre><code>var toolbar_width = '600px';
</code></pre>

<p>Its function is to wrap the toolbar to match the textarea&#8217;s size. While I could hard-code this
information, I decided to make it a setting, in case of a user mod or plugin changing the textarea&#8217;s
size. If this happens, change the width to match the new size of the textarea.</p>

<h2>Advanced Enhancements</h2>

<p>upm_quicktags allows you to extend it without hacks. Simply add your own JavaScript to the supplied
custom.js file. You can use your own functions and create your own buttons, but still make use of the
generic functions supplied. You can use any, all, or none of the default buttons. It&#8217;s all up to
your imagination and your scripting skills.</p>

<p>This is considered advanced, and is intended to be used by those who know JavaScript. I can&#8217;t
promise to support whatever changes you make, so don&#8217;t do this unless you&#8217;re prepared to
handle it yourself.</p>

<h2>Supported Browsers</h2>

<p>Please notify me of any browsers this plugin does or does not work in, if it has not already been
listed below.</p>

<p>This plugin is known to work in: Firefox 1.0.2 (Win) and Internet Explorer 6 SP2 (Win). It has almost
seamless support in Opera 8 (Win), but limited support in the versions preceeding it.</p>

<h2>Suggestions</h2>

<p>I am always willing to take suggestions for this plugin. Suggested changes for the default settings and
buttons shipped with this plugin may be used, if it is felt that a large number of users would desire it.
Nevertheless, feel free to share your Quicktags configuration with other users.</p>

<h2>Credits</h2>

<p><a href="http://www.alexking.org/">Alex King</a> wrote Quicktags, and released it under the
<a href="http://gnu.org/copyleft/lesser.html">LGPL license</a>.</p>

<p>Much thanks goes to:</p>

<ul>
	<li><a href="http://www.hakjoon.com/">Patrick Woods</a> (aka &#8220;hakjoon&#8221;)</li>
	<li><a href="http://thresholdstate.com/">Alex Shiels</a> (aka &#8220;zem&#8221;)</li>
	<li><a href="http://micampe.it/log">Michele Campeotto</a> (aka &#8220;micampe&#8221;)</li>
	<li><a href="http://www.wilshireone.com/">Rob Sable</a> (aka &#8220;wilshire&#8221;)</li>
</ul>

<p>I studied their examples and plugins in order to make this one. Particular thanks goes to Patrick:
I&#8217;ve made use of his modified version of Quicktags.</p>

<p>Additional thanks goes to the several users who &#8220;beta-tested&#8221; this plugin, offered their
ideas, and helped me iron out the wrinkles.&#8212;Mary</p>";s:15:"allow_html_help";i:1;s:3:"md5";s:32:"621b596ed2686af66f24c1522f51f792";}