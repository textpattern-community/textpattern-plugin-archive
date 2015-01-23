<?phpif(@txpinterface == 'admin') { 
    global $smd_akey_event, $smd_akey_styles; 
    $smd_akey_event = 'smd_akey'; 

    $smd_akey_styles = array( 
        'list' => 
         '.smd_hidden { display:none; }', 
    ); 

    add_privs($smd_akey_event, '1'); 
    add_privs('plugin_prefs.smd_access_keys', '1'); 
    register_tab('extensions', $smd_akey_event, smd_akey_gTxt('smd_akey_tab_name')); 
    register_callback('smd_akey_dispatcher', $smd_akey_event); 
    register_callback('smd_akey_welcome', 'plugin_lifecycle.smd_access_keys'); 
    register_callback('smd_akey_prefs', 'plugin_prefs.smd_access_keys'); 
} 

global $smd_akey_prefs; 
$smd_akey_prefs = array( 
    'smd_akey_file_download_expires' => array( 
        'html'     => 'text_input', 
        'type'     => PREF_HIDDEN, 
        'position' => 10, 
        'default'  => '3600', 
    ), 
    'smd_akey_salt_length' => array( 
        'html'     => 'text_input', 
        'type'     => PREF_HIDDEN, 
        'position' => 20, 
        'default'  => '8', 
    ), 
    'smd_akey_log_ip' => array( 
        'html'     => 'yesnoradio', 
        'type'     => PREF_HIDDEN, 
        'position' => 30, 
        'default'  => '0', 
    ), 
); 

if (!defined('SMD_AKEYS')) define("SMD_AKEYS", 'smd_akeys'); 

register_callback('smd_access_protect_download', 'file_download'); 

// ******************** 
// ADMIN SIDE INTERFACE 
// ******************** 
// Jump off point for event/steps 
function smd_akey_dispatcher($evt, $stp) { 
    if(!$stp or !in_array($stp, array( 
            'smd_akey_table_install', 
            'smd_akey_table_remove', 
            'smd_akey_create', 
            'smd_akey_prefs', 
            'smd_akey_prefsave', 
            'smd_akey_multi_edit', 
            'smd_akey_change_pageby', 
        ))) { 
        smd_akey(''); 
    } else $stp(); 
} 

// Bootstrap when installed/deleted 
function smd_akey_welcome($evt, $stp) { 
    $msg = ''; 
    switch ($stp) { 
        case 'installed': 
            smd_akey_table_install(0); 
            $msg = 'Restrict your TXP world :-)'; 
            break; 
        case 'deleted': 
            smd_akey_table_remove(0); 
            break; 
    } 
    return $msg; 
} 

