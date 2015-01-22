<?phpfunction upm_textile($atts = array(), $thing = '')
    {
        global $prefs;

        extract(lAtts(array(
            'lite'           => '',
            'no_image'   => '',
            'parse_tags' => 1,
            'rel'               => '',
            'strict'       => '',
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
            'lite'           => 1,
            'no_image'   => 1,
            'parse_tags' => 1,
            'rel'               => 'nofollow',
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
    }?>