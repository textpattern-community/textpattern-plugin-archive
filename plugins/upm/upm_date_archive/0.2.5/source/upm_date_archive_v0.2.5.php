<?phpupm_date_archive_install();

    if (txpinterface == 'admin')
    {
        add_privs('upm_date_archive', '1,2,3,6');
        register_tab('extensions', 'upm_date_archive', 'upm_date_archive');
        register_callback('upm_date_archive_prefs', 'upm_date_archive');
    }

    else
    {
        if ($GLOBALS['permlink_mode'] != 'messy')
        {
            register_callback('upm_date_archive_load', 'textpattern');
        }
    }

// -------------------------------------------------------------

    function upm_date_menu($atts)
    {
        global $prefs, $pretext;

        extract(lAtts(array(
            'category'                        => '',
            'class'                                => '',
            'exclude_category'        => '',
            'exclude_section'            => '',
            'id'                                    => '',
            'include_expired'     => ($prefs['publish_expired_articles'] ? 'yes' : 'no'),
            'insert_empty_months' => 'yes',
            'link_to'                            => '',
            'month_format'                => '%b',
            'remain_linked'                => 'yes',
            'section'                            => $pretext['s'],
            'show_article_count'  => 'no',
            'sort'                                => 'the_year desc, the_month asc',
            'time'                                => 'past',
            'year_format'                    => '%Y'
        ), $atts));

        // ----------------------------
        // prepare query

        $offset = tz_offset();

        if ($show_article_count == 'yes')
        {
            $count_sql = ",count(id) as num";
        }

        else
        {
            $count_sql = '';
        }

        switch ($time)
        {
            case 'any':
                $time = '';
            break;

            case 'future':
                $time = "and Posted > now()";
            break;

            default:
                $time = "and Posted <= now()";
            break;
        }

        if ($include_expired == 'no')
        {
            $time .= " and (now() <= Expires or Expires = ".NULLDATETIME.")";
        }

        list($section_sql, $category_sql) = upm_date_archive_query_build($section, $exclude_section, $category, $exclude_category);

        // ----------------------------

        $rs = safe_rows_start("unix_timestamp(date_add(Posted, interval '$offset' second)) as the_date,
            date_format(date_add(Posted, interval '$offset' second), '%m') as the_month,
            date_format(date_add(Posted, interval '$offset' second), '%Y') as the_year
            $count_sql",
            'textpattern',
            "Status = 4
            $time
            $section_sql $category_sql
            group by date_format(date_add(Posted, interval '$offset' second), '%Y-%m')
            order by ".doSlash($sort));

        if ($rs)
        {
            $available_months = array(
                1 => 'Jan 01',
                2 => 'Feb 01',
                3 => 'Mar 01',
                4 => 'Apr 01',
                5 => 'May 01',
                6 => 'Jun 01',
                7 => 'Jul 01',
                8 => 'Aug 01',
                9 => 'Sep 01',
                10 => 'Oct 01',
                11 => 'Nov 01',
                12 => 'Dec 01'
            );

            $working_month = '';
            $working_year = '';

            $out = array();

            // ----------------------------

            while ($row = nextRow($rs))
            {
                extract($row);

                $the_month = intval(ltrim($the_month, '0'));

                if ($the_year != $working_year)
                {
                    if ($insert_empty_months == 'yes' and !empty($working_month) and !empty($working_year))
                    {
                        for ($i = $working_month + 1; $i <= 12; $i++)
                        {
                            $out[] = n.t.'<dd>'.strftime($month_format, strtotime($available_months[$i].' '.$the_year)).'</dd>';
                        }
                    }

                    $out[] = n.'<dt>'.strftime($year_format, $the_date).'</dt>';

                    if ($insert_empty_months == 'yes' and !empty($working_month) and $the_month != 1)
                    {
                        for ($i = 1; $i < $the_month; $i++)
                        {
                            $out[] = n.t.'<dd>'.strftime($month_format, strtotime($available_months[$i].' '.$the_year)).'</dd>';
                        }
                    }

                    $working_year = $the_year;
                }

                if ($insert_empty_months == 'yes')
                {
                    if (empty($working_month))
                    {
                        for ($i = 1; $i < $the_month; $i++)
                        {
                            $out[] = n.t.'<dd>'.strftime($month_format, strtotime($available_months[$i].' '.$the_year)).'</dd>';
                        }
                    }

                    elseif ($working_month + 1 != $the_month)
                    {
                        for ($i = $working_month + 1; $i < $the_month; $i++)
                        {
                            $out[] = n.t.'<dd>'.strftime($month_format, strtotime($available_months[$i].' '.$the_year)).'</dd>';
                        }
                    }
                }

                $what = strftime($month_format, $the_date);

                if ($remain_linked == 'yes')
                {
                    if ($link_to == false)
                    {
                        $link_to = $pretext['s'];
                    }

                    if ($link_to == 'default')
                    {
                        $url = '?month=';
                    }

                    else
                    {
                        if ($prefs['permlink_mode'] == 'messy')
                        {
                            $url = '?s='.$link_to.a.'month=';
                        }

                        else
                        {
                            $url = hu.$link_to.'/';
                        }
                    }

                    $what = '<a href="'.$url.strftime('%Y-%m', $the_date).'">'.$what.'</a>';
                }

                if (strftime('%Y-%m', $the_date) == $pretext['month'])
                {
                    $out[] = n.t.'<dd class="active">'.$what.($show_article_count == 'yes' ? " ($num)" : '').'</dd>';
                }

                else
                {
                    $out[] = n.t.'<dd>'.$what.($show_article_count == 'yes' ? " ($num)" : '').'</dd>';
                }

                $working_month = $the_month;
            }

            // ----------------------------

            if ($out)
            {
                if ($insert_empty_months == 'yes' and $working_month < 12)
                {
                    for ($i = $working_month + 1; $i <= 12; $i++)
                    {
                        $out[] = n.t.'<dd>'.strftime($month_format, strtotime($available_months[$i].' '.$the_year)).'</dd>';
                    }
                }

                $out = join('', $out);

                return doTag($out, 'dl', $class, '', $id);
            }
        }

        return;
    }

// -------------------------------------------------------------

    function upm_date_archive($atts, $thing = null)
    {
        // since gAtt is deprecated...
        $mode = isset($atts['mode']) ? $atts['mode'] : 'smart';

        // prevent lAtts errors later on
        unset($atts['mode']);

        switch ($mode)
        {
            case 'full':
                return upm_date_archive_full($atts, $thing);
            break;

            case 'smart':
            default:
                return upm_date_archive_smart($atts, $thing);
            break;
        }
    }

// -------------------------------------------------------------

    function upm_date_archive_smart($atts, $thing)
    {
        global $prefs, $pretext;

        if ($pretext['month'])
        {
            $month = $pretext['month'];
        }

        else
        {
            return;
        }

        // ----------------------------

        extract(lAtts(array(
            'category'                 => '',
            'date_format'             => (is_numeric(strftime('%e')) ? '%e' : '%d'),
            'exclude_category' => '',
            'exclude_section'     => '',
            'form'                         => '',
            'heading_class'         => '',
            'heading_format'     => '%B %Y',
            'heading_id'             => '',
            'heading_level'         => '1',
            'include_date'         => 'yes',
            'include_expired'  => ($prefs['publish_expired_articles'] ? 'yes' : 'no'),
            'list_class'             => '',
            'list_id'                     => '',
            'none_found'             => '',
            'section'                     => $pretext['s'],
            'sort'                         => 'the_date asc',
            'time'                         => 'past'
        ), $atts));

        // ----------------------------
        // prepare query

        $offset = tz_offset();

        switch ($time)
        {
            case 'any':
                $time = '';
            break;

            case 'future':
                $time = " and Posted > now()";
            break;

            case 'past':
            default:
                $time = " and Posted <= now()";
            break;
        }

        if ($include_expired == 'no')
        {
            $time .= " and (now() <= Expires or Expires = ".NULLDATETIME.")";
        }

        list($section_sql, $category_sql) = upm_date_archive_query_build($section, $exclude_section, $category, $exclude_category);

        list($year, $month) = explode('-', $month);

        // ----------------------------

        $rs = safe_rows_start("*, unix_timestamp(Posted) as uPosted, unix_timestamp(Expires) as uExpires, unix_timestamp(LastMod) as uLastMod,
            unix_timestamp(date_add(Posted, interval '$offset' second)) as the_date,
            date_format(date_add(Posted, interval '$offset' second), '%d') as the_day,
            date_format(date_add(Posted, interval '$offset' second), '%m') as the_month,
            date_format(date_add(Posted, interval '$offset' second), '%Y') as the_year",
            'textpattern',
            "Status = 4
            $time
            $section_sql $category_sql and
            date_format(date_add(Posted, interval '$offset' second), '%Y') = '".doSlash($year)."' and
            date_format(date_add(Posted, interval '$offset' second), '%m') = '".doSlash($month)."'
            order by ".doSlash($sort));

        if (!$rs)
        {
            return;
        }

        $post_count = numRows($rs);

        if (!$post_count)
        {
            return n.graf(upm_date_archive_gTxt('no_month_archive'));
        }

        // ----------------------------
        // display results

        $heading_id = ($heading_id) ? ' id="'.$heading_id.'"' : '';
        $heading_class = ($heading_class) ? ' class="'.$heading_class.'"' : '';

        $list_type = ($include_date == 'yes') ? 'dl' : 'ul';
        $list_id = ($list_id) ? ' id="'.$list_id.'"' : '';
        $list_class = ($list_class) ? ' class="'.$list_class.'"' : '';

        if ($form)
        {
            $what = fetch_form($form);
        }

        elseif ($thing)
        {
            $what = $thing;
        }

        $i = 0;

        $current_day = '';

        while ($row = nextRow($rs))
        {
            extract($row);

            if ($include_date == 'yes' && $the_day != $current_day)
            {
                $out[] = n.'<dt>'.strftime($date_format, $the_date).'</dt>';

                $current_day = $the_day;
            }

            if ($form or $thing)
            {
                ++$i;

                $row['uPosted'] = $the_date;

                populateArticleData($row);

                $GLOBALS['thisarticle']['is_first'] = ($i == 1);
                $GLOBALS['thisarticle']['is_last'] = ($i == $post_count);

                $contents = parse($what);

                unset($GLOBALS['thisarticle']);
            }

            else
            {
                $contents = '<a rel="bookmark" href="'.permlinkurl_id($ID).'">'.$Title.'</a>';
            }

            $out[] = ($include_date == 'yes') ?
                '<dd>'.$contents.'</dd>' :
                '<li>'.$contents.'</li>';
        }

        return n.tag(strftime($heading_format, $the_date), 'h'.$heading_level, $heading_id.$heading_class).
            n.n.'<'.$list_type.$list_id.$list_class.'>'.
            n.t.join(n.t, $out).
            n.'</'.$list_type.'>';
    }

// -------------------------------------------------------------

    function upm_date_archive_full($atts, $thing)
    {
        global $prefs, $pretext;

        extract(lAtts(array(
            'category'                 => '',
            'date_format'             => (is_numeric(strftime('%e')) ? '%e' : '%d'),
            'exclude_category' => '',
            'exclude_section'     => '',
            'form'                         => '',
            'heading_class'         => '',
            'heading_format'     => '%B %Y',
            'heading_id'             => '',
            'heading_level'         => 2,
            'include_date'         => 'yes',
            'include_expired'  => ($prefs['publish_expired_articles'] ? 'yes' : 'no'),
            'list_class'             => '',
            'list_id'                     => '',
            'section'                     => $pretext['s'],
            'sort'                         => 'the_year desc, the_month desc, the_date asc',
            'time'                         => 'past'
        ), $atts));

        // ----------------------------
        // prepare query

        $offset = tz_offset();

        switch ($time)
        {
            case 'any':
                $time = '';
            break;

            case 'future':
                $time = " and Posted > now()";
            break;

            default:
                $time = " and Posted <= now()";
            break;
        }

        if ($include_expired == 'no')
        {
            $time .= " and (now() <= Expires or Expires = ".NULLDATETIME.")";
        }

        list($section_sql, $category_sql) = upm_date_archive_query_build($section, $exclude_section, $category, $exclude_category);

        // ----------------------------

        $rs = safe_rows_start("*, unix_timestamp(Posted) as uPosted, unix_timestamp(Expires) as uExpires, unix_timestamp(LastMod) as uLastMod,
            unix_timestamp(date_add(Posted, interval '$offset' second)) as the_date,
            date_format(date_add(Posted, interval '$offset' second), '%d') as the_day,
            date_format(date_add(Posted, interval '$offset' second), '%m') as the_month,
            date_format(date_add(Posted, interval '$offset' second), '%m %Y') as month_year,
            date_format(date_add(Posted, interval '$offset' second), '%Y') as the_year",
            'textpattern',
            "Status = 4
            $time
            $section_sql $category_sql
            order by ".doSlash($sort));

        if (!$rs)
        {
            return;
        }

        $post_count = numRows($rs);

        if (!$post_count)
        {
            return;
        }

        // ----------------------------
        // display results

        $heading_id = ($heading_id) ? ' id="'.$heading_id.'"' : '';
        $heading_class = ($heading_class) ? ' class="'.$heading_class.'"' : '';

        $list_id = ($list_id) ? ' id="'.$list_id.'"' : '';
        $list_class = ($list_class) ? ' class="'.$list_class.'"' : '';

        if ($form)
        {
            $what = fetch_form($form);
        }

        elseif ($thing)
        {
            $what = $thing;
        }

        $out = array();

        $current_month = '';
        $current_day = '';

        $i = 0;

        while ($row = nextRow($rs))
        {
            extract($row);

            if ($month_year != $current_month)
            {
                if (!empty($current_month))
                {
                    $out[] = ($include_date == 'yes') ?
                        n.'</dl>' :
                        n.'</ul>';
                }

                $out[] = n.n.tag(strftime($heading_format, $the_date), 'h'.$heading_level, $heading_id.$heading_class);

                $out[] = ($include_date == 'yes') ?
                    n.n.'<dl'.$list_id.$list_class.'>' :
                    n.n.'<ul'.$list_id.$list_class.'>';

                $current_month = $month_year;
            }

            if ($include_date == 'yes')
            {
                if ($the_day != $current_day)
                {
                    $out[] = n.t.'<dt>'.strftime($date_format, $the_date).'</dt>';

                    $current_day = $the_day;
                }
            }

            if ($form or $thing)
            {
                ++$i;

                $row['uPosted'] = $the_date;

                populateArticleData($row);

                $GLOBALS['thisarticle']['is_first'] = ($i == 1);
                $GLOBALS['thisarticle']['is_last'] = ($i == $post_count);

                $contents = parse($what);

                unset($GLOBALS['thisarticle']);
            }

            else
            {
                $contents = '<a rel="bookmark" href="'.permlinkurl_id($ID).'">'.$Title.'</a>';
            }

            $out[] = ($include_date == 'yes') ?
                n.t.'<dd>'.$contents.'</dd>' :
                n.t.'<li>'.$contents.'</li>';
        }

        return ($include_date == 'yes') ?
            join('', $out).n.'</dl>' :
            join('', $out).n.'</ul>';
    }

// -------------------------------------------------------------

    function upm_date_archive_query_build($section, $exclude_section, $category, $exclude_category)
    {
        // ----- sections

        $section_sql = '';

        if ($exclude_section)
        {
            if ($exclude_section == 'default')
            {
                $exclude_section = safe_column('name', 'txp_section', "on_frontpage = '1'");
            }

            else
            {
                $exclude_section = do_list($exclude_section);
            }

            $exclude_section = join("','", doSlash($exclude_section));

            $section_sql = "and Section not in('$exclude_section')";
        }

        elseif ($section)
        {
            if ($section == 'default')
            {
                $section = safe_column('name', 'txp_section', "on_frontpage = '1'");
            }

            else
            {
                $section = do_list($section);
            }

            $section = join("','", doSlash($section));

            $section_sql = "and Section in('$section')";
        }

        // ----- categories

        $category_sql = '';

        if ($exclude_category)
        {
            $exclude_category = join("','", doSlash(do_list($exclude_category)));

            $category_sql = "and (Category1 not in('$exclude_category') and Category2 not in('$exclude_category'))";
        }

        elseif ($category)
        {
            $category = join("','", doSlash(do_list($category)));

            $category_sql = "and (Category1 in('$category') or Category2 in('$category'))";
        }

        return array($section_sql, $category_sql);
    }

// -------------------------------------------------------------

    function upm_date_archive_load()
    {
        global $prefs, $pretext, $s, $month, $status, $page;

        if (empty($prefs['upm_date_archive_listen']))
        {
            return;
        }

        $sections = unserialize($prefs['upm_date_archive_listen']);

        if (empty($sections))
        {
            return;
        }

        if ($pretext['status'] != '404')
        {
            return;
        }

        $request_uri = preg_replace("|^https?://[^/]+|i", '', serverSet('REQUEST_URI'));

        if (!$request_uri and serverSet('SCRIPT_NAME'))
        {
            $request_uri = serverSet('SCRIPT_NAME').(serverSet('QUERY_STRING') ? '?'.serverSet('QUERY_STRING') : '');
        }

        if (!$request_uri and $argv = serverSet('argv'))
        {
            $request_uri = @substr($argv[0], strpos($argv[0], ';') + 1);
        }

        $subpath = preg_quote(preg_replace("/https?:\/\/.*(\/.*)/Ui", "$1", hu), '/');

        $req = preg_replace("/^$subpath/i", '/', $request_uri);

        $req = chopUrl($req);

        if (!$pretext['id'] and in_array($req['u1'], $sections))
        {
            $pretext['status'] = $status = '200';

            $pretext['s'] = $s = $req['u1'];

            $rs = safe_row('page', 'txp_section', "name = '".doSlash($pretext['s'])."' limit 1");
            $pretext['page'] = $page = @$rs['page'];

            if ($req['u2'] and preg_match('/^[0-9]{4}-{1}[0-9]{2}$/', $req['u2']))
            {
                $pretext['month'] = $month = $req['u2'];
            }
        }
    }

// -------------------------------------------------------------

    function upm_date_archive_prefs($event, $step)
    {
        global $prefs;

        pagetop(upm_date_archive_gTxt('prefs'), ($step == 'update' ? gTxt('preferences_saved') : ''));

        echo <<<css

<style type="text/css" media="screen,projection">
<!--

#upm_date_archive {
margin: 3em auto auto auto;
width: 20em;
}

#upm_date_archive ul {
margin: 0 auto 15px auto;
padding: 0;
width: 14em;
list-style: none none;
}

#upm_date_archive li {
margin: 0 0 5px 0;
padding: 0;
}

#upm_date_archive li input {
margin: 0;
padding: 0;
vertical-align: middle;
}