// Main admin interface 
function smd_akey($msg='') { 
    global $smd_akey_event, $smd_akey_list_pageby, $smd_akey_styles, $logging, $smd_akey_prefs; 

    pagetop(smd_akey_gTxt('smd_akey_tab_name'), $msg); 

    if (smd_akey_table_exist(1)) { 
        extract(gpsa(array('page', 'sort', 'dir', 'crit', 'search_method'))); 
        if ($sort === '') $sort = get_pref('smd_akey_sort_column', 'time'); 
        if ($dir === '') $dir = get_pref('smd_akey_sort_dir', 'desc'); 
        $dir = ($dir == 'asc') ? 'asc' : 'desc'; 

        switch ($sort) { 
            case 'page': 
                $sort_sql = 'page '.$dir.', time desc'; 
            break; 

            case 'triggah': 
                $sort_sql = 'triggah '.$dir.', time desc'; 
            break; 

            case 'maximum': 
                $sort_sql = 'maximum '.$dir.', time desc'; 
            break; 

            case 'accesses': 
                $sort_sql = 'accesses '.$dir.', time desc'; 
            break; 

            case 'ip': 
                $sort_sql = 'ip '.$dir.', time desc'; 
            break; 

            default: 
                $sort = 'time'; 
                $sort_sql = 'time '.$dir; 
            break; 
        } 

        set_pref('smd_akey_sort_column', $sort, 'smd_akey', PREF_HIDDEN, '', 0, PREF_PRIVATE); 
        set_pref('smd_akey_sort_dir', $dir, 'smd_akey', PREF_HIDDEN, '', 0, PREF_PRIVATE); 

        $switch_dir = ($dir == 'desc') ? 'asc' : 'desc'; 

        $criteria = 1; 

        if ($search_method and $crit) { 
            $crit_escaped = doSlash(str_replace(array('\\','%','_','\''), array('\\\\','\\%','\\_', '\\\''), $crit));
            $critsql = array( 
                'page'     => "page like '%$crit_escaped%'", 
                'triggah'  => "triggah like '%$crit_escaped%'", 
                'maximum'  => "maximum = '$crit_escaped'", 
                'accesses' => "accesses = '$crit_escaped'", 
                'ip'       => "ip like '%$crit_escaped%'", 
            ); 

            if (array_key_exists($search_method, $critsql)) { 
                $criteria = $critsql[$search_method]; 
                $limit = 500; 
            } else { 
                $search_method = ''; 
                $crit = ''; 
            } 
        } else { 
            $search_method = ''; 
            $crit = ''; 
        } 

        $total = safe_count(SMD_AKEYS, "$criteria"); 

        echo '<div id="'.$smd_akey_event.'_control" class="txp-control-panel">'; 

        if ($total < 1) { 
            if ($criteria != 1) { 
                echo n.smd_akey_search_form($crit, $search_method). 
                    n.graf(gTxt('no_results_found'), ' class="indicator"').'</div>'; 
            } 
        } 

        $limit = max($smd_akey_list_pageby, 15); 

        list($page, $offset, $numPages) = pager($total, $limit, $page); 

        echo n.smd_akey_search_form($crit, $search_method).'</div>'; 

        // Retrieve the secret keyring table entries 
        $secring = safe_rows('*', SMD_AKEYS, "$criteria order by $sort_sql limit $offset, $limit"); 

        // Set up the buttons and column info 
        $newbtn = '<a class="navlink" href="#" onclick="return smd_akey_togglenew();">'.smd_akey_gTxt('smd_akey_btn_new').'</a>';
        $prefbtn = '<a class="navlink" href="?event='.$smd_akey_event.a.'step=smd_akey_prefs">'.smd_akey_gTxt('smd_akey_btn_pref').'</a>'; 
        $showip = get_pref('smd_akey_log_ip', $smd_akey_prefs['smd_akey_log_ip']['default'], 1); 

        echo <<<EOC 
<script type="text/javascript"> 
function smd_akey_togglenew() { 
    box = jQuery("#smd_akey_create"); 
    if (box.css("display") == "none") { 
        box.show(); 
    } else { 
        box.hide(); 
    } 
    jQuery("input.smd_focus").focus(); 
    return false; 
} 
jQuery(function() { 
    jQuery("#smd_akey_add").click(function () { 
        jQuery("#smd_akey_step").val('smd_akey_create'); 
        jQuery("#smd_akey_form").removeAttr('onsubmit').submit(); 
    }); 
}); 
</script> 
EOC; 
        // Inject styles 
        echo '<style type="text/css">' . $smd_akey_styles['list'] . '</style>'; 

        // Access key list 
        echo n.'<div id="'.$smd_akey_event.'_container" class="txp-container txp-list">'; 
        echo '<form name="longform" id="smd_akey_form" action="index.php" method="post" onsubmit="return verify(\''.gTxt('are_you_sure').'\')">';
        echo startTable('list'); 
        echo n.'<thead>' 
            .n.tr(tda($newbtn . sp . $prefbtn, ' class="noline"')) 
            .n.tr( 
                n.column_head(smd_akey_gTxt('smd_akey_page'), 'page', $smd_akey_event, true, $switch_dir, $crit, $search_method, (('page' == $sort) ? "$dir " : '').'page').
                n.column_head(smd_akey_gTxt('smd_akey_trigger'), 'triggah', $smd_akey_event, true, $switch_dir, $crit, $search_method, (('triggah' == $sort) ? "$dir " : '')).
                n.column_head(smd_akey_gTxt('smd_akey_time'), 'time', $smd_akey_event, true, $switch_dir, $crit, $search_method, (('time' == $sort) ? "$dir " : '').'date time').
                n.column_head(smd_akey_gTxt('smd_akey_max'), 'maximum', $smd_akey_event, true, $switch_dir, $crit, $search_method, (('maximum' == $sort) ? "$dir " : '')).
                n.column_head(smd_akey_gTxt('smd_akey_accesses'), 'accesses', $smd_akey_event, true, $switch_dir, $crit, $search_method, (('accesses' == $sort) ? "$dir " : '')).
                (($showip) ? n.column_head('IP', 'ip', $smd_akey_event, true, $switch_dir, $crit, $search_method, (('ip' == $sort) ? "$dir " : '').'ip') : '').
                n.hCell('', '', ' class="multi-edit"') 
            ). 
            n.'</thead>'; 

        $multiOpts = array('smd_akey_delete' => gTxt('delete')); 

        echo '<tfoot>' . tr(tda( 
                select_buttons() 
                .n.selectInput('smd_akey_multi_edit', $multiOpts, '', true) 
                .n.eInput($smd_akey_event) 
                .n.fInput('submit', '', gTxt('go'), 'smallerbox') 
            ,' class="multi-edit" colspan="' . (($showip) ? 7 : 6) . '" style="text-align: right; border: none;"')); 
        echo '</tfoot>'; 
        echo '<tbody>'; 

        // New access key row 
        echo '<tr id="smd_akey_create" class="smd_hidden">'; 
        echo td(fInput('hidden', 'step', 'smd_akey_multi_edit', '', '', '', '', '', 'smd_akey_step').fInput('text', 'smd_akey_newpage', '', 'smd_focus', '', '', '60'))
            .td(fInput('text', 'smd_akey_triggah', '')) 
            .td(fInput('text', 'smd_akey_time', safe_strftime('%Y-%m-%d %H:%M:%S'), '', '', '', '25')) 
            .td(fInput('text', 'smd_akey_maximum', '', '', '', '', '5')) 
            .td('&nbsp;') 
            . (($showip) ? td('&nbsp;') : '') 
            .td(fInput('submit', 'smd_akey_add', gTxt('add'), 'smallerbox', '', '', '', '', 'smd_akey_add'));
        echo '</tr>'; 

        // Remaining access keys 
        foreach ($secring as $secidx => $data) { 
            if ($showip) { 
                $ips = do_list($data['ip'], ' '); 
                $iplist = array(); 
                foreach ($ips as $ip) { 
                    $iplist[] = ($logging == 'none') ? $ip : eLink('log', 'log_list', 'search_method', 'ip', $ip, 'crit', $ip);
                } 
            } 
            $dkey = $data['page'].'|'.$data['t_hex']; 
            echo tr( 
                td('<a href="'.$data['page'].'">'.$data['page'].'</a>', '', 'page') 
                . td($data['triggah']) 
                . td(safe_strftime('%Y-%m-%d %H:%M:%S', $data['time']), 85, 'date time') 
                . td($data['maximum']) 
                . td($data['accesses']) 
                . ( ($showip) ? td( trim(join(' ', $iplist)), 20, 'ip' ) : '' ) 
                . td( fInput('checkbox', 'selected[]', $dkey, 'checkbox'), '', 'multi-edit') 
            ); 
        } 
        echo '</tbody>'; 
        echo endTable(); 
        echo '</form>'; 

        echo '<div id="'.$smd_akey_event.'_navigation" class="txp-navigation">'. 
            n.nav_form($smd_akey_event, $page, $numPages, $sort, $dir, $crit, $search_method, $total, $limit).

            n.pageby_form($smd_akey_event, $smd_akey_list_pageby). 
            n.'</div>'.n.'</div>'; 

    } else { 
        // Table not installed 
        $btnInstall = '<form method="post" action="?event='.$smd_akey_event.a.'step=smd_akey_table_install" style="display:inline">'.fInput('submit', 'submit', smd_akey_gTxt('smd_akey_tbl_install_lbl'), 'smallerbox').'</form>';
        $btnStyle = ' style="border:0;height:25px"'; 
        echo startTable('list'); 
        echo tr(tda(strong(smd_akey_gTxt('smd_akey_prefs_some_tbl')).br.br 
                .smd_akey_gTxt('smd_akey_prefs_some_explain').br.br 
                .smd_akey_gTxt('smd_akey_prefs_some_opts'), ' colspan="2"') 
        ); 
        echo tr(tda($btnInstall, $btnStyle)); 
        echo endTable(); 
    } 
} 

