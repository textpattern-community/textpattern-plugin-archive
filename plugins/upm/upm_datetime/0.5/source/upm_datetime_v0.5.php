<?phpfunction upm_datetime($atts)
    {
        global $prefs;

        extract(lAtts(array(
            'format'     => $prefs['dateformat'],
            'function' => 'strftime',
            'gmt'             => 0,
            'lang'         => '',
        ), $atts));

        $now = time();

        switch ($function)
        {
            case 'date':
                if ($format == 'since')
                {
                    $format = 'l, F jS, Y';
                }

                return ($gmt) ? gmdate($format, $now) : date($format, $now + tz_offset());
            break;

            case 'strftime':
            default:
                if ($format == 'since')
                {
                    $format = '%A, %B '.( is_numeric(strftime('%e')) ? '%e' : '%d' ).', %Y';
                }

                return safe_strftime($format, $now, $gmt, $lang);
            break;
        }
    }?>