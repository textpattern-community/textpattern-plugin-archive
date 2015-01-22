<?phpif (txpinterface == 'public')
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
    }?>