// Change and store qty-per-page value 
function smd_akey_change_pageby() { 
    event_change_pageby('smd_akey'); 
    smd_akey(); 
} 

// The search dropdown list 
function smd_akey_search_form($crit, $method) { 
    global $smd_akey_event, $smd_akey_prefs; 

    $doip = get_pref('smd_akey_log_ip', $smd_akey_prefs['smd_akey_log_ip']['default'], 1); 

    $methods =    array( 
        'page'     => smd_akey_gTxt('smd_akey_page'), 
        'triggah'  => smd_akey_gTxt('smd_akey_trigger'), 
        'maximum'  => smd_akey_gTxt('smd_akey_max'), 
        'accesses' => smd_akey_gTxt('smd_akey_accesses'), 
    ); 

    if ($doip) { 
        $methods['ip'] = gTxt('IP'); 
    } 

    return search_form($smd_akey_event, '', $crit, $methods, $method, 'page'); 
} 

// Create a key from the admin side's 'New key' button 
function smd_akey_create() { 
    extract(gpsa(array('smd_akey_newpage', 'smd_akey_triggah', 'smd_akey_time', 'smd_akey_maximum'))); 

    if ($smd_akey_newpage) { 
        // Just call the public tag with the relevant options 
        $key = smd_access_key( 
            array( 
                'url' => $smd_akey_newpage, 
                'trigger' => $smd_akey_triggah, 
                'start' => $smd_akey_time, 
                'max' => $smd_akey_maximum, 
            ) 
        ); 
        $msg = smd_akey_gTxt('smd_akey_generated', array('{key}' => $key)); 
    } else { 
        $msg = array(smd_akey_gTxt('smd_akey_need_page'), E_ERROR); 
    } 
    smd_akey($msg); 
} 

// Handle submission of the multi-edit dropdown options 
function smd_akey_multi_edit() { 
    $selected = gps('selected'); 
    $operation = gps('smd_akey_multi_edit'); 
    $del = 0; 
    $msg = ''; 

    switch ($operation) { 
        case 'smd_akey_delete': 
            if ($selected) { 
                foreach ($selected as $sel) { 
                    $parts = explode('|', $sel); 
                    $ret = safe_delete(SMD_AKEYS, "page = '" . $parts[0] . "' AND t_hex = '" . $parts[1] . "'");
                    $del = ($ret) ? $del+1 : $del; 
                } 
                $msg = smd_akey_gTxt('smd_akey_deleted', array('{deleted}' => $del)); 
            } 
        break; 
    } 

    smd_akey($msg); 
} 

