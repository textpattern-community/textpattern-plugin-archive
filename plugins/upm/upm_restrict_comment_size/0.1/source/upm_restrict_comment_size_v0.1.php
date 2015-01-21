a:11:{s:4:"name";s:25:"upm_restrict_comment_size";s:11:"description";s:22:"Restrict comment size.";s:7:"version";s:3:"0.1";s:4:"type";i:0;s:6:"author";s:13:"Mary Fredborg";s:10:"author_uri";s:59:"http://utterplush.com/txp-plugins/upm-restrict-comment-size";s:4:"code";s:2142:"if (txpinterface == 'public')
	{
		upm_restrict_comment_size_error();
	}

// -------------------------------------------------------------
// have to do a buffer workaround
// because of current Txp plugin limitations


	function upm_restrict_comment_size_error()
	{
		ob_start('upm_restrict_comment_size_buffer');
	}

// -------------------------------------------------------------
// make sure the textarea is marked as having an error (for styling)

	function upm_restrict_comment_size_buffer($buffer)
	{
		global $upm_restrict_comment_size_error;

		if (!empty($upm_restrict_comment_size_error))
		{
			$find = 'txpCommentInputMessage';
			$replace = 'txpCommentInputMessage comments_error';

			$buffer = str_replace($find, $replace, $buffer);
		}

		return $buffer;
	}

// -------------------------------------------------------------

	function upm_restrict_comment_size($atts = array())
	{
		extract(lAtts(array(
			'error_message' => 'Please enter between {min} and {max} {type} in your message.',
			'max'           => 1500,
			'min'           => 15,
			'type'          => 'chars',
		), $atts));

		$GLOBALS['upm_restrict_comment_size_error'] = false;

		$preview = ps('preview');

		if ($preview)
		{
			$message = ps('message');

			if ($message == '')
			{
				$in = getComment();
				$message = $in['message'];
			}

			$message = doStripTags(doDeEnt($message));

			// if the message is completely empty
			// the error condition is already handled by Txp
			if (!empty($message))
			{
				switch ($type)
				{
					case 'words':
						$size = count(explode(chr(32), $message));
					break;

					case 'chars':
					case 'characters':
					default:
						$size = strlen($message);
					break;
				}

				$condition_low = ($min > 1 and $size < $min);
				$condition_high = ($size > $max);

				if ($condition_low or $condition_high)
				{
					$evaluator =& get_comment_evaluator();

					$evaluator->add_estimate(RELOAD, 1,
						strtr($error_message, array(
							'{min}'  => $min,
							'{max}'  => $max,
							'{type}' => $type
						))
					);

					$GLOBALS['upm_restrict_comment_size_error'] = true;
				}
			}
		}
	}";s:4:"help";s:1642:"<style type="text/css" media="screen,projection">
<!--
dt {
	font-weight: bold;
}

var {
	padding: 0.1em;
	font-style: normal;
	font-weight: bold;
	font-family: monospace;
	background-color: #eee;
}

-->
</style>

<h1>upm_restrict_comment_size</h1>

<h2 id="intro-section">Intro</h2>

<p>Quite simply, this plugin allows you to more finely control the amount of text entered in any given comment.</p>


<h2 id="tag-section">New Tag</h2>

<p>This plugin provides you with a single new comment form (<var>comment_form</var>) tag. It is recommended that this tag be placed <em>before</em> the <var>comments_error</var> tag in the form.</p>

<h3>upm_restrict_comment_size</h3>

<dl>
	<dt>error_message</dt>
	<dd>The error message displayed when size restrictions are not met. max, min and type are the available variables (surrounded with <var>{</var> and <var>}</var>) which may be used.</dd>
	<dd>Required: no</dd>
	<dd>Available values: any desired text</dd>
	<dd>Default value: <var>Please enter between {min} and {max} {type} in your message.</var></dd>

	<dt>max</dt>
	<dd>The maximum amount of <var>type</var> allowed.</dd>
	<dd>Required: no</dd>
	<dd>Available values: any desired number</dd>
	<dd>Default value: <var>1500</var></dd>

	<dt>min</dt>
	<dd>The minimum of amount of <var>type</var> required.</dd>
	<dd>Required: no</dd>
	<dd>Available values: any desired number</dd>
	<dd>Default value: <var>15</var></dd>

	<dt>type</dt>
	<dd>Whether to restrict by the number of characters or words.</dd>
	<dd>Required: no</dd>
	<dd>Available values: <var>chars</var> or <var>words</var></dd>
	<dd>Default value: <var>chars</var></dd>
</dl>";s:8:"help_raw";s:1642:"<style type="text/css" media="screen,projection">
<!--
dt {
	font-weight: bold;
}

var {
	padding: 0.1em;
	font-style: normal;
	font-weight: bold;
	font-family: monospace;
	background-color: #eee;
}

-->
</style>

<h1>upm_restrict_comment_size</h1>

<h2 id="intro-section">Intro</h2>

<p>Quite simply, this plugin allows you to more finely control the amount of text entered in any given comment.</p>


<h2 id="tag-section">New Tag</h2>

<p>This plugin provides you with a single new comment form (<var>comment_form</var>) tag. It is recommended that this tag be placed <em>before</em> the <var>comments_error</var> tag in the form.</p>

<h3>upm_restrict_comment_size</h3>

<dl>
	<dt>error_message</dt>
	<dd>The error message displayed when size restrictions are not met. max, min and type are the available variables (surrounded with <var>{</var> and <var>}</var>) which may be used.</dd>
	<dd>Required: no</dd>
	<dd>Available values: any desired text</dd>
	<dd>Default value: <var>Please enter between {min} and {max} {type} in your message.</var></dd>

	<dt>max</dt>
	<dd>The maximum amount of <var>type</var> allowed.</dd>
	<dd>Required: no</dd>
	<dd>Available values: any desired number</dd>
	<dd>Default value: <var>1500</var></dd>

	<dt>min</dt>
	<dd>The minimum of amount of <var>type</var> required.</dd>
	<dd>Required: no</dd>
	<dd>Available values: any desired number</dd>
	<dd>Default value: <var>15</var></dd>

	<dt>type</dt>
	<dd>Whether to restrict by the number of characters or words.</dd>
	<dd>Required: no</dd>
	<dd>Available values: <var>chars</var> or <var>words</var></dd>
	<dd>Default value: <var>chars</var></dd>
</dl>";s:15:"allow_html_help";i:1;s:3:"md5";s:32:"9dc7833e43afb1e466aa23f0a8680ce2";}