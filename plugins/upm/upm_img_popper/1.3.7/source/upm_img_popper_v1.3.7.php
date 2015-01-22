<?phpif (txpinterface == 'admin')
    {
        upm_img_popper_install();

        add_privs('upm_img_popper', '1,2,3,4,5');
        register_callback('upm_img_popper', 'upm_img_popper');

        add_privs('upm_img_popper_link', '1,2,3,4,5');
        register_callback('upm_img_popper_link', 'article');

        add_privs('upm_img_popper_js', '1,2,3,4,5');
        register_callback('upm_img_popper_js', 'upm_img_popper_js', '', 1);

        add_privs('upm_img_popper_prefs', '1,2');
        register_tab('extensions', 'upm_img_popper_prefs', 'upm_img_popper');
        register_callback('upm_img_popper_prefs', 'upm_img_popper_prefs');
    }

// -------------------------------------------------------------

    function upm_img_popper()
    {
        global $step;

        if ($step == 'upm_img_popper_change_pageby')
        {
            event_change_pageby('image');
            $GLOBALS['prefs'] = get_prefs();
        }

        upm_img_popper_list();
    }

// -------------------------------------------------------------

    function upm_img_popper_list($message = '')
    {
        global $prefs;

        pagetop(upm_img_popper_gTxt('image_selector'), $message);

        $path_to_site = $prefs['path_to_site'];
        $img_dir = $prefs['img_dir'];

        if (!defined('IMPATH'))
        {
            define('IMPATH', $path_to_site.'/'.$img_dir.'/');
        }

        echo <<<css
<style type="text/css">
<!--
label {
color: #000;
cursor: pointer;
}

ul {
margin: 0;
padding: 0;
list-style: none;
}

li {
margin: 0 0 2px 0;
padding: 0;
}

p {
margin: 5px 0;
padding: 0;
}

.right {
text-align: right;
}

.center {
margin: 0 auto;
text-align: center;
}

#no {
margin-left: 2em;
}

#return, #success, #failed {
padding: 1em;
text-align: center;
background-color: #fff;
border: 5px solid #fc3;
}
-->
</style>