// Display the prefs 
function smd_akey_prefs() { 
    global $smd_akey_event, $smd_akey_prefs; 

    pagetop(smd_akey_gTxt('smd_akey_pref_legend')); 

    $out = array(); 
    $out[] = '<form name="smd_akey_prefs" id="smd_akey_prefs" action="index.php" method="post">'; 
    $out[] = eInput($smd_akey_event).sInput('smd_akey_prefsave'); 
    $out[] = startTable('list'); 
    $out[] = tr(tdcs(strong(smd_akey_gTxt('smd_akey_pref_legend')), 2)); 
    foreach ($smd_akey_prefs as $idx => $prefobj) { 
        $subout = array(); 
        $subout[] = tda('<label for="'.$idx.'">'.smd_akey_gTxt($idx).'</label>', ' class="noline" style="text-align: right; vertical-align: middle;"'); 
        $val = get_pref($idx, $prefobj['default']); 
        switch ($prefobj['html']) { 
            case 'text_input': 
                $subout[] = fInputCell($idx, $val, '', '', '', $idx); 
            break; 
            case 'yesnoradio': 
                $subout[] = tda(yesnoRadio($idx, $val),' class="noline"'); 
            break; 
        } 
        $out[] = tr(join(n ,$subout)); 
    } 

    $out[] = tr(tda('&nbsp;', ' class="noline"') . tda(fInput('submit', '', gTxt('save'), 'publish'), ' class="noline"'));
    $out[] = endTable(); 
    $out[] = '</form>'; 

    echo join(n, $out); 
} 

// Save the prefs 
function smd_akey_prefsave() { 
    global $smd_akey_event, $smd_akey_prefs; 

    foreach ($smd_akey_prefs as $idx => $prefobj) { 
        $val = ps($idx); 
        set_pref($idx, $val, $smd_akey_event, $prefobj['type'], $prefobj['html'], $prefobj['position']); 
    } 

    $msg = smd_akey_gTxt('smd_akey_prefs_saved'); 

    smd_akey($msg); 
} 

// Add akey table if not already installed 
function smd_akey_table_install($showpane='1') { 
    $GLOBALS['txp_err_count'] = 0; 
    $ret = ''; 
    $sql = array(); 

    // Use 'triggah' and 'maximum' because 'trigger' and 'max' are reserved words. 
    $sql[] = "CREATE TABLE IF NOT EXISTS `".PFX.SMD_AKEYS."` ( 
        `page` varchar(255) NOT NULL default '', 
        `t_hex` varchar(12) NOT NULL default '', 
        `time` int(14) NOT NULL default 0, 
        `secret` varchar(255) NOT NULL default '', 
        `triggah` varchar(255) NULL default '', 
        `maximum` int(11) NULL default 0, 
        `accesses` int(11) NULL default 0, 
        `ip` text NOT NULL default '', 
        PRIMARY KEY (`page`,`t_hex`) 
    ) ENGINE=MyISAM"; 

    if(gps('debug')) { 
        dmp($sql); 
    } 
    foreach ($sql as $qry) { 
        $ret = safe_query($qry); 
        if ($ret===false) { 
            $GLOBALS['txp_err_count']++; 
            echo "<b>".$GLOBALS['txp_err_count'].".</b> ".mysql_error()."<br />\n"; 
            echo "<!--\n $qry \n-->\n"; 
        } 
    } 

    // Be kind to beta testers and update table 
    $flds = getThings('describe `'.PFX.SMD_AKEYS.'`'); 
    if (!in_array('ip',$flds)) { 
        safe_alter(SMD_AKEYS, "add `ip` text NOT NULL default '' after `accesses`"); 
    } 

    // Spit out results 
    if ($GLOBALS['txp_err_count'] == 0) { 
        if ($showpane) { 
            $msg = smd_akey_gTxt('smd_akey_tbl_installed'); 
            smd_akey($msg); 
        } 
    } else { 
        if ($showpane) { 
            $msg = smd_akey_gTxt('smd_akey_tbl_not_installed'); 
            smd_akey($msg); 
        } 
    } 
} 

// ------------------------ 
// Drop table if in database 
function smd_akey_table_remove() { 
    $ret = ''; 
    $sql = array(); 
    $GLOBALS['txp_err_count'] = 0; 
    if (smd_akey_table_exist()) { 
        $sql[] = "DROP TABLE IF EXISTS " .PFX.SMD_AKEYS. "; "; 
        if(gps('debug')) { 
            dmp($sql); 
        } 
        foreach ($sql as $qry) { 
            $ret = safe_query($qry); 
            if ($ret===false) { 
                $GLOBALS['txp_err_count']++; 
                echo "<b>".$GLOBALS['txp_err_count'].".</b> ".mysql_error()."<br />\n"; 
                echo "<!--\n $qry \n-->\n"; 
            } 
        } 
    } 
    if ($GLOBALS['txp_err_count'] == 0) { 
        $msg = smd_akey_gTxt('smd_akey_tbl_removed'); 
    } else { 
        $msg = smd_akey_gTxt('smd_akey_tbl_not_removed'); 
        smd_akey($msg); 
    } 
} 

