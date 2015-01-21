a:11:{s:4:"name";s:11:"upm_savenew";s:11:"description";s:41:""Save New" button for articles and forms.";s:7:"version";s:5:"0.4.2";s:4:"type";i:1;s:6:"author";s:13:"Mary Fredborg";s:10:"author_uri";s:45:"http://utterplush.com/txp-plugins/upm-savenew";s:4:"code";s:1975:"if (txpinterface == 'admin')
	{
		// link to javascript
		add_privs('upm_savenew_js_link_load', '1,2,3,4,5,6');
		register_callback('upm_savenew_js_link_load', 'article', 'edit', 1);
		register_callback('upm_savenew_js_link_load', 'form', '', 1);

		// load javascript
		add_privs('upm_savenew_js', '1,2,3,4,5,6');
		register_callback('upm_savenew_js', 'upm_savenew_js', '', 1);
	}

// -------------------------------------------------------------

	function upm_savenew_js_link_load()
	{
		ob_start('upm_savenew_js_link');
	}

	function upm_savenew_js_link($buffer)
	{
		$find = '</head>';
		$replace = n.n.t.t.'<script type="text/javascript" src="index.php?event=upm_savenew_js"></script>'.n.t;

		return str_replace($find, $replace.$find, $buffer);
	}

// -------------------------------------------------------------

	function upm_savenew_js()
	{
		while (@ob_end_clean());

		$save_new = gTxt('save_new');

		header("Content-type: text/javascript");

		echo <<<js
/*
upm_savenew
*/

	$(document).ready(function() {
		// create new article submit button
		$('#page-article input[name="save"]').
			after(' <input type="submit" name="publish" value="$save_new" class="publish" />');

		// article save new button
		$('#page-article input[name="save"] + input[name="publish"]').
			// onclick...
			click(function(){
				// check reset time checkbox
				$('#reset_time').attr({
					name: 'publish_now',
					checked: true
				});

				// empty URL-only title
				$('#url-title').attr('value', '');
			});

		// create new form submit button
		$('#page-form input[name="save"]').
			after(' <input type="submit" name="savenew" value="$save_new" class="publish" />');

		// forms save new button
		$('#page-form input[name="save"] + input[name="savenew"]').
			// onclick...
			click(function(){
				// change form name from original to original_copy
				$('input[name="name"]').attr('value', $('input[name="name"]').attr('value') + '_copy');
			});
	});

js;
		exit(0);
	}";s:4:"help";s:301:"h1. upm_savenew

Once installed and activated, you're ready to go.

This plugin started as "a hack created by *grapeice925*":http://forum.textpattern.com/viewtopic.php?id=2586 of the Textpattern forum. I converted it into an admin-side plugin, and extended it to include a "Save New" button for forms.";s:8:"help_raw";s:301:"h1. upm_savenew

Once installed and activated, you're ready to go.

This plugin started as "a hack created by *grapeice925*":http://forum.textpattern.com/viewtopic.php?id=2586 of the Textpattern forum. I converted it into an admin-side plugin, and extended it to include a "Save New" button for forms.";s:15:"allow_html_help";i:0;s:3:"md5";s:32:"41ed3143534381dfd6a0f63d6c696a96";}