<script type="text/javascript" src="index.php?event=upm_img_popper_js&#38;name=pop"></script>
css;

        extract(gpsa(array('page', 'sort', 'dir', 'crit', 'search_method')));

        $dir = ($dir == 'desc') ? 'desc' : 'asc';

        switch ($sort)
        {
            case 'id':
                $sort_sql = "id $dir";
            break;

            case 'name':
                $sort_sql = "name $dir";
            break;

            case 'thumbnail':
                $sort_sql = "thumbnail $dir, id asc";
            break;

            case 'category':
                $sort_sql = "category $dir, id asc";
            break;

            case 'date':
                $sort_sql = "date $dir, id asc";
            break;

            case 'author':
                $sort_sql = "author $dir, id asc";
            break;

            default:
                $dir = 'desc';
                $sort_sql = "id $dir";
            break;
        }

        $switch_dir = ($dir == 'desc') ? 'asc' : 'desc';

        $criteria = 1;

        if ($search_method and $crit)
        {
            $crit_escaped = doSlash($crit);

            $critsql = array(
                'id'             => "id = '$crit_escaped'",
                'name'         => "name like '%$crit_escaped%'",
                'category' => "category like '%$crit_escaped%'",
                'author'     => "author like '%$crit_escaped%'",
                'alt'             => "alt like '%$crit_escaped%'",
                'caption'     => "caption like '%$crit_escaped%'"
            );

            if (array_key_exists($search_method, $critsql))
            {
                $criteria = $critsql[$search_method];
                $limit = 500;
            }

            else
            {
                $search_method = '';
                $crit = '';
            }
        }

        else
        {
            $search_method = '';
            $crit = '';
        }

        $total = safe_count('txp_image', "$criteria");

        if ($total < 1)
        {
            if ($criteria != 1)
            {
                echo n.upm_img_popper_search_form($crit, $search_method, $sort, $dir).
                    n.graf(gTxt('no_results_found'), ' style="text-align: center;"');
            }

            else
            {
                echo n.graf(gTxt('no_images_recorded'), ' style="text-align: center;"');
            }

            return;
        }

        echo upm_img_popper_options_form();

        $limit = max($prefs['image_list_pageby'], 15);

        list($page, $offset, $numPages) = pager($total, $limit, $page);

        echo upm_img_popper_search_form($crit, $search_method, $sort, $dir);

        $rs = safe_rows_start('*, unix_timestamp(date) as uDate', 'txp_image',
            "$criteria order by $sort_sql limit $offset, $limit
        ");

        if ($rs)
        {
            $sort_link = 'upm_img_popper'.a.'bm=true';

            echo n.n.startTable('list').
                n.tr(
                    column_head('ID', 'id', $sort_link, true, $switch_dir, $crit, $search_method).
                    hCell().
                    column_head('date', 'date', $sort_link, true, $switch_dir, $crit, $search_method).
                    column_head('name', 'name', $sort_link, true, $switch_dir, $crit, $search_method).
                    column_head('thumbnail', 'thumbnail', $sort_link, true, $switch_dir, $crit, $search_method).
                    hCell(gTxt('preview')).
                    column_head('image_category', 'category', $sort_link, true, $switch_dir, $crit, $search_method).
                    column_head('author', 'author', $sort_link, true, $switch_dir, $crit, $search_method).
                    hCell(upm_img_popper_gTxt('insert')).
                    ( function_exists('upm_image') ? hCell(upm_img_popper_gTxt('insert_plugin')) : '' ).
                    hCell()
                );

            while ($a = nextRow($rs))
            {
                extract($a);

                $edit_url = '?event=image'.a.'step=image_edit'.a.'id='.$id.a.'sort='.$sort.
                    a.'dir='.$dir.a.'page='.$page.a.'search_method='.$search_method.a.'crit='.$crit;

                $name = empty($name) ? gTxt('unnamed') : $name;

                $thumbnail = ($thumbnail) ?
                    '<img src="'.hu.$img_dir.'/'.$id.'t'.$ext.'" />' :
                    gTxt('no');

                // god I hate javascript in php

                $insert = n.'<ul>'.n.
                    n.t.'<li><a href="#" onclick="insertTag(\'append-article-image\', '.chr(39).$id.chr(39).'); return false;">'.upm_img_popper_gTxt('article_image_append').'</a></li>'.
                    n.t.'<li><a href="#" onclick="insertTag(\'replace-article-image\', '.chr(39).$id.chr(39).'); return false;">'.upm_img_popper_gTxt('article_image_replace').'</a></li>'.
                    n.t.'<li><a href="#" onclick="insertTag(\'image\', '.chr(39).$id.chr(39).'); return false;">'.upm_img_popper_gTxt('image').'</a></li>';

                $plugin = n.'<ul>'.n.
                    n.t.'<li><a href="#" onclick="insertTag(\'plugin-image\', '.chr(39).$id.chr(39).'); return false;">'.upm_img_popper_gTxt('image').'</a></li>';

                if ($thumbnail)
                {
                    $insert .= n.t.'<li><a href="#" onclick="insertTag(\'thumb\', '.chr(39).$id.chr(39).'); return false;">'.gTxt('thumbnail').'</a></li>'.
                        n.t.'<li><a href="#" onclick="insertTag(\'popup\', '.chr(39).$id.chr(39).'); return false;">'.upm_img_popper_gTxt('popup').'</a></li>';

                    $plugin .= n.t.'<li><a href="#" onclick="insertTag(\'plugin-thumb\', '.chr(39).$id.chr(39).'); return false;">'.gTxt('thumbnail').'</a></li>'.
                        n.t.'<li><a href="#" onclick="insertTag(\'plugin-popup\', '.chr(39).$id.chr(39).'); return false;">'.upm_img_popper_gTxt('popup').'</a></li>';
                }

                $insert .= n.t.'<li><a href="#" onclick="insertTag(\'textile\', '.chr(39).$id.chr(39).', '.chr(39).$ext.chr(39).', \'\', \'\', '.chr(39).htmlspecialchars($alt).chr(39).'); return false;">Textile</a></li>'.
                    n.t.'<li><a href="#" onclick="insertTag(\'xhtml\', '.chr(39).$id.chr(39).', '.chr(39).$ext.chr(39).', '.chr(39).$w.chr(39).', '.chr(39).$h.chr(39).', '.chr(39).htmlspecialchars($alt).chr(39).', '.chr(39).htmlspecialchars($caption).chr(39).'); return false;">XHTML</a></li>'.
                    n.'</ul>';

                $plugin .= n.t.'<li><a href="#" onclick="insertTag(\'plugin-custom\', '.chr(39).$id.chr(39).'); return false;">'.upm_img_popper_gTxt('custom_form').'</a></li>'.
                    n.'</ul>';

                $preview = n.'<ul>'.
                    n.t.'<li><a href="#" onclick="makeWin('.chr(39).'&#8220;'.$name.'&#8221;'.chr(39).', '.chr(39).'&#60;img src=&#34;'.hu.$img_dir.'/'.$id.$ext.'&#34; /&#62;'.chr(39).', '.$w.', '.$h.'); return false;">'.upm_img_popper_gTxt('image').'</a></li>';

                if (!empty($alt))
                {
                    $preview .= n.t.'<li><a href="#" onclick="makeWin('.chr(39).upm_img_popper_gTxt('alt_text_for').' &#8220;'.$name.'&#8221;'.chr(39).', '.chr(39).htmlspecialchars($alt).chr(39).'); return false;">'.gTxt('alt_text').'</a></li>';
                }

                if (!empty($caption))
                {
                    $preview .= n.t.'<li><a href="#" onclick="makeWin('.chr(39).upm_img_popper_gTxt('caption_for').' &#8220;'.$name.'&#8221;'.chr(39).', '.chr(39).htmlspecialchars($caption).chr(39).'); return false;">'.gTxt('caption').'</a></li>';
                }

                $preview .= n.'</ul>';

                $category = ($category) ? '<span title="'.fetch_category_title($category, 'image').'">'.$category.'</span>' : '';


                echo n.n.tr(

                    n.td($id, 20).

                    td('<a href="'.$edit_url.'" onclick="warnEditImage('.$id.'); return false;">'.gTxt('edit').'</a>'
                    , 35).

                    td(
                        safe_strftime('%d %b %Y %I:%M %p', $uDate)
                    , 75).

                    td('<a href="'.$edit_url.'" onclick="warnEditImage('.$id.'); return false;">'.$name.'</a>'
                    , 75).

                    td($thumbnail, 75).

                    td($preview).

                    td($category, 75).

                    td(
                        '<span title="'.get_author_name($author).'">'.$author.'</span>'
                    , 75).

                    td($insert, 150).
                    ( function_exists('upm_image') ? n.td($plugin) : '' ).

                    td(
                        dLink('image', 'image_delete', 'id', $id)
                    , 10)
                );
            }

            echo endTable().

                nav_form('upm_img_popper'.a.'bm=1', $page, $numPages, $sort, $dir, $crit, $search_method).

                upm_img_popper_pageby_form().

                n.tag(
                    n.graf(upm_img_popper_gTxt('return')).
                    n.graf('<a id="yes" href="#" onclick="goBack(this.href); return false;">'.gTxt('yes').'</a>.'.
                        ' <a id="no" href="#" onclick="done(\'return\'); return false;">'.gTxt('no').'</a>.')
                , 'div',    ' id="return" style="display: none; width: 200px; height: 75px;"').

                n.graf(upm_img_popper_gTxt('success'), ' id="success" style="display: none; width: 150px; height: 35px;"').
                n.graf(upm_img_popper_gTxt('failed'), ' id="failed" style="display: none; width: 150px; height: 35px;"');
        }
    }