// ------------------------ 
function smd_akey_table_exist($all='') { 
    if ($all) { 
        $tbls = array(SMD_AKEYS => 8); 
        $out = count($tbls); 
        foreach ($tbls as $tbl => $cols) { 
            if (gps('debug')) { 
                echo "++ TABLE ".$tbl." HAS ".count(@safe_show('columns', $tbl))." COLUMNS; REQUIRES ".$cols." ++".br;
            } 
            if (count(@safe_show('columns', $tbl)) == $cols) { 
                $out--; 
            } 
        } 
        return ($out===0) ? 1 : 0; 
    } else { 
        if (gps('debug')) { 
            echo "++ TABLE ".SMD_AKEYS." HAS ".count(@safe_show('columns', SMD_AKEYS))." COLUMNS;"; 
        } 
        return(@safe_show('columns', SMD_AKEYS)); 
    } 
} 

//********************** 
// PUBLIC SIDE INTERFACE 
//********************** 
function smd_access_key($atts, $thing=NULL) { 
    global $smd_akey_prefs; 

    extract(lAtts(array( 
        'secret'    => '', 
        'url'       => '', 
        'site_name' => '1', 
        'start'     => '', 
        'trigger'   => 'smd_akey', 
        'max'       => '', 
        'extra'     => '', 
    ),$atts)); 

    if (smd_akey_table_exist(1)) { 
        $trigger = trim($trigger); 
        $trigger = ($trigger == 'file_download') ? '' : $trigger; 

        $smd_akey_salt_length = get_pref('smd_akey_salt_length', $smd_akey_prefs['smd_akey_salt_length']['default']); 

        // Without a URL, assume current page 
        $page = rtrim( (($url) ? $url : serverSet('REQUEST_URI')), '/'); 
        if ($site_name) { 
            $page = (strpos($page, 'http') === 0) ? $page : rtrim(hu, '/') . $page; 
        } 

        if (!$secret) { 
            $secret = uniqid('', true); 
        } 

        $salt = substr(md5(uniqid(rand(), true)), 0, $smd_akey_salt_length); 
        $plen = strlen($page) % 32; // Because 32 is the size of an md5 string and we don't want to fall off the end

        // Generate a timestamp. The clock starts ticking from this moment 
        $ts = ($start) ? safe_strtotime($start) : time(); 
        $ts = ($ts === false) ? time() : $ts; 
        $t_hex = dechex($ts); 

        // Update/insert the remaining data 
        $exists = safe_field('page', SMD_AKEYS, "page='".doSlash($page)."' AND t_hex='".doSlash($t_hex)."'");
        $maxinfo = ''; 
        if ($max) { 
            $maxinfo = ", maximum = '".doSlash($max)."', accesses = '0'"; 
        } 
        if ($exists) { 
            safe_update(SMD_AKEYS, "triggah='".doSlash($trigger)."', time='".doSlash($ts)."', secret='".doSlash($secret)."'" . $maxinfo, "page='".doSlash($page)."' AND t_hex='".doSlash($t_hex)."'");
        } else { 
            safe_insert(SMD_AKEYS, "page='".doSlash($page)."', t_hex='".doSlash($t_hex)."', triggah='".doSlash($trigger)."', secret='".doSlash($secret)."', time='".doSlash($ts)."'" . $maxinfo);
       } 

        // Tack on max if applicable 
        $max = ($max) ? '.'.$max : ''; 

        // And any extra 
        $extratok = ($extra) ? '/'.$extra : ''; 

        // Create the raw token... 
        $token = md5($salt.$secret.$page.$trigger.$t_hex.$max.$extra); 
        // ... and insert the salt partway through 
        $salty_token = substr($token, 0, $plen) . $salt . substr($token, $plen); 

        return $page . (($trigger) ? '/' . $trigger : '') . '/' . $salty_token . '/' . $t_hex . $max . $extratok;
    } else { 
        trigger_error(smd_akey_gTxt('smd_akey_tbl_not_installed'), E_USER_NOTICE); 
    } 
} 

