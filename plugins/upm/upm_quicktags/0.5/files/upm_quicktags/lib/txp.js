	var write_tab = document.getElementById('write_tab');
	var forms_tab = document.getElementById('forms_tab');
	var pages_tab = document.getElementById('pages_tab');

	if (write_tab || forms_tab || pages_tab)
	{
		var css = (document.createElementNS) ?
			document.createElementNS('http://www.w3.org/1999/xhtml', 'link') :
			document.createElement('link');

		css.setAttribute('href','upm_quicktags/style.css');
		css.setAttribute('type','text/css');
		css.setAttribute('rel','stylesheet');

		document.getElementsByTagName('head')[0].appendChild(css);

		var current = false;

		if (write_tab && document.article.from_view.value == 'text')
		{
			var body = document.getElementById('body');
			var excerpt = document.getElementById('excerpt');

			current = 'body';

			upm_quicktags_toolbar(body);

			if (excerpt)
			{
				upm_quicktags_toolbar(excerpt);

				body.setAttribute('onclick', "current = 'body';");
				excerpt.setAttribute('onclick', "current = 'excerpt';");
			}
		}

		if (forms_tab)
		{
			current = 'form';
			upm_quicktags_toolbar(document.getElementById('form'));
			document.getElementById('upm_quicktags').style.width = toolbar_width;
		}

		if (pages_tab)
		{
			current = 'html';
			upm_quicktags_toolbar(document.getElementById('html'));
			document.getElementById('upm_quicktags').style.width = toolbar_width;
		}
	}