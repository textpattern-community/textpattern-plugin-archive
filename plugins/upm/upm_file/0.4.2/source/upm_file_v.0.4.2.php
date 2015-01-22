<?phpif (txpinterface == 'admin')
    {
        upm_file_install();

        add_privs('upm_file_prefs', '1,2');
        register_tab('extensions', 'upm_file_prefs', 'upm_file');
        register_callback('upm_file_prefs', 'upm_file_prefs');

        add_privs('upm_file_install', '1,2');
        register_callback('upm_file_install', 'upm_file_prefs', '', 1);
    }

// -------------------------------------------------------------

    function upm_article_file_list($atts = array())
    {
        global $thisfile, $thisarticle, $file_base_path, $prefs;

        extract(lAtts(array(
            'break'                    => br,
            'class'                    => '',
            'id'                        => $thisarticle['thisid'],
            'file_category' => '',
            'form'                    => 'files',
            'label'                    => '',
            'labeltag'            => '',
            'limit'                    => 0,
            'offset'                => 0,
            'sort'                    => '',
            'sortby'                => '', // deprecated
            'wraptag'                => ''
        ), $atts));

        $id = (int) $id;

        if ($id > 0)
        {
            $field = fetch($prefs['upm_file_field'], 'textpattern', 'ID', doSlash($id));
        }

        if (empty($id) or empty($field))
        {
            return;
        }

        // get custom file field and turn into array
        $list = explode(',', $field);

        // chop off unwanted IDs
        $list = ($limit) ? array_slice($list, $offset, $limit) : array_slice($list, $offset);

        // sanitize for query
        // make sure only real numbers are used
        $list = doSlash(join(',', array_map(create_function('$value', 'return (int) $value;'), $list)));

        if ($file_category)
        {
            $file_category = join("','", doSlash(do_list($file_category)));

            $file_category_sql = "and category in('$file_category')";
        }

        else
        {
            $file_category_sql = '';
        }

        if ($sortby)
        {
            $sort = $sortby;
        }

        $sort = ($sort) ? doSlash($sort) : "field(id, $list)";

        $rs = safe_rows_start('*', 'txp_file', "id in($list) $file_category_sql order by $sort");

        if ($rs)
        {
            $form = fetch_form($form);

            $out = array();

            while ($a = nextRow($rs))
            {
                $GLOBALS['thisfile'] = file_download_format_info($a);

                $out[] = parse($form);

                $GLOBALS['thisfile'] = '';
            }

            if ($out)
            {
                return doLabel($label, $labeltag).doWrap($out, $wraptag, $break, $class);
            }
        }

        return '';
    }

// -------------------------------------------------------------

    function upm_file_article($atts = array(), $thing = '')
    {
        global $is_article_body, $has_article_tag;

        if ($is_article_body)
        {
            trigger_error(gTxt('article_tag_illegal_body'));
            return '';
        }

        $has_article_tag = true;

        return upm_file_parseArticles($atts, 0, $thing);
    }

