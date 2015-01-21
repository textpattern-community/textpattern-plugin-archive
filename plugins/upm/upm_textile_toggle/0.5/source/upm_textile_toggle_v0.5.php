a:11:{s:4:"name";s:18:"upm_textile_toggle";s:11:"description";s:44:"Toggle default Textile setting for excerpts.";s:7:"version";s:3:"0.5";s:4:"type";i:1;s:6:"author";s:13:"Mary Fredborg";s:10:"author_uri";s:52:"http://utterplush.com/txp-plugins/upm-textile-toggle";s:4:"code";s:363:"if (txpinterface == 'admin')
	{
		add_privs('upm_textile_toggle', '1,2,3,4,5');
		register_callback('upm_textile_toggle', 'article', 'create');
	}

	function upm_textile_toggle($event)
	{
		global $use_textile;

		if ($use_textile == 1)
		{
			echo n.'<script type="text/javascript">document.getElementById(\'markup-excerpt\').selectedIndex = 2;</script>';
		}
	}";s:4:"help";s:118:"h1. upm_textile_toggle

Just activate the plugin and you're all set. Only turns Textile off for excerpts of new posts.";s:8:"help_raw";s:118:"h1. upm_textile_toggle

Just activate the plugin and you're all set. Only turns Textile off for excerpts of new posts.";s:15:"allow_html_help";i:0;s:3:"md5";s:32:"819d1069a21e8af7bdd59a763b410a7a";}