// Protect a page for a given time limit from the moment the 
// access token has been generated. Embed this tag at the top 
// of the page you want to protect or wrap it around part of a 
// page you wish to protect. The unique URL to the resource 
// is generated by <txp:smd_access_key /> 
function smd_access_protect($atts, $thing=NULL) { 
    global $smd_access_error, $smd_access_errcode, $smd_akey_info, $smd_akey_prefs, $permlink_mode; 

    extract(lAtts(array( 
        'trigger'      => 'smd_akey', 
        'trigger_mode' => 'exact', // exact, begins, ends, contains 
        'site_name'    => '1', 
        'force'        => '0', 
        'expires'      => '3600', // in seconds 
    ),$atts)); 

    if (smd_akey_table_exist(1)) { 
        $url = serverSet('REQUEST_URI'); 
        $url = (($site_name && (strpos($url, hu) === false)) ? rtrim(hu, '/') : '') . $url; 
        $parts = explode('/', $url); 

        // Look for one of the triggers in the URL and bomb out if we find it 
        $triggers = do_list($trigger); 
        $trigger = $triggers[0]; // Initialise to the first value in case no others are found 

        $trigoff = false; 
        foreach ($triggers as $trig) { 
            switch ($trigger_mode) { 
                case 'exact': 
                    $trigoff = array_search($trig, $parts); 
                    $realTrig = $trig; 
                break; 
                case 'begins': 
                    $count = 0; 
                    foreach ($parts as $part) { 
                        if (strpos($part, $trig) === 0) { 
                            $trigoff = $count; 
                            $realTrig = $part; 
                            break; 
                        } 
                        $count++; 
                    } 
                break; 
                case 'ends': 
                    $count = 0; 
                    foreach ($parts as $part) { 
                        $re = '/.+'.preg_quote($trig).'$/i'; 
                        if (preg_match($re, $part) === 1) { 
                            $trigoff = $count; 
                            $realTrig = $part; 
                            break; 
                        } 
                        $count++; 
                    } 
                break; 
                case 'contains': 
                    $count = 0; 
                    foreach ($parts as $part) { 
                        $re = '/.*'.preg_quote($trig).'.*$/i'; 
                        if (preg_match($re, $part) === 1) { 
                            $trigoff = $count; 
                            $realTrig = $part; 
                            break; 
                        } 
                        $count++; 
                    } 
                break; 
            } 
            if ($trigoff !== false) { 
                // Found it so set the trigger to be the current item and jump out 
                $trigoff = ($trigger == 'file_download') ? $trigoff + 2 : $trigoff; 
                $trigger = $realTrig; 
                break; 
            } 
        } 

        $ret = false; 
        $smd_access_error = $smd_access_errcode = ''; 
        $smd_akey_salt_length = get_pref('smd_akey_salt_length', $smd_akey_prefs['smd_akey_salt_length']['default']); 
        $doip = get_pref('smd_akey_log_ip', $smd_akey_prefs['smd_akey_log_ip']['default']); 

        if ($trigoff !== false) { 
            $tokidx = $trigoff + 1; 
            $timeidx = $trigoff + 2; 
            $extraidx = $trigoff + 3; 

            // OK, on a trigger page, so read the token from the URL 
            $tok = (isset($parts[$tokidx]) && strlen($parts[$tokidx]) == intval(32 + $smd_akey_salt_length)) ? $parts[$tokidx] : 0;

            if ($tok) { 
                // The token is present, so read the timestamp from the URL 
                $t_hex = (isset($parts[$timeidx])) ? $parts[$timeidx] : 0; 

                // Is there a download limit? Extract it if so 
                $timeparts = do_list($t_hex, '.'); 
                $max = (isset($timeparts[1])) ? $timeparts[1] : '0'; 
                $maxtok = ($max) ? '.'.$max : ''; 
                $t_hex = $timeparts[0]; 

                // Any extra info? 
                $extras = (isset($parts[$extraidx])) ? array_slice($parts, $extraidx) : array(); 

                // Recreate the original page URL, sans /trigger/token/time 
                if ($trigger == 'file_download') { 
                    $trigoff++; 
                    $trigger = ''; 
                } 
                if ($permlink_mode == 'messy') { 
                    // Don't want a slash between site and start of query params 
                    $page = rtrim(join('/', array_slice($parts, 0, $trigoff-1)), '/') . $parts[$trigoff-1];
                } else { 
                    $page = rtrim(join('/', array_slice($parts, 0, $trigoff)), '/'); 
                } 

                if ($t_hex) { 
                    // The timestamp is present. Next, get the secret key 
                    $secret = false; 
                    $secring = safe_row('*', SMD_AKEYS, "page='".doSlash($page)."' AND t_hex = '".doSlash($t_hex)."'");

                    if ($secring) { 
                        $secret = $secring['secret']; 

                        // Extract the salt from the token 
                        $plen = strlen($page) % 32; 
                        $salt = substr($tok, $plen, $smd_akey_salt_length); 
                        $tok = substr($tok, 0, $plen).substr($tok, $plen+$smd_akey_salt_length); 
                        $ext = (($extras) ? urldecode(join('/', $extras)) : ''); 

                        // Regenerate the original token... 
                         $check_token = md5($salt.$secret.$page.$trigger.$t_hex.$maxtok.$ext); 

                        // ... and compare it to the one in the URL 
                        if ($check_token == $tok) { 
                            // Token is valid. Now check if the page has expired 
                            $t_dec = hexdec($t_hex); 
                            $now = time(); 

                            // Has the resource become available yet? 
                            if ($now < $t_dec) { 
                                if ($thing == NULL) { 
                                    txp_die(smd_akey_gTxt('smd_akey_err_unavailable'), 410); 
                                } else { 
                                    $smd_access_error = 'smd_akey_err_unavailable'; 
                                    $smd_access_errcode = 410; 
                                } 
                            } else { 
                                // Is 'now' greater than 'then' (when token generated) + expiry period? 
                                if ($expires != 0 && $now > $t_dec + $expires) { 
                                    if ($thing == NULL) { 
                                        txp_die(smd_akey_gTxt('smd_akey_err_expired'), 410); 
                                    } else { 
                                        $smd_access_error = 'smd_akey_err_expired'; 
                                        $smd_access_errcode = 410; 
                                    } 
                                } else { 
                                    // Check if the download limit has been exceeded 
                                    $vu_qty = $secring['accesses']; 
                                    if ($max) { 
                                        if ($vu_qty < $max) { 
                                            $ret = true; 
                                        } else { 
                                            if ($thing == NULL) { 
                                                txp_die(smd_akey_gTxt('smd_akey_err_limit'), 410); 
                                            } else { 
                                                $smd_access_error = 'smd_akey_err_limit'; 
                                                $smd_access_errcode = 410; 
                                            } 
                                        } 
                                    } else { 
                                        $ret = true; 
                                    } 
                                    // Increment the access counter 
                                    $vu_qty++; 
     
                                    // Grab the IP and add it to the list of IPs so far 
                                    if ($doip) { 
                                        $ips = do_list($secring['ip'], ' '); 
                                        $ip = remote_addr(); 
                                        if (!in_array($ip, $ips)) { 
                                            $ips[] = $ip; 
                                        } 
                                        $ipup = ", ip='".doSlash(trim(join(' ', $ips)))."'"; 
                                    } else { 
                                        $ipup = ''; 
                                    } 
                                    safe_update(SMD_AKEYS, "accesses='".doSlash($vu_qty)."'" . $ipup, "page='".doSlash($page)."' AND t_hex = '".doSlash($t_hex)."'");

                                    // Load up the global array so <txp:smd_access_info> and <txp:if_smd_access_info> work
                                    $smd_akey_info = array( 
                                        'page'     => $secring['page'], 
                                        'hextime'  => $secring['t_hex'], 
                                        'issued'   => $secring['time'], 
                                        'now'      => $now, 
                                        'expires'  => $t_dec + $expires, 
                                        'trigger'  => $secring['triggah'], 
                                        'maximum'  => $secring['maximum'], 
                                        'accesses' => $vu_qty, 
                                    ); 
                                    if ($doip) { 
                                        $smd_akey_info['ip'] = $ip; 
                                    } 
                                    if ($extras) { 
                                        $smd_akey_info['extra'] = urldecode(join('/', $extras)); 
                                        foreach($extras as $idx => $extra) { 
                                            $smd_akey_info['extra_'.intval($idx+1)] = urldecode($extra); 
                                        } 
                                    } 
                                } 
                            } 

                        } else { 
                            if ($thing == NULL) { 
                                txp_die(smd_akey_gTxt('smd_akey_err_invalid_token'), 403); 
                            } else { 
                                $smd_access_error = 'smd_akey_err_invalid_token'; 
                                $smd_access_errcode = 403; 
                            } 
                        } 

                    } else { 
                        if ($thing == NULL) { 
                            txp_die(smd_akey_gTxt('smd_akey_err_unauthorized'), 401); 
                        } else { 
                            $smd_access_error = 'smd_akey_err_unauthorized'; 
                            $smd_access_errcode = 401; 
                        } 
                    } 

                } else { 
                    if ($thing == NULL) { 
                        txp_die(smd_akey_gTxt('smd_akey_err_missing_timestamp'), 403); 
                    } else { 
                        $smd_access_error = 'smd_akey_err_missing_timestamp'; 
                        $smd_access_errcode = 403; 
                    } 
                } 

            } else { 
                if ($thing == NULL) { 
                    txp_die(smd_akey_gTxt('smd_akey_err_bad_token'), 403); 
                } else { 
                    $smd_access_error = 'smd_akey_err_bad_token'; 
                    $smd_access_errcode = 403; 
                } 
            } 
        } else { 
            // If we always want to forbid access to this page regardless if the trigger exists 
            if ($force) { 
                if ($thing == NULL) { 
                    txp_die(smd_akey_gTxt('smd_akey_err_forbidden'), 401); 
                } else { 
                    $smd_access_error = 'smd_akey_err_forbidden'; 
                    $smd_access_errcode = 401; 
                } 
            } else { 
                $ret = true; 
            } 
        } 

        // If we reach this point it's because we're using a container 
        return parse(EvalElse($thing, $ret)); 
    } else { 
        trigger_error(smd_akey_gTxt('smd_akey_tbl_not_installed'), E_USER_NOTICE); 
    } 
} 