// -------------------------------------------------------------

    function upm_img_popper_options_form()
    {
        global $prefs;

        echo n.'<form action="" style="margin-bottom: 15px;'.( !$prefs['upm_img_popper_show_form'] ? ' display: none;' : '').'">'.
            graf(
                n.'<input type="checkbox" id="inc_width"'.(($prefs['upm_img_popper_show_width'] == '1') ? ' checked="checked"' : '').' /> <label for="inc_width">'.upm_img_popper_gTxt('include_width').'</label> '.
                n.'<input type="checkbox" id="inc_height"'.(($prefs['upm_img_popper_show_height'] == '1') ? ' checked="checked"' : '').' /> <label for="inc_height">'.upm_img_popper_gTxt('include_height').'</label> '.
                n.'<input type="checkbox" id="inc_alt"'.(($prefs['upm_img_popper_show_alt'] == '1') ? ' checked="checked"' : '').' /> <label for="inc_alt">'.upm_img_popper_gTxt('include_alt').'</label> '.
                n.'<input type="checkbox" id="inc_title"'.(($prefs['upm_img_popper_show_title'] == '1') ? ' checked="checked"' : '').' /> <label for="inc_title">'.upm_img_popper_gTxt('include_caption').'</label>'.br.
                n.'<input type="checkbox" id="inc_class"'.(($prefs['upm_img_popper_show_class'] == '1') ? ' checked="checked"' : '').' /> <label for="inc_class">'.upm_img_popper_gTxt('assign_class').'</label> '.
                n.'<input type="text" id="the_class" value="'.$prefs['upm_img_popper_the_class'].'" class="edit" />'
            , ' style="text-align: center;"').

            n.graf(
                '<label for="custom_form">'.upm_img_popper_gTxt('custom_form').'</label> <input type="text" id="custom_form" value="'.$prefs['upm_img_popper_custom_form'].'" class="edit" /> ('.upm_img_popper_gTxt('override').')'
            , ' style="text-align: center;"').

        n.'</form>';
    }

// -------------------------------------------------------------

    function upm_img_popper_search_form($crit, $method, $sort, $dir)
    {
        $default_method = 'name';

        $method = ($method) ? $method : $default_method;

        $methods =    array(
            'id'             => gTxt('ID'),
            'name'         => gTxt('name'),
            'category' => gTxt('image_category'),
            'author'     => gTxt('author'),
            'alt'             => gTxt('alt_text'),
            'caption'     => gTxt('caption')
        );

        return n.n.'<form method="get" action="index.php" style="margin: auto; text-align: center;">'.
            n.graf(
                '<label for="upm-img-popper-search">'.gTxt('search').'</label>'.sp.
                selectInput('search_method', $methods, $method, '', '', 'upm-img-popper-search').sp.
                fInput('text', 'crit', $crit, 'edit', '', '', '15').
                eInput('upm_img_popper').
                hInput('bm','1').
                fInput('submit', 'search', gTxt('go'), 'smallerbox')
            ).

            ($crit ? n.graf('<a href="?event=upm_img_popper'.a.'bm=true">'.upm_img_popper_gTxt('view_all').'</a>') : '').

        n.'</form>';
    }

