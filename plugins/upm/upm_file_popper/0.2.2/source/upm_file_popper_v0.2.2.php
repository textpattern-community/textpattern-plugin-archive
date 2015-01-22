<?phpif (txpinterface == 'admin')
    {
        add_privs('upm_file_popper', '1,2,3,4,5');
        register_callback('upm_file_popper', 'upm_file_popper');

        add_privs('upm_file_popper_link', '1,2,3,4,5');
        register_callback('upm_file_popper_link', 'article');

        add_privs('upm_file_popper_js', '1,2,3,4,5');
        register_callback('upm_file_popper_js', 'upm_file_popper_js', '', 1);

        add_privs('upm_file_popper_img', '1,2,3,4,5');
        register_callback('upm_file_popper_img', 'upm_file_popper_img', '', 1);
    }

// -------------------------------------------------------------

    function upm_file_popper()
    {
        global $step, $prefs;

        if ($step == 'upm_file_popper_change_pageby')
        {
            event_change_pageby('file');
            $GLOBALS['prefs'] = get_prefs();
        }

        upm_file_popper_list();
    }

// -------------------------------------------------------------

    function upm_file_popper_list()
    {
        global $file_base_path, $prefs;

        pagetop(upm_file_popper_gTxt('file_selector'));

    echo <<<css
<style type="text/css">
<!--
label {
cursor: pointer;
color: #000;
}

img {
border: none;
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

<script type="text/javascript" src="index.php?event=upm_file_popper_js&#38;name=pop"></script>
css;

        extract(gpsa(array('page', 'sort', 'dir', 'crit', 'search_method')));

        $dir = ($dir == 'desc') ? 'desc' : 'asc';

        switch ($sort)
        {
            case 'id':
                $sort_sql = "id $dir, filename asc";
            break;

            case 'filename':
                $sort_sql = "filename $dir";
            break;

            case 'description':
                $sort_sql = 'description '.$dir.', filename desc';
            break;

            case 'category':
                $sort_sql = "category $dir, filename asc";
            break;

            case 'downloads':
                $sort_sql = "downloads $dir, filename asc";
            break;

            default:
                $dir = 'desc';
                $sort_sql = "filename $dir";
            break;
        }

        $switch_dir = ($dir == 'desc') ? 'asc' : 'desc';

        $criteria = 1;

        if ($search_method and $crit)
        {
            $crit_escaped = doSlash($crit);

            $critsql = array(
                'id'                    => "id = '$crit_escaped'",
                'filename'        => "filename like '%$crit_escaped%'",
                'category'        => "category like '%$crit_escaped%'",
                'description' => "description like '%$crit_escaped%'"
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

        $total = safe_count('txp_file', "$criteria");

        if ($total < 1)
        {
            if ($criteria != 1)
            {
                echo n.upm_file_popper_search_form($crit, $search_method, $sort, $dir).
                    n.graf(gTxt('no_results_found'), ' style="text-align: center;"');
            }

            else
            {
                echo n.graf(gTxt('no_files_recorded'), ' style="text-align: center;"');
            }

            return;
        }

        $limit = max($prefs['file_list_pageby'], 15);

        list($page, $offset, $numPages) = pager($total, $limit, $page);

        echo upm_file_popper_search_form($crit, $search_method, $sort, $dir);

        $rs = safe_rows_start('*', 'txp_file', "$criteria order by $sort_sql limit $offset, $limit");

        if ($rs)
        {
            $sort_link = 'upm_file_popper'.a.'bm=true';

            echo startTable('list'),
                tr(
                    column_head('ID', 'id', $sort_link, true, $switch_dir, $crit, $search_method).
                    column_head('file_name', 'filename', $sort_link, true,    $switch_dir, $crit, $search_method).
                    column_head('description', 'description', $sort_link, true, $switch_dir, $crit, $search_method).
                    column_head('file_category', 'category', $sort_link, true, $switch_dir, $crit, $search_method).
                    hCell().
                    hCell(gTxt('tags')).
                    hCell(gTxt('status')).
                    column_head('downloads', 'downloads', $sort_link, true, $switch_dir, $crit, $search_method)
                );

            while ($a = nextRow($rs))
            {
                extract($a);

                // god I hate javascript in php
                $insert = '<a href="#" onclick="upm_file_popper_attachFile('.chr(39).$id.chr(39).'); return false;"><img src="index.php?event=upm_file_popper_img" width="16px" height="16px" alt="'.upm_file_popper_gTxt('attach_file').'" title="'.upm_file_popper_gTxt('attach_file').'" /></a>';

                $tag_url = '?event=tag'.a.'tag_name=file_download_link'.a.'id='.$id.a.'description='.urlencode($description).
                    a.'filename='.urlencode($filename);

                $file_exists = file_exists(build_file_path($file_base_path, $filename));

                $status = ($file_exists) ?
                    '<span class="ok">'.gTxt('file_status_ok').'</span>' :
                    '<span class="not-ok">'.gTxt('file_status_missing').'</span>';

                if (!isset($downloads))
                {
                    safe_alter('txp_file', "ADD downloads INT DEFAULT '0' NOT NULL");
                    $downloads = 0;
                }

                elseif (empty($downloads))
                {
                    $downloads = '0';
                }


                echo n.n.tr(

                    n.td($id).

                    td('<a href="#" onclick="upm_file_popper_warnEditFile('.$id.'); return false;">'.$filename.'</a>', 125).

                    td($description, 150).
                    td($category, 90).

                    td($insert).

                    td(
                        n.'<ul>'.
                        n.t.'<li><a target="_blank" href="'.$tag_url.a.'type=textile" onclick="popWin(this.href, 400, 250); return false;">Textile</a></li>'.
                        n.t.'<li><a target="_blank" href="'.$tag_url.a.'type=textpattern" onclick="popWin(this.href, 400, 250); return false;">Textpattern</a></li>'.
                        n.t.'<li><a target="_blank" href="'.$tag_url.a.'type=xhtml" onclick="popWin(this.href, 400, 250); return false;">XHTML</a></li>'.
                        n.'</ul>'
                    , 75).

                    td($status, 45).

                    td(
                        ($downloads == '0' ? gTxt('none') : $downloads)
                    , 25)
                );
            }

            echo endTable().

                nav_form('upm_file_popper', $page, $numPages, $sort, $dir, $crit, $search_method).

                upm_file_popper_pageby_form().

                n.tag(
                    n.graf(upm_file_popper_gTxt('return')).
                    n.graf('<a id="yes" href="#" onclick="upm_file_popper_goBack(this.href); return false;">'.gTxt('yes').'</a>.'.
                        ' <a id="no" href="#" onclick="upm_file_popper_done(\'return\'); return false;">'.gTxt('no').'</a>.')
                , 'div',    ' id="return" style="display: none; width: 200px; height: 75px;"').

                n.graf(upm_file_popper_gTxt('success'), ' id="success" style="display: none; width: 150px; height: 35px;"').
                n.graf(upm_file_popper_gTxt('failed'), ' id="failed" style="display: none; width: 150px; height: 35px;"');
        }
    }

// -------------------------------------------------------------

    function upm_file_popper_search_form($crit, $method, $sort, $dir)
    {
        $default_method = 'filename';

        $method = ($method) ? $method : $default_method;

        $methods =    array(
            'id'                    => gTxt('id'),
            'filename'        => gTxt('file_name'),
            'description' => gTxt('description'),
            'category'        => gTxt('file_category')
        );

        return n.n.'<form method="get" action="index.php" style="margin: auto; text-align: center;">'.
            graf(
                '<label for="upm-file-popper-search">'.gTxt('search').'</label>'.sp.
                selectInput('search_method', $methods, $method, '', '', 'upm-file-popper-search').sp.
                fInput('text', 'crit', $crit, 'edit', '', '', '15').
                eInput('upm_file_popper').
                hInput('bm','true').
                fInput('submit', 'search', gTxt('go'), 'smallerbox')
            ).

            ($crit ? n.graf('<a href="?event=upm_file_popper'.a.'bm=true">'.upm_file_popper_gTxt('view_all').'</a>') : '').

        n.'</form>';
    }

// -------------------------------------------------------------

    function upm_file_popper_pageby_form()
    {
        global $prefs;

        $vals = array(
            15    => 15,
            25    => 25,
            50    => 50,
            100 => 100
        );

        $select_page = selectInput('qty', $vals, $prefs['file_list_pageby'], '', 1);

        // proper localisation
        $page = str_replace('{page}', $select_page, gTxt('view_per_page'));

        return form(
            '<div style="margin: auto; text-align: center;">'.
                $page.
                eInput('upm_file_popper').
                sInput('upm_file_popper_change_pageby').
                hInput('bm', '1').
                '<noscript> <input type="submit" value="'.gTxt('go').'" class="smallerbox" /></noscript>'.
            '</div>'
        );
    }

// -------------------------------------------------------------

    function upm_file_popper_link()
    {
        $view = gps('view');

        if ($view == 'text' or empty($view))
        {
            echo <<<form

<form action="">
<input type="hidden" id="upm_file_popper_start" value="">
</form>

<script type="text/javascript" src="index.php?event=upm_file_popper_js&#38;name=link"></script>
form;
        }
    }

// -------------------------------------------------------------

    function upm_file_popper_js()
    {
        global $prefs;

        while (@ob_end_clean());

        header("Content-type: text/javascript");

        if (empty($prefs['upm_file_field']))
        {
            $lang_forgot_field = upm_file_popper_gTxt('forgot_field');

            echo <<<js
/*
upm_file_popper - custom field has not been defined
*/

$(document).ready(function() {
    alert('$lang_forgot_field');
});
js;
            exit(0);
        }

        else
        {
            $upm_file_field = !empty($prefs['upm_file_field']) ? $prefs['upm_file_field'] : '';
            $upm_file_field = str_replace('_', '-', $upm_file_field);
        }

        if (gps('name') == 'link')
        {
            $txt = gTxt('advanced_options');

            $lang_attach_file = upm_file_popper_gTxt('attach_file');

            echo <<<js
/*
upm_file_popper - article window
*/

$(document).ready(function() {
    // ------------------------------
    // setup article edit tracking

    $('#upm_file_popper_start').val( $('body').val() );

    // ------------------------------
    // create and insert popup link

    var w = 575;
    var h = 375;

    var t = (screen.height) ? (screen.height - h) / 2 : 0;
    var l = (screen.width) ? (screen.width - w) / 2 : 0;

    var a = $(document.createElement('a')).
        text('$lang_attach_file').
        attr({
            target: '_blank',
            href: '?event=upm_file_popper\u0026bm=1'
        }).
        click(function () {
            var upm_file_popper = window.open(this.href, 'upm_file_popper', 'top = '+t+', left = '+l+', width = '+w+', height = '+h+', toolbar = no, location = no, directories = no, status = yes, menubar = no, scrollbars = yes, copyhistory = no, resizable = yes');
            upm_file_popper.focus();
            return false;
        });

    var link = $(document.createElement('h3')).
        addClass('plain').
        append(a);

    $('h3.plain > a:contains("$txt")').
        before(link);
});
js;

        exit(0);
    }

    elseif (gps('name') == 'pop')
    {
        $lang_check_leave = upm_file_popper_gTxt('check_leave');

        echo <<<js
/*
upm_file_popper - popup window
*/

    function upm_file_popper_attachFile(id)
    {
        var field = $('#$upm_file_field', window.opener.document).val();

        if ($('form[name="article"]', window.opener))
        {
            if (field == '')
            {
                $('#$upm_file_field', window.opener.document).val(id);
                return upm_file_popper_showMsg('success');
            }

            else
            {
                $('#$upm_file_field', window.opener.document).val(field + ',' + id);
                return upm_file_popper_showMsg('success');
            }
        }

        return upm_file_popper_showMsg('failed');
    }

// -------------------------------------------------------------

    function upm_file_popper_warnEditFile(id)
    {
        if ($('#body', window.opener.document).val() != $('#upm_file_popper_start', window.opener.document).val())
        {
            var check = confirm('$lang_check_leave');

            if (check)
            {
                upm_file_popper_goEditFile(id);
            }
        }

        else
        {
            upm_file_popper_goEditFile(id);
        }
    }

// -------------------------------------------------------------

    function upm_file_popper_goEditFile(id)
    {
        window.opener.focus();

        if ($('input[name="step"]', window.opener.document).val() != 'create')
        {
            $('#yes').attr('href', window.opener.document.location);
            upm_file_popper_showMsg('return');
        }

        window.opener.document.location = '?event=file\u0026step=file_edit\u0026id='+id;
    }

// -------------------------------------------------------------

    function upm_file_popper_goBack(url)
    {
        window.opener.focus();

        upm_file_popper_done('return');

        window.opener.document.location = url;
    }

// -------------------------------------------------------------

    function upm_file_popper_showMsg(id)
    {
        var msg = $('#'+id);

        msg.css({
            position: 'absolute',
            top:            100 + upm_file_popper_scrollTop() + 'px',
            left:            ( (upm_file_popper_pageWidth() / 2) - (parseInt(msg.css('width')) / 2) ) + upm_file_popper_scrollLeft() + 'px',
            display:    'block'
        });

        if (id != 'return')
        {
            setTimeout("upm_file_popper_done('"+id+"')", 1000);
        }
    }

// -------------------------------------------------------------

    function upm_file_popper_done(id)
    {
        $('#'+id).css('display', 'none');
    }

// -------------------------------------------------------------

    function upm_file_popper_pageWidth()
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

    function upm_file_popper_scrollTop()
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

    function upm_file_popper_scrollLeft()
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

        exit(0);
    }
}

// -------------------------------------------------------------

    function upm_file_popper_img()
    {
        while (@ob_end_clean());

        header("Content-type: image/gif");

        echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6'.
            'QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAJvSURBVDjLpZPrS5NhGIf9W7YvBYO'.
            'khlkoqCklWChv2WyKik7blnNris72bi6dus0DLZ0TDxW1odtopDs4D8MDZuLU0kXq61CijSIIasOvv94VT'.
            'UfLiB74fXngup7nvrnvJABJ/5PfLnTTdcwOj4RsdYmo5glBWP6iOtzwvIKSWstI0Wgx80SBblpKtE9KQs/'.
            'We7EaWoT/8wbWP61gMmCH0lMDvokT4j25TiQU/ITFkek9Ow6+7WH2gwsmahCPdwyw75uw9HEO2gUZSkfyI'.
            '9zBPCJOoJ2SMmg46N61YO/rNoa39Xi41oFuXysMfh36/Fp0b7bAfWAH6RGi0HglWNCbzYgJaFjRv6zGuy+'.
            'b9It96N3SQvNKiV9HvSaDfFEIxXItnPs23BzJQd6DDEVM0OKsoVwBG/1VMzpXVWhbkUM2K4oJBDYuGmbKI'.
            'J0qxsAbHfRLzbjcnUbFBIpx/qH3vQv9b3U03IQ/HfFkERTzfFj8w8jSpR7GBE123uFEYAzaDRIqX/2JAtJ'.
            'bDat/COkd7CNBva2cMvq0MGxp0PRSCPF8BXjWG3FgNHc9XPT71Ojy3sMFdfJRCeKxEsVtKwFHwALZfCUk3'.
            'tIfNR8XiJwc1LmL4dg141JPKtj3WUdNFJqLGFVPC4OkR4BxajTWsChY64wmCnMxsWPCHcutKBxMVp5mxA1'.
            'S+aMComToaqTRUQknLTH62kHOVEE+VQnjahscNCy0cMBWsSI0TCQcZc5ALkEYckL5A5noWSBhfm2AecMAj'.
            'bcRWV0pUTh0HE64TNf0mczcnnQyu/MilaFJCae1nw2fbz1DnVOxyGTlKeZft/Ff8x1BRssfACjTwQAAAAB'.
            'JRU5ErkJggg==');

        exit(0);
    }

// -------------------------------------------------------------

    function upm_file_popper_gTxt($var, $atts = array())
    {
        $lang = array(
            'attach'              => 'Attach',
            'attach_file'         => 'Attach File',
            'check_leave'         => 'You have made changes to the current post which you have not saved. Continue without saving?',
            'description_for'     => 'Description for',
            'failed'              => 'Insert failed.',
            'file_selector'       => 'File Selector',
            'forgot_field'        => 'You forgot to define the custom field for upm_file/upm_file_popper to use. Ensure that you have a custom field available, and that you have upm_file installed and activated, then visit Extensions > upm_file.',
            'no_description'      => 'No description has been defined for this file.',
            'packet_custom_field' => 'Packet Custom Field',
            'return'              => 'Return to editing the post?',
            'success'             => 'Success.',
            'view_all'            => 'View All'
        );

        return strtr($lang[$var], $atts);
    }?>