// Called just before a download is initiated 
function smd_access_protect_download($evt, $stp) { 
    global $smd_akey_prefs, $id, $file_error; 

    if (smd_akey_table_exist(1) && !isset($file_error)) { 
        $fileid = intval($id); 

        // In case the page was called with a bogus filename, get the "true" filename 
        // from the database and make up the valid URL 
        $real_file = safe_field("filename", "txp_file", "id=".doSlash($fileid)); 
        $page = filedownloadurl($fileid, $real_file); 
        $secring = safe_field('page', SMD_AKEYS, "page='".doSlash($page)."'"); 

        // Only want to protect pages that we've generated tokens for 
        if ($secring) { 
            return smd_access_protect( 
                array( 
                    'trigger' => 'file_download', 
                    'force'   => '1', 
                    'expires' => get_pref('smd_akey_file_download_expires', $smd_akey_prefs['smd_akey_file_download_expires']['default']), 
                ) 
            ); 
        } 
    } 
    // remote download not done - leave to TXP to handle error or "local" file download 
    return; 
} 

// Conditional tag for checking error status from smd_access_protect 
function smd_if_access_error($atts, $thing=NULL) { 
    global $smd_access_error, $smd_access_errcode; 

    extract(lAtts(array( 
        'type'   => '', 
        'code'   => '', 
    ),$atts)); 

    $err = array(); 
    $codes = do_list($code); 
    $types = do_list($type); 

    if ($smd_access_error) { 
        if ($code && $type) { 
            $err['code'] = (in_array($smd_access_errcode, $codes)) ? true : false; 
            $err['msg'] = (in_array($smd_access_error, $types)) ? true : false; 
        } else if ($code) { 
            $err['code'] = (in_array($smd_access_errcode, $codes)) ? true : false; 
        } else if ($type) { 
            $err['msg'] = (in_array($smd_access_error, $types)) ? true : false; 
        } else { 
            $err['msg'] = true; 
        } 
    } 

    $out = in_array(false, $err) ? false : true; // AND logic 

    return parse(EvalElse($thing, $out)); 
} 