// -------------------------------------------------------------

    function upm_img_popper_link()
    {
        $view = gps('view');

        if ($view == 'text' or empty($view))
        {
            echo <<<form

<form action="">
<input type="hidden" id="upm_img_popper_start" value="">
</form>

<script type="text/javascript" src="index.php?event=upm_img_popper_js&#38;name=link"></script>

form;

        }
    }

// -------------------------------------------------------------

    function upm_img_popper_pageby_form()
    {
        global $prefs;

        $vals = array(
            15    => 15,
            25    => 25,
            50    => 50,
            100 => 100
        );

        $select_page = selectInput('qty', $vals, $prefs['image_list_pageby'], '', 1);

        // proper localisation
        $page = str_replace('{page}', $select_page, gTxt('view_per_page'));

        return form(
            '<div style="margin: auto; text-align: center;">'.
                $page.
                eInput('upm_img_popper').
                sInput('upm_img_popper_change_pageby').
                hInput('bm', '1').
                '<noscript> <input type="submit" value="'.gTxt('go').'" class="smallerbox" /></noscript>'.
            '</div>'
        );
    }

// -------------------------------------------------------------

    function upm_img_popper_js()
    {
        while (@ob_end_clean());

        if (gps('name') == 'link')
        {
            global $prefs;

            $txt = gTxt('advanced_options');
            $lang_insert_image = upm_img_popper_gTxt('insert_image');

            header("Content-type: text/javascript");

            echo <<<js

    document.getElementById('upm_img_popper_start').value = document.getElementById('body').value;

    var current = 'body';

    if (document.getElementById('excerpt'))
    {
        // IE is retarded
        document.getElementById('body').onclick = function () {
            current = 'body';
        };

        document.getElementById('excerpt').onclick = function () {
            current = 'excerpt';
        };
    }

// -------------------------------------------------------------

    var txt = '$txt';

    var a = (document.createElementNS) ?
        document.createElementNS('http://www.w3.org/1999/xhtml', 'a') :
        document.createElement('a');

    a.appendChild(document.createTextNode('$lang_insert_image'));

    var w = {$prefs['upm_img_popper_admin_popup_width']};
    var h = {$prefs['upm_img_popper_admin_popup_height']};

    var t = (screen.height) ? (screen.height - h) / 2 : 0;
    var l = (screen.width) ? (screen.width - w) / 2 : 0;

    a.onclick = function () {
        var upm_img_popper = window.open(this.href, 'upm_img_popper', 'top = '+t+', left = '+l+', width = '+w+', height = '+h+', toolbar = no, location = no, directories = no, status = yes, menubar = no, scrollbars = yes, copyhistory = no, resizable = yes');
            upm_img_popper.focus();
            return false;
    };

    a.setAttribute('target', 'blank');
    a.setAttribute('href', '?event=upm_img_popper\u0026bm=1');

    var link = (document.createElementNS) ?
        document.createElementNS('http://www.w3.org/1999/xhtml', 'h3') :
        document.createElement('h3');

    link.setAttribute('className', 'plain'); // stupid IE
    link.setAttribute('class', 'plain');
    link.appendChild(a);

// -------------------------------------------------------------

    var h3s = document.getElementsByTagName('h3');

    for (var i = 0; i < h3s.length; i++)
    {
        var h3 = document.getElementsByTagName('h3')[i];

        if (h3.firstChild.firstChild)
        {
            if (h3.firstChild.firstChild.nodeValue == txt)
            {
                h3.parentNode.insertBefore(link, h3);
            }
        }
    }

// -------------------------------------------------------------
// thanks Alex! <http://www.alexking.org/>

    function upm_img_popper_insert(myField, myValue)
    {
        // IE support
        if (document.selection)
        {
            myField.focus();
            sel = document.selection.createRange();
            sel.text = myValue;
            myField.focus();
        }

        // Mozilla/Netscape support
        else if (myField.selectionStart || myField.selectionStart == '0')
        {
            startPos = myField.selectionStart;
            endPos = myField.selectionEnd;

            myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);

            myField.focus();
            myField.selectionStart = startPos + myValue.length;
            myField.selectionEnd = startPos + myValue.length;
        }

        else
        {
            myField.value += myValue;
            myField.focus();
        }
    }
js;
        }

        elseif (gps('name') == 'pop')
        {
            global $img_dir;

            $img_url = hu.$img_dir;

            $lang_confirm_overwrite = upm_img_popper_gTxt('confirm_overwrite');
            $lang_check_leave = upm_img_popper_gTxt('check_leave');

            header("Content-type: text/javascript");

            echo <<<js
    function makeWin(title, content, w, h)
    {
        if (w && h)
        {
            var ww = parseInt(w);
            var wh = parseInt(h);

            if (ww < 100)
            {
                ww = 125;
            }

            if (wh < 100)
            {
                wh = 125;

                ww += 75;
            }

            var scroll = false;

            if (screen.width && (screen.width < ww))
            {
                scroll = 'yes';
                ww = screen.width;
            }

            if (screen.height && (screen.height < wh))
            {
                scroll = 'yes';
                wh = screen.height;
            }
        }

        else
        {
            ww = 450;
            wh = 250;
        }

        if (!title)
        {
            title = name;
        }

        var t = (screen.height) ? (screen.height - wh) / 2 : 0;
        var l = (screen.width) ? (screen.width - ww) / 2 : 0;

        var fastWin = window.open('', 'upm_img_popper_preview', 'top = '+t+', left = '+l+', width = '+ww+', height = '+wh+', toolbar = no, location = no, directories = no, status = no, menubar = no, scrollbars = '+scroll+', copyhistory = no, resizable = yes');

        fastWin.document.writeln('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">');
        fastWin.document.writeln('<html>');
        fastWin.document.writeln('<head>');
        fastWin.document.writeln('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">');
        fastWin.document.writeln('<meta http-equiv="imagetoolbar" content="no">');
        fastWin.document.writeln('<title>'+title+'</title>');
        fastWin.document.writeln('<style type="text/css">');
        fastWin.document.writeln('<!--');
        fastWin.document.writeln('body {');
        fastWin.document.writeln('margin: 0;');
        fastWin.document.writeln('padding: 0;');
        fastWin.document.writeln('color: #000;');
        fastWin.document.writeln('background-color: #fff;');
        fastWin.document.writeln('border-top: 15px solid #fc3;');
        fastWin.document.writeln('text-align: center;');

        if (scroll == false)
        {
            fastWin.document.writeln('overflow: hidden;');
        }

        fastWin.document.writeln('}');

        fastWin.document.writeln('');
        fastWin.document.writeln('img {');
        fastWin.document.writeln('margin: 0 auto;');
        fastWin.document.writeln('padding: 0;');
        fastWin.document.writeln('border: none;');

        if (wh == 125)
        {
            fastWin.document.writeln('margin-top: ' + Math.floor(50-(h/2)) + 'px;');
        }

        fastWin.document.writeln('}');

        fastWin.document.writeln('-->');
        fastWin.document.writeln('</style>');
        fastWin.document.writeln('</head>');
        fastWin.document.writeln('<body>');
        fastWin.document.writeln('<div>');
        fastWin.document.writeln(content);
        fastWin.document.writeln('</div>');
        fastWin.document.writeln('</body>');
        fastWin.document.write('</html>');

        fastWin.document.close();

        fastWin.focus();

        return false;
    }

// -------------------------------------------------------------

    function buildTag(type, id, ext, width, height, alt, title)
    {
        var inc_alt = document.getElementById('inc_alt');
        var inc_title = document.getElementById('inc_title');

        var inc_width = document.getElementById('inc_width');
        var inc_height = document.getElementById('inc_height');

        var inc_class = document.getElementById('inc_class');
        var the_class = document.getElementById('the_class').value;

        var custom_form = document.getElementById('custom_form').value;

        if (type == 'image')
        {
            var insert = '<txp:image id="'+id+'"';

            if (inc_class.checked && the_class)
            {
                insert += ' class="'+the_class+'"';
            }
        }

        else if (type == 'thumb')
        {
            var insert = '<txp:thumbnail id="'+id+'"';
        }

        else if (type == 'popup')
        {
            var insert = '<txp:thumbnail id="'+id+'" poplink="1"';
        }

        else if (type == 'textile')
        {
            var insert = '!';

            if (inc_class.checked && the_class)
            {
                insert += '('+the_class+')';
            }

            insert += '$img_url/'+id+ext;

            if (inc_alt.checked && alt)
            {
                insert += '('+alt+')';
            }

            insert += '!';

            return insert;
        }

        else if (type == 'xhtml')
        {
            var insert = '<img src="$img_url/'+id+ext+'"';

            if (inc_class.checked && the_class)
            {
                insert += ' class="'+the_class+'"';
            }

            if (inc_width.checked && width)
            {
                insert += ' width="'+width+'"';
            }

            if (inc_height.checked && height)
            {
                insert += ' height="'+height+'"';
            }

            if (inc_alt.checked && alt)
            {
                insert += ' alt="'+alt+'"';
            }

            else
            {
                insert += ' alt=""';
            }

            if (inc_title.checked && title)
            {
                insert += ' title="'+title+'"';
            }
        }

        else if (type == 'plugin-custom')
        {
            var insert = '<txp:upm_image image_id="'+id+'" form="'+custom_form+'"';
        }

        else
        {
            type = new String(type);
            type = type.replace('plugin-', '');

            var insert = '<txp:upm_image type="'+type+'" image_id="'+id+'"';

            if (inc_class.checked && the_class)
            {
                insert += ' class="'+the_class+'"';
            }

            if (inc_width.checked == false)
            {
                insert += ' show_width="no"';
            }

            if (inc_height.checked == false)
            {
                insert += ' show_height="no"';
            }

            if (inc_alt.checked == false)
            {
                insert += ' show_alt="no"';
            }

            if (inc_title.checked == false)
            {
                insert += ' show_title="no"';
            }
        }

        insert += ' />';

        return insert;
    }

// -------------------------------------------------------------

    function insertTag(type, id, ext, width, height, alt, title)
    {
        var insert = buildTag(type, id, ext, width, height, alt, title);

        if (window.opener.document.article)
        {
            if (type == 'append-article-image')
            {
                if (window.opener.document.getElementById('article-image').value == '')
                {
                    window.opener.document.getElementById('article-image').value = id;
                    return showMsg('success');
                }

                else
                {
                    window.opener.document.getElementById('article-image').value += ','+id;
                    return showMsg('success');
                }
            }

            else if (type == 'replace-article-image')
            {
                if (window.opener.document.getElementById('article-image').value.length > 1)
                {
                    var option = confirm('$lang_confirm_overwrite');

                    if (option)
                    {
                        window.opener.document.getElementById('article-image').value = id;
                        return showMsg('success');
                    }
                }

                else
                {
                    window.opener.document.getElementById('article-image').value = id;
                    return showMsg('success');
                }
            }

            else
            {
                var temp = 'window.opener.document.getElementById(window.opener.current)';

                temp = eval(temp);

                window.opener.upm_img_popper_insert(temp, insert);

                return showMsg('success');
            }
        }

        else if (window.opener.document.getElementById('form'))
        {
            window.opener.upm_img_popper_insert(window.opener.document.getElementById('form'), insert);
            return showMsg('success');
        }

        else if (window.opener.document.getElementById('html'))
        {
            window.opener.upm_img_popper_insert(window.opener.document.getElementById('html'), insert);
            return showMsg('success');
        }

        return showMsg('failed');
    }

// -------------------------------------------------------------

    function warnEditImage(id)
    {
        if (window.opener.document.getElementById('body').value != window.opener.document.getElementById('upm_img_popper_start').value)
        {
            var check = confirm('$lang_check_leave');

            if (check)
            {
                goEditImage(id);
            }
        }

        else
        {
            goEditImage(id);
        }

        return false;
    }

// -------------------------------------------------------------

    function goEditImage(id)
    {
        window.opener.focus();

        if (window.opener.document.article.step.value != 'create')
        {
            document.getElementById('yes').setAttribute('href', window.opener.document.location);
            showMsg('return');
        }

        window.opener.document.location = '?event=image\u0026step=image_edit\u0026id='+id;
    }

// -------------------------------------------------------------

    function goBack(url)
    {
        window.opener.focus();

        done('return');

        window.opener.document.location = url;
    }

// -------------------------------------------------------------

    function showMsg(id)
    {
        var obj = document.getElementById(id);

        var width = parseInt(obj.style.width);
        var height = parseInt(obj.style.height);

        var top = 100 + scrollTop();
        var left = ( (pageWidth() / 2) - (width / 2) ) + scrollLeft();

        obj.style.position = 'absolute';
        obj.style.top = top + 'px';
        obj.style.left = left + 'px';
        obj.style.display = 'block';

        if (id != 'return')
        {
            setTimeout("done('"+id+"')", 1000);
        }
    }

// -------------------------------------------------------------

    function done(id)
    {
        document.getElementById(id).style.display = 'none';
    }

// -------------------------------------------------------------

    function pageWidth()
    {
        if (self.innerWidth)
        {
            return self.innerWidth;
        }

        else if (document.documentElement && document.documentElement.clientWidth)
        {
            return document.documentElement.clientWidth;
        }

        return 0;
    }

// -------------------------------------------------------------

    function scrollTop()
    {
        if (self.pageYOffset)
        {
            return self.pageYOffset;
        }

        else if (document.documentElement && document.documentElement.scrollTop)
        {
            return document.documentElement.scrollTop;
        }

        return 0;
    }

// -------------------------------------------------------------

    function scrollLeft()
    {
        if (self.pageYOffset)
        {
            return self.pageXOffset;
        }

        else if (document.documentElement && document.documentElement.scrollTop)
        {
            return document.documentElement.scrollLeft;
        }

        return 0;
    }

js;
        }

        exit(0);
    }

