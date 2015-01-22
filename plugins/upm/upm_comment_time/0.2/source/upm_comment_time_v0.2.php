<?phpfunction upm_comment_time($atts)
{
    global $thiscomment, $comments_dateformat;

    extract(lAtts(array(
        'format' => $comments_dateformat,
        'function' => false
    ), $atts));

    if ($format == 'since')
    {
        return since($thiscomment['time']);
    }

    if ($function == 'date')
    {
        return date($format, $thiscomment['time'] + tz_offset());
    }

    return safe_strftime($format, $thiscomment['time']);
}?>