// Display access error information 
function smd_access_error($atts, $thing=NULL) { 
    global $smd_access_error, $smd_access_errcode; 

    extract(lAtts(array( 
        'item'    => 'message', 
        'message' => '', 
        'wraptag'    => '', 
        'class'      => '', 
        'html_id'    => '', 
        'break'      => '', 
        'breakclass' => '', 
    ),$atts)); 

    $out = array(); 
    $items = do_list($item); 

    if ($smd_access_errcode && in_array('code', $items)) { 
        $out[] = $smd_access_errcode; 
    } 

    if ($smd_access_error && in_array('message', $items)) { 
        $out[] = ($message) ? $message : smd_akey_gTxt($smd_access_error); 
    } 

    if ($out) { 
        return doWrap($out, $wraptag, $break, $class, $breakclass, '', '', $html_id); 
    } 

    return ''; 
} 

// Display access information for custom formatted messages 
function smd_access_info($atts, $thing=NULL) { 
    global $smd_akey_info; 

    extract(lAtts(array( 
        'item'       => 'page', 
        'escape'     => 'html', 
        'format'     => '%Y-%m-%d %H:%M:%S', 
        'wraptag'    => '', 
        'class'      => '', 
        'html_id'    => '', 
        'break'      => '', 
        'breakclass' => '', 
    ),$atts)); 

    $out = array(); 
    $items = do_list($item); 

    foreach ($items as $idx) { 
        if ($smd_akey_info && array_key_exists($idx, $smd_akey_info)) { 
            $val = ($escape == 'html') ? htmlspecialchars($smd_akey_info[$idx]) : $smd_akey_info[$idx]; 
            if (in_array($idx, array('time', 'now', 'expires')) && $format) { 
                $val = safe_strftime($format, $val); 
            } 
            $out[] = $val; 
        } 
    } 

    if ($out) { 
        return doWrap($out, $wraptag, $break, $class, $breakclass, '', '', $html_id); 
    } 

    return ''; 
} 

/** 
 * smd_akey_gTxt    Convert strings for i18n purposes 
 * 
 * @param    string    $what [+] [private] [static]        Name of the item from the array to retrieve 
 * @param    array    $atts [+]    (Default: Array)    Array of 'search' => 'replacements' to make 
 */ 
function smd_akey_gTxt($what, $atts = array()) { 
    $lang = array( 
        'smd_akey_accesses' => 'Access attempts', 
        'smd_akey_btn_new' => 'New key', 
        'smd_akey_btn_pref' => 'Prefs', 
        'smd_akey_deleted' => 'Keys deleted: {deleted}', 
        'smd_akey_err_bad_token' => 'Missing or mangled access key', 
        'smd_akey_err_expired' => 'Access expired', 
        'smd_akey_err_forbidden' => 'Forbidden access', 
        'smd_akey_err_invalid_token' => 'Invalid access key', 
        'smd_akey_err_missing_timestamp' => 'Missing timestamp', 
        'smd_akey_err_unavailable' => 'Not available', 
        'smd_akey_err_unauthorized' => 'Unauthorized access', 
        'smd_akey_err_limit' => 'Access limit reached', 
        'smd_akey_file_download_expires' => 'File download expiry time (seconds)', 
        'smd_akey_generated' => 'Access key: {key}', 
        'smd_akey_log_ip' => 'Log IP addresses', 
        'smd_akey_max' => 'Maximum', 
        'smd_akey_need_page' => 'You need to enter a page URL', 
        'smd_akey_page' => 'Page', 
        'smd_akey_pref_legend' => 'Access key preferences', 
        'smd_akey_prefs_saved' => 'Preferences saved', 
        'smd_akey_prefs_some_tbl' => 'Not all table info available.', 
        'smd_akey_prefs_some_explain' => 'This is either a new installation or a different version'.br.'of the plugin to one you had before.',
        'smd_akey_prefs_some_opts' => 'Click "Install table" to add or update the table'.br.'leaving all existing data untouched.',
        'smd_akey_prefs_some_tbl' => 'Not all table info available.', 
        'smd_akey_salt_length' => 'Salt length (characters)', 
        'smd_akey_tab_name' => 'Access keys', 
        'smd_akey_tbl_install_lbl' => 'Install table', 
        'smd_akey_tbl_installed' => 'Table installed', 
        'smd_akey_tbl_not_installed' => 'Table not installed', 
        'smd_akey_tbl_removed' => 'Table removed', 
        'smd_akey_tbl_not_removed' => 'Table not removed', 
        'smd_akey_time' => 'Issued', 
        'smd_akey_trigger' => 'Trigger', 
    ); 
    return strtr($lang[$what], $atts); 
}?>