// -------------------------------------------------------------

    function upm_img_popper_prefs($event, $step)
    {
        global $prefs;

        pagetop(upm_img_popper_gTxt('prefs'), ($step == 'update' ? gTxt('preferences_saved') : ''));

        if ($step == 'update')
        {
            extract(doSlash(psas(array(
                'show_width', 'show_height', 'show_alt', 'show_title', 'show_class', 'the_class', 'custom_form',
                'show_form', 'admin_popup_width', 'admin_popup_height'
            ))));

            $show_width  = ($show_width == '1') ? $show_width : '0';
            $show_height = ($show_height == '1') ? $show_height : '0';
            $show_alt    = ($show_alt == '1') ? $show_alt : '0';
            $show_title  = ($show_title == '1') ? $show_title : '0';
            $show_class  = ($show_class == '1') ? $show_class : '0';

            $the_class   = preg_replace("/(^|&\S+;)|(<[^>]*>)/U", '', dumbDown($the_class));
            $custom_form = sanitizeForUrl($custom_form);

            if (empty($admin_popup_width))
            {
                $admin_popup_width = '725';
            }

            if (empty($admin_popup_height))
            {
                $admin_popup_height = '400';
            }

            safe_update('txp_prefs', "val = '$show_width'", "name = 'upm_img_popper_show_width'");
            safe_update('txp_prefs', "val = '$show_height'", "name = 'upm_img_popper_show_height'");
            safe_update('txp_prefs', "val = '$show_alt'", "name = 'upm_img_popper_show_alt'");
            safe_update('txp_prefs', "val = '$show_title'", "name = 'upm_img_popper_show_title'");
            safe_update('txp_prefs', "val = '$show_class'", "name = 'upm_img_popper_show_class'");
            safe_update('txp_prefs', "val = '$the_class'", "name = 'upm_img_popper_the_class'");
            safe_update('txp_prefs', "val = '$custom_form'", "name = 'upm_img_popper_custom_form'");

            safe_update('txp_prefs', "val = '$show_form'", "name = 'upm_img_popper_show_form'");
            safe_update('txp_prefs', "val = '$admin_popup_width'", "name = 'upm_img_popper_admin_popup_width'");
            safe_update('txp_prefs', "val = '$admin_popup_height'", "name = 'upm_img_popper_admin_popup_height'");

            $prefs = get_prefs();
        }

        echo n.n.'<div style="margin: 3em auto auto auto; width: 20em;">'.

        n.n.hed(upm_img_popper_gTxt('prefs'), '1').

        n.n.form(
            n.eInput('upm_img_popper_prefs').
            n.sInput('update').

            n.n.tag(
                n.n.tag(upm_img_popper_gTxt('public_side'), 'legend').

                n.n.graf(
                    upm_img_popper_gTxt('show_width').br.
                    n.yesnoRadio('show_width', $prefs['upm_img_popper_show_width'])
                ).

                n.n.graf(
                    upm_img_popper_gTxt('show_height').br.
                    n.yesnoRadio('show_height', $prefs['upm_img_popper_show_height'])
                ).

                n.n.graf(
                    upm_img_popper_gTxt('show_alt').br.
                    n.yesnoRadio('show_alt', $prefs['upm_img_popper_show_alt'])
                ).

                n.n.graf(
                    upm_img_popper_gTxt('show_title').br.
                    n.yesnoRadio('show_title', $prefs['upm_img_popper_show_title'])
                ).

                n.n.graf(
                    upm_img_popper_gTxt('show_class').br.
                    n.yesnoRadio('show_class', $prefs['upm_img_popper_show_class'])
                ).

                n.n.graf(
                    upm_img_popper_gTxt('the_class').br.
                    n.fInput('text', 'the_class', $prefs['upm_img_popper_the_class'], 'edit')
                ).

                n.n.graf(
                    upm_img_popper_gTxt('custom_form').br.
                    n.fInput('text', 'custom_form', $prefs['upm_img_popper_custom_form'], 'edit')
                )
            , 'fieldset').

            n.n.tag(
                n.n.tag(upm_img_popper_gTxt('admin_side'), 'legend').

                n.n.graf(
                    upm_img_popper_gTxt('show_form').br.
                    n.yesnoRadio('show_form', $prefs['upm_img_popper_show_form'])
                ).

                n.n.graf(
                    upm_img_popper_gTxt('admin_popup_width').br.
                    n.fInput('text', 'admin_popup_width', $prefs['upm_img_popper_admin_popup_width'], 'edit')
                ).

                n.n.graf(
                    upm_img_popper_gTxt('admin_popup_height').br.
                    n.fInput('text', 'admin_popup_height', $prefs['upm_img_popper_admin_popup_height'], 'edit')
                )

            , 'fieldset').

            n.n.fInput('submit', 'update', 'Update', 'smallerbox')

        ).n.n.'</div>';
    }