// -------------------------------------------------------------

    function upm_file_doArticles($atts, $iscustom = 0, $thing = '')
    {
        global $pretext, $prefs;

        extract($pretext);
        extract($prefs);

        $customFields = getCustomFields();
        $customlAtts = array_null(array_flip($customFields));

        $theAtts = lAtts(array(
            'allowoverride'   => (!$q and !$iscustom),
            'author'                  => '',
            'break'           => '',
            'category'              => '',
            'class'           => '',
            'excerpted'              => '',
            'form'                      => 'default',
            'frontpage'              => '',
            'id'                          => '',
            'include_expired' => ($publish_expired_articles ? 'yes' : 'no'),
            'keywords'              => '',
            'label'           => '',
            'labeltag'        => '',
            'limit'                      => 10,
            'listform'              => '',
            'month'                      => '',
            'offset'                  => 0,
            'pageby'                  => '',
            'pgonly'                  => 0,
            'searchall'              => 1,
            'searchform'          => '',
            'searchsticky'      => 0,
            'section'                  => '',
            'sort'                      => '',
            'sortby'                  => '',
            'sortdir'                  => '',
            'status'                  => '4',
            'time'                      => 'past',
            'withfiles'              => false,
            'wraptag'         => '',
        ) + $customlAtts, $atts);

        // if an article ID is specified, treat it as a custom list
        $iscustom = !empty($theAtts['id']) ? true : $iscustom;

        // for the article tag, some attributes are taken from globals
        // override them before extract
        if (!$iscustom)
        {
            $theAtts['author']        = !empty($author) ? "a.$author" : '';
            $theAtts['category']    = ($c) ? "a.$c" : '';
            $theAtts['excerpted'] = '';
            $theAtts['frontpage'] = ($s && $s == 'default') ? true : false;
            $theAtts['month']            = !empty($month) ? "a.$month" : '';
            $theAtts['section']        = ($s && $s != 'default') ? "a.$s" : '';
        }

        extract($theAtts);

        // if a listform is specified, $thing is for doArticle() - hence ignore here.
        if (!empty($listform))
        {
            $thing = '';
        }

        $pageby = (empty($pageby) ? $limit : $pageby);

        // treat sticky articles differently wrt search filtering, etc
        $status = in_array(strtolower($status), array('sticky', '5')) ? 5 : 4;
        $issticky = ($status == 5);

        // give control to search, if necesary
        if ($q && !$iscustom && !$issticky)
        {
            include_once(txpath.'/publish/search.php');

            $s_filter = ($searchall) ? upm_file_filterSearch() : '';

            $q = doSlash($q);

      // searchable article fields are limited to the columns of
      // the textpattern table and a matching fulltext index must exist.
            $cols = do_list($searchable_article_fields);

            if (empty($cols) or $cols[0] == '')
            {
                $cols = array('a.Title', 'a.Body');
            }

            $match = ', match ('.join(', ', $cols).") against ('$q') as score";

            for ($i = 0; $i < count($cols); $i++)
            {
                $cols[$i] = "$cols[$i] rlike '$q'";
            }

            $search = " and (".join(" or ", $cols).") $s_filter";

            // searchall=0 can be used to show search results for the current section only
            if ($searchall)
            {
                $section = '';
            }

            if (!$sort)
            {
                $sort = "downloads desc, score desc";
            }
        }

        else
        {
            $match = $search = '';

            if (!$sort)
            {
                $sort = "downloads desc, Posted desc";
            }
        }

        // for backwards compatibility
        // sortby and sortdir are deprecated
        if ($sortby)
        {
            if (!$sortdir)
            {
                $sortdir = 'desc';
            }

            $sort = "$sortby $sortdir";
        }

        elseif ($sortdir)
        {
            $sort = "a.Posted $sortdir";
        }

        // building query parts
        $author    = ($author)    ? " and a.AuthorID in('".join("','", doSlash(do_list($author)))."')" : '';
        $category = join("','", doSlash(do_list($category)));
        $category    = ($category)    ? " and ( a.Category1 in('".doSlash($category)."') or a.Category2 in('".doSlash($category)."') ) " : '';
        $excerpted = ($excerpted == 'y') ? " and a.Excerpt != ''" : '';
        $frontpage = ($frontpage and (!$q or $issticky)) ? upm_file_filterFrontPage() : '';
        $id    = ($id)    ? " and a.ID in('".join(',', array_map('intval', do_list($id)))."')" : '';
        $month = ($month) ? " and a.Posted like '".doSlash($month)."%'" : '';
        $section = ($section)    ? " and a.Section in('".join("','", doSlash(do_list($section)))."')" : '';

        switch ($time)
        {
            case 'any':
                $time = '';
            break;

            case 'future':
                $time = " and a.Posted > now()";
            break;

            default:
                $time = " and a.Posted <= now()";
            break;
        }

        if ($include_expired == 'no')
        {
            $time .= " and (now() <= Expires or Expires = ".NULLDATETIME.")";
        }

        // trying custom fields here

        $custom = '';

        $customFields = getCustomFields();

        if ($customFields)
        {
            foreach ($customFields as $cField)
            {
                if (isset($atts[$cField]))
                {
                    $customPairs[$cField] = $atts[$cField];
                }
            }

            if (!empty($customPairs))
            {
                $custom =     buildCustomSql($customFields, $customPairs);
            }

            else
            {
                $custom = '';
            }
        }

        // Allow keywords for no-custom articles. That tagging mode, you know
        if ($keywords)
        {
            $keys = doSlash(do_list($keywords));

            foreach ($keys as $key)
            {
                $keyparts[] = "find_in_set('".$key."', a.Keywords)";
            }

            $keywords = " and (" . join(' or ', $keyparts) . ")";
        }

        if ($q and $searchsticky)
        {
            $statusq = ' and a.Status >= 4';
        }

        elseif ($id)
        {
            $statusq = ' and a.Status >= 4';
        }

        else
        {
            $statusq = ' and a.Status = '.intval($status);
        }

        $where = "1". $statusq. $time. $search. $id. $category. $section. $excerpted. $month. $author. $keywords. $custom. $frontpage;

        //do not paginate if we are on a custom list
        if (!$iscustom and !$issticky)
        {
            $grand_total = getThing("select count(*) from ".safe_pfx_j('textpattern')." as a where $where");
            $total = $grand_total - $offset;
            $numPages = ceil($total / $pageby);
            $pg = (!$pg) ? 1 : $pg;
            $pgoffset = $offset + (($pg - 1) * $pageby);

            // send paging info to txp:newer and txp:older
            $pageout['s']                        = $s;
            $pageout['c']                        = $c;
            $pageout['pg']                    = $pg;
            $pageout['total']                = $total;
            $pageout['grand_total'] = $grand_total;
            $pageout['numPages']        = $numPages;

            global $thispage;

            if (empty($thispage))
            {
                $thispage = $pageout;
            }

            if ($pgonly)
            {
                return;
            }
        }

        else
        {
            $pgoffset = $offset;
        }

        $join_type = ($withfiles) ? 'right' : 'left';

        $rs = safe_query("select a.*, unix_timestamp(a.Posted) as uPosted, unix_timestamp(a.Expires) as uExpires, unix_timestamp(a.LastMod) as uLastMod
            $match
            from ".safe_pfx('textpattern')." as a $join_type outer join ".safe_pfx('txp_file')." as f on(a.$upm_file_field = f.id)
            where $where order by ".doSlash($sort)." limit ".intval($pgoffset).", ".intval($limit));

        // get the form name
        if ($q and !$iscustom and !$issticky)
        {
            $form = ($searchform ? $searchform : 'search_results');
        }

        else
        {
            $form = ($listform ? $listform : $form);
        }

        if ($rs)
        {
            $count = 0;

            $articles = array();

            while ($a = nextRow($rs))
            {
                ++$count;

                populateArticleData($a);

                global $thisarticle, $uPosted, $limit;

                $thisarticle['is_first'] = ($count == 1);
                $thisarticle['is_last'] = ($count == numRows($rs));

                if (@constant('txpinterface') === 'admin' and gps('Form'))
                {
                    $articles[] = parse(gps('Form'));
                }

                elseif ($allowoverride and $a['override_form'])
                {
                    $articles[] = parse(fetch_form($a['override_form']));
                }

                else
                {
                    $articles[] = ($thing) ? parse($thing) : parse(fetch_form($form));
                }

                // sending these to paging_link(); Required?
                $uPosted = $a['uPosted'];

                unset($GLOBALS['thisarticle']);
            }

            return doLabel($label, $labeltag).doWrap($articles, $wraptag, $break, $class);
        }
    }

// -------------------------------------------------------------

    function upm_file_filterFrontPage()
    {
        static $filterFrontPage;

        if (isset($filterFrontPage))
        {
            return $filterFrontPage;
        }

        $rs = safe_column('name', 'txp_section', "on_frontpage != '1'");

        if ($rs)
        {
            foreach ($rs as $name)
            {
                $filters[] = " and a.Section != '".doSlash($name)."'";
            }

            $filterFrontPage = join(' ', $filters);
        }

        else
        {
            $filterFrontPage = false;
        }

        return $filterFrontPage;
    }

// -------------------------------------------------------------

    function upm_file_filterSearch()
    {
        $rs = safe_column('name', 'txp_section', "searchable != '1'");

        if ($rs)
        {
            foreach ($rs as $name)
            {
                $filters[] = " and a.Section != '".doSlash($name)."'";
            }

            return join(' ', $filters);
        }

        return false;
    }

// -------------------------------------------------------------

    function upm_file_article_custom($atts = array(), $thing = '')
    {
        return upm_file_parseArticles($atts, 1, $thing);
    }

// -------------------------------------------------------------

    function upm_file_parseArticles($atts, $iscustom = '', $thing = '')
    {
        global $pretext, $is_article_list;

        $old_ial = $is_article_list;

        $is_article_list = ($pretext['id'] && !$iscustom) ? false : true;

        article_push();

        $r = ($is_article_list) ? upm_file_doArticles($atts, $iscustom, $thing) : doArticle($atts, $thing);

        article_pop();

        $is_article_list = $old_ial;

        return $r;
    }


// -------------------------------------------------------------

    function upm_file_prefs($event, $step)
    {
        global $prefs;

        if ($step == 'update')
        {
            $field = doSlash(strip_tags(ps('field')));

            safe_update('txp_prefs', "val = '$field'", "name = 'upm_file_field'");
        }

        extract(get_prefs());

        pagetop('upm_file'.sp.gTxt('prefs'), ($step == 'update' ? gTxt('preferences_saved') : ''));

        $fields = array();

        $max = get_pref('max_custom_fields', 10);


        for($i = 1; $i <= $max; $i++)
        {
            $field_name = "custom_{$i}_set";
            $field_title = $prefs[$field_name];

            if (!empty($prefs["custom_{$i}_set"]))
            {
                $options[ "custom_{$i}" ] = $prefs["custom_{$i}_set"];
            }
        }

        $option_list = array();

        foreach ($options as $key => $value)
        {
            if ($key == $upm_file_field)
            {
                $option_list[] = n.t.'<option value="'.$key.'" selected="selected">'.$value.'</option>';
            }

            elseif (!empty($value))
            {
                $option_list[] = n.t.'<option value="'.$key.'">'.$value.'</option>';
            }
        }

        echo n.'<div style="margin: 3em auto auto auto; width: 16em;">'.
        n.hed('upm_file'.sp.gTxt('prefs'),'1').
        n.n.form(
            n.eInput('upm_file_prefs').
            n.sInput('update').
            n.upm_file_gTxt('custom_field').br.n.'<select name="field" class="list">'.join(n, $option_list).'</select>'.
            br.br.
            n.fInput('submit', 'update', 'Update', 'smallerbox')
        ).n.'</div>';

    }

// -------------------------------------------------------------

    function upm_file_install()
    {
        global $prefs;

        $field = 'custom_1';

        // for upgraders
        if (isset($prefs['upm_file_packets_field']))
        {
            $field = $prefs['upm_file_packets_field'];

            safe_delete('txp_prefs', "name = 'upm_file_packets_field' limit 1");
        }

        if (!isset($prefs['upm_file_field']))
        {
            safe_insert('txp_prefs', "
                name = 'upm_file_field',
                val = '$field',
                html = 'text_input',
                prefs_id = 1,
                type = 2,
                event = 'admin',
                position = 0
            ");
        }
    }

// -------------------------------------------------------------

    function upm_file_gTxt($var, $atts = array())
    {
        $lang = array(
            'custom_field' => 'File Custom Field',
        );

        return strtr($lang[$var], $atts);
    }?>