-->
</style>

css;

        if ($step == 'update')
        {
            $sections = gps('sections');

            if (empty($sections))
            {
                $sections = array();
            }

            else
            {
                $sections = doSlash($sections);
            }

            $sections = serialize($sections);

            safe_update('txp_prefs', "val = '$sections'", "name = 'upm_date_archive_listen'");

            $prefs['upm_date_archive_listen'] = $sections;
        }

        $sections = unserialize($prefs['upm_date_archive_listen']);

        echo n.n.'<div id="upm_date_archive">'.
            n.n.hed(upm_date_archive_gTxt('prefs'), '1');

        $rs = safe_rows_start('name', 'txp_section', "name != 'default' order by name asc");

        if ($rs)
        {
            $out = array();

            while ($row = nextRow($rs))
            {
                extract($row);

                $temp = n.t.'<li><label><input type="checkbox" name="sections[]" value="'.$name.'"';

                if (is_array($sections) and in_array($name, $sections))
                {
                    $temp .= ' checked="checked"';
                }

                $temp .= ' /> '.$name.'</label></li>';

                $out[] = $temp;
            }

            if ($out)
            {
                echo n.n.form(
                    n.eInput('upm_date_archive').
                    n.sInput('update').

                    n.n.'<ul>'.
                        join('', $out).
                    n.'</ul>'.

                    n.n.fInput('submit', 'update', 'Update', 'smallerbox')
                );
            }
        }

        echo n.n.'</div>';
    }

// -------------------------------------------------------------

    function upm_date_archive_install()
    {
        global $prefs;

        if (!isset($prefs['upm_date_archive_listen']))
        {
            safe_insert('txp_prefs', "
                name = 'upm_date_archive_listen',
                val = '',
                html = 'text_input',
                prefs_id = 1,
                type = 2,
                event = 'publish',
                position = 0
            ");
        }
    }

// -------------------------------------------------------------

    function upm_date_archive_gTxt($what, $atts = array())
    {
        $lang = array(
            'no_month_archive' => 'No articles could be found for the requested month.',
            'prefs'                         => 'upm_date_archive Preferences'
        );

        return strtr($lang[$what], $atts);
    }?>