// -------------------------------------------------------------

    function upm_img_popper_install()
    {
        global $prefs;

        $updated = false;

        $settings = "prefs_id = 1, type = 2, event = 'admin', position = 0";

        if (!isset($prefs['upm_img_popper_show_width']))
        {
            safe_insert('txp_prefs', "
                name = 'upm_img_popper_show_width',
                val = '1',
                html = 'yesnoradio',
                $settings
            ");

            $updated = true;
        }

        if (!isset($prefs['upm_img_popper_show_height']))
        {
            safe_insert('txp_prefs', "
                name = 'upm_img_popper_show_height',
                val = '1',
                html = 'yesnoradio',
                $settings
            ");

            $updated = true;
        }

        if (!isset($prefs['upm_img_popper_show_alt']))
        {
            safe_insert('txp_prefs', "
                name = 'upm_img_popper_show_alt',
                val = '1',
                html = 'yesnoradio',
                $settings
            ");

            $updated = true;
        }

        if (!isset($prefs['upm_img_popper_show_title']))
        {
            safe_insert('txp_prefs', "
                name = 'upm_img_popper_show_title',
                val = '1',
                html = 'yesnoradio',
                $settings
            ");

            $updated = true;
        }

        if (!isset($prefs['upm_img_popper_show_class']))
        {
            safe_insert('txp_prefs', "
                name = 'upm_img_popper_show_class',
                val = '1',
                html = 'yesnoradio',
                $settings
            ");

            $updated = true;
        }

        if (!isset($prefs['upm_img_popper_the_class']))
        {
            safe_insert('txp_prefs', "
                name = 'upm_img_popper_the_class',
                val = 'image',
                html = 'text_input',
                $settings
            ");

            $updated = true;
        }

        if (!isset($prefs['upm_img_popper_custom_form']))
        {
            safe_insert('txp_prefs', "
                name = 'upm_img_popper_custom_form',
                val = 'upm_img_popper',
                html = 'text_input',
                $settings
            ");

            $updated = true;
        }

        if (!isset($prefs['upm_img_popper_show_form']))
        {
            safe_insert('txp_prefs', "
                name = 'upm_img_popper_show_form',
                val = '1',
                html = 'yesnoradio',
                $settings
            ");

            $updated = true;
        }

        if (!isset($prefs['upm_img_popper_admin_popup_width']))
        {
            safe_insert('txp_prefs', "
                name = 'upm_img_popper_admin_popup_width',
                val = '725',
                html = 'text_input',
                $settings
            ");

            $updated = true;
        }

        if (!isset($prefs['upm_img_popper_admin_popup_height']))
        {
            safe_insert('txp_prefs', "
                name = 'upm_img_popper_admin_popup_height',
                val = '400',
                html = 'text_input',
                $settings
            ");

            $updated = true;
        }

        if ($updated)
        {
            $GLOBALS['prefs'] = get_prefs();
        }
    }

// -------------------------------------------------------------

    function upm_img_popper_gTxt($what, $atts = array())
    {
        $lang = array(
            'admin_popup_width'     => 'Popup width',
            'admin_popup_height'    => 'Popup height',
            'admin_side'            => 'Admin',
            'alt_text_for'                    => 'Alternate text for',
            'article_image_append'    => 'Article image (append)',
            'article_image_replace' => 'Article image (replace)',
            'assign_class'                    => 'assign class',
            'built_in'                            => 'Built-in',
            'caption_for'                        => 'Caption for',
            'check_leave'                        => 'You have made changes to the current post which you have not saved. Continue without saving?',
            'confirm_overwrite'            => 'You have more than one article image defined. This action will overwrite the entire list. Continue?',
            'custom_form'                        => 'Custom form',
            'failed'                                => 'Insert failed.',
            'for'                                        => 'for',
            'image'                                    => 'Image',
            'image_selector'                => 'Image Selector',
            'include'                                => 'include',
            'include_alt'                        => 'include alt text',
            'include_caption'                => 'include caption',
            'include_height'                => 'include height',
            'include_width'                    => 'include width',
            'insert'                                => 'Insert',
            'insert_image'                    => 'Insert Image',
            'insert_plugin'                    => 'Insert (upm_image)',
            'no_thumbnail'                    => 'Thumbnail not found.',
            'override'                            => 'overrides all optional parameters',
            'popup'                                    => 'Popup',
            'prefs'                                    => 'upm_img_popper Preferences',
            'public_side'           => 'Public',
            'return'                                => 'Return to editing the post?',
            'show_alt'                            => 'Show alternate text?',
            'show_class'                        => 'Show class?',
            'show_height'                        => 'Show height?',
            'show_title'                        => 'Show caption?',
            'show_width'                        => 'Show width?',
            'show_form'                            => 'Show customization options in popup?',
            'success'                                => 'Success.',
            'the_class'                            => 'The class',
            'view_all'                            => 'View All'
        );

        return strtr($lang[$what], $atts);
    }?>