<?phpfunction upm_category_image($atts = array())
    {
        global $pretext, $prefs;

        if ($pretext['c'])
        {
            $row = getRow("select cat.name as cat_name, cat.title as cat_title,
                img.id as img_id, img.w as img_w, img.h as img_h, img.ext as img_ext, img.thumbnail as img_thumbnail
                from ".safe_pfx('txp_image')." as img left join ".safe_pfx('txp_category')." as cat on(cat.name = img.name)
                where img.category = 'upm_category_image' and img.name = '".doSlash($pretext['c'])."' limit 0, 1");

            if ($row)
            {
                $atts['thumb'] = 'no';

                return upm_cat_img($pretext['c'], $row, $atts);
            }
        }
    }

// -------------------------------------------------------------

    function upm_category1_image($atts = array())
    {
        global $thisarticle, $pretext, $prefs;

        if ($thisarticle['category1'])
        {
            $row = getRow("select cat.name as cat_name, cat.title as cat_title,
                img.id as img_id, img.w as img_w, img.h as img_h, img.ext as img_ext, img.thumbnail as img_thumbnail
                from ".safe_pfx('txp_image')." as img left join ".safe_pfx('txp_category')." as cat on(cat.name = img.name)
                where img.category = 'upm_category_image' and img.name = '".doSlash($thisarticle['category1'])."' limit 0, 1");

            if ($row)
            {
                return upm_cat_img($thisarticle['category1'], $row, $atts);
            }
        }
    }

// -------------------------------------------------------------

    function upm_category2_image($atts = array())
    {
        global $thisarticle, $pretext, $prefs;

        if ($thisarticle['category2'])
        {
            $row = getRow("select cat.name as cat_name, cat.title as cat_title,
                img.id as img_id, img.w as img_w, img.h as img_h, img.ext as img_ext, img.thumbnail as img_thumbnail
                from ".safe_pfx('txp_image')." as img left join ".safe_pfx('txp_category')." as cat on(cat.name = img.name)
                where img.category = 'upm_category_image' and img.name = '".doSlash($thisarticle['category2'])."' limit 0, 1");

            if ($row)
            {
                return upm_cat_img($thisarticle['category2'], $row, $atts);
            }
        }
    }

// -------------------------------------------------------------

    function upm_cat_img($category, $row, $atts)
    {
        global $pretext, $prefs;

        extract(lAtts(array(
            'class'                 => '',
            'height'             => 'yes',
            'id'                     => '',
            'link'                 => 'yes',
            'link_section' => 'no',
            'thumb'                 => 'yes',
            'title'                 => 'yes',
            'width'                 => 'yes',
            'wraptag'             => '',
            'xhtml'                 => 'yes'
        ), $atts));

        if ($row['img_thumbnail'] and ($thumb == 'yes'))
        {
            $img_url = hu.$prefs['img_dir'].'/'.$row['img_id'].'t'.$row['img_ext'];

            list($w, $h) = getimagesize($prefs['path_to_site'].DS.$prefs['img_dir'].DS.$row['img_id'].'t'.$row['img_ext']);
        }

        else
        {
            $img_url = hu.$prefs['img_dir'].'/'.$row['img_id'].$row['img_ext'];

            $w = $row['img_w'];
            $h = $row['img_h'];
        }

        $id = ($id) ? ' id="'.$id.'"' : $id;
        $class = ($class) ? ' class="'.$class.'"' : $class;

        $img = '<img src="'.$img_url.'"'.
            ($width == 'yes' ? ' width="'.$w.'"' : '').
            ($height == 'yes' ? ' height="'.$h.'"' : '').
            (($wraptag == false and $link == 'no') ? $id.$class : '').
            ' alt="'.$row['cat_title'].'"'.
            ($title == 'yes' ? ' title="'.$row['cat_title'].'"' : ' title=""').
            ($xhtml == 'yes' ? ' />' : '>');

        if ($link == 'no')
        {
            return $img;
        }

        $category = urlencode($row['cat_name']);

        $url = ($link_section == 'yes') ?
            pagelinkurl(array('s'    => $pretext['s'], 'c' => $category)) :
            pagelinkurl(array('c' => $category));

        return ($wraptag) ?
            doTag(href($img, $url), $wraptag, $class, '', $id) :
            tag($img, 'a', ' href="'.$url.'"'.$id.$class);
    }

// -------------------------------------------------------------

    function upm_category_image_list($atts = array())
    {
        global $prefs, $pretext;

        $attrs = lAtts(array(
            'break'                 => '',
            'class'                 => '',
            'height'             => 'yes',
            'id'                     => '',
            'link'                 => 'yes',
            'link_section' => 'no',
            'show'                 => 'all',
            'thumb'                 => 'yes',
            'title'                 => 'yes',
            'width'                 => 'yes',
            'wraptag'             => '',
            'xhtml'                 => 'yes'
        ), $atts);

        extract($attrs);

        switch ($show)
        {
            case 'used':
                $rs = safe_query("select distinct(cat.name) as cat_name, cat.title as cat_title,
                    img.id as img_id, img.w as img_w, img.h as img_h, img.ext as img_ext, img.caption as img_caption, img.thumbnail as img_thumbnail
                    from ".safe_pfx('txp_image')." as img left join ".safe_pfx('txp_category')." as cat on(cat.name = img.name)
                    left join ".safe_pfx('textpattern')." as post on(cat.name = post.Category1 or cat.name = post.Category2)

                    where
                        post.Section = '".doSlash($pretext['s'])."' and
                        cat.type = 'article' and
                        cat.name not in('root','default') and
                        img.category = 'upm_category_image'

                    order by cat.title asc");
            break;

            case 'all':
            default:
                $rs = safe_query("select cat.name as cat_name, cat.title as cat_title,
                    img.id as img_id, img.w as img_w, img.h as img_h, img.ext as img_ext, img.caption as img_caption, img.thumbnail as img_thumbnail
                    from ".safe_pfx('txp_image')." as img left join ".safe_pfx('txp_category')." as cat on(cat.name = img.name)
                    where
                        cat.type = 'article' and
                        cat.name not in('root','default') and
                        img.category = 'upm_category_image'

                    order by cat.title asc");
            break;
        }

        if ($rs)
        {
            $out = array();

            while ($row = nextRow($rs))
            {
                $out[] = upm_cat_img_list($attrs, $row);
            }

            if ($out)
            {
                if ($wraptag == 'ul' or $wraptag == 'ol')
                {
                    return doWrap($out, $wraptag, $break, $class, '', '', '', $id);
                }

                return ($wraptag) ? doTag(join($break, $out), $wraptag, $class, '', $id) : join('', $out);
            }
        }
    }

// -------------------------------------------------------------

    function upm_cat_img_list($atts, $row)
    {
        global $prefs, $pretext;

        extract($atts);

        if ($row['img_thumbnail'] and ($thumb == 'yes'))
        {
            $img_url = hu.$prefs['img_dir'].'/'.$row['img_id'].'t'.$row['img_ext'];

            list($w, $h) = getimagesize($prefs['path_to_site'].DS.$prefs['img_dir'].DS.$row['img_id'].'t'.$row['img_ext']);
        }

        else
        {
            $img_url = hu.$prefs['img_dir'].'/'.$row['img_id'].$row['img_ext'];

            $w = $row['img_w'];
            $h = $row['img_h'];
        }

        $img_title = '';
        $link_title = '';

        $img_class = '';
        $link_class = '';

        if ($link == 'yes')
        {
            if ($row['cat_title'] and ($title == 'yes') )
            {
                $link_title = ' title="'.$row['cat_title'].'"';
            }

            if (!$wraptag and $class)
            {
                $link_class = ' class="'.$class.'"';
            }
        }

        else
        {
            if ($row['cat_title'] and ($title == 'yes') )
            {
                $img_title = ' title="'.$row['cat_title'].'"';
            }

            if (!$wraptag and $class)
            {
                $img_class = ' class="'.$class.'"';
            }
        }

        $img = '<img src="'.$img_url.'"'.
            ($width == 'yes' ? ' width="'.$w.'"' : '').
            ($height == 'yes' ? ' height="'.$h.'"' : '').
            $img_class.
            ' alt="'.$row['cat_title'].'"'.
            $img_title.
            ($xhtml == 'yes' ? ' />' : '>');

        if ($link == 'no')
        {
            return $img;
        }

        $category = urlencode($row['cat_name']);

        $url = ($link_section == 'yes') ?
            pagelinkurl(array('s'    => $pretext['s'], 'c' => $category)) :
            pagelinkurl(array('c' => $category));

        return tag($img, 'a', $link_class.' href="'.$url.'"'.$link_title);
    }?>