<?phpif (txpinterface == 'admin')
    {
        add_privs('upm_plugin_restore', '1,2');
        register_tab('admin', 'upm_plugin_restore', upm_plugin_restore_gTxt('plugin_restore'));
        register_callback('upm_plugin_restore', 'upm_plugin_restore');
    }

// -------------------------------------------------------------

    function upm_plugin_restore($event, $step)
    {
        switch ($step)
        {
            case 'restore':
                upm_plugin_restore_restore();
            break;

            case 'list':
            default:
                upm_plugin_restore_list();
            break;
        }
    }

// -------------------------------------------------------------

    function upm_plugin_restore_list($message = '')
    {
        pagetop(upm_plugin_restore_gTxt('plugin_restore'), $message);

        echo n.n.'<div style="margin: 0 auto 10px; width: 38em;">'.

            n.n.hed(upm_plugin_restore_gTxt('plugin_restore'), 1);

        $rs = safe_rows_start('name, description', 'txp_plugin', "md5(code) != code_md5 order by name");

        if ($rs and numRows($rs) > 0)
        {
            echo n.n.graf(upm_plugin_restore_gTxt('summary')).

                n.n.graf(upm_plugin_restore_gTxt('warning')).

                n.n.startTable('list').
                n.assHead('plugin', 'description', '');

            while ($a = nextRow($rs))
            {
                extract($a);

                $name        = htmlspecialchars($name);
                $description = htmlspecialchars($description);

                // Fix up the description for clean cases
                $description = preg_replace(
                    array(
                        '#&lt;br /&gt;#',
                        '#&lt;(/?(a|b|i|em|strong))&gt;#',
                        '#&lt;a href=&quot;(https?|\.|\/|ftp)([A-Za-z0-9:/?.=_]+?)&quot;&gt;#'
                    ),
                    array('<br />', '<$1>', '<a href="$1$2">'),
                    $description
                );

                echo tr(
                    n.td($name).
                    td($description, 260).
                    td(
                        '<form method="post" action="index.php" onclick="return verify(\''.gTxt('are_you_sure').'\');">'.
                            '<div>'.
                                '<input type="hidden" name="event" value="upm_plugin_restore" />'.
                                '<input type="hidden" name="step" value="restore" />'.
                                '<input type="hidden" name="name" value="'.$name.'" />'.
                                '<input type="submit" class="smallerbox" value="'.upm_plugin_restore_gTxt('restore').'" />'.
                            '</div>'.
                        '</form>'
                    )
                );
            }

            echo endTable();
        }

        else
        {
            echo n.n.graf(upm_plugin_restore_gTxt('none_modified'));
        }

        echo n.n.'</div>';
    }

// -------------------------------------------------------------

    function upm_plugin_restore_restore()
    {
        $name = ps('name');

        if (safe_update('txp_plugin', "code = code_restore", "name = '".doSlash($name)."'"))
        {
            $message = upm_plugin_restore_gTxt('restore_success', array('{name}' => $name));
        }

        else
        {
            $message = upm_plugin_restore_gTxt('restore_failure', array('{name}' => $name));
        }

        upm_plugin_restore_list($message);
    }

// -------------------------------------------------------------

    function upm_plugin_restore_gTxt($what, $atts = array())
    {
        $lang = array(
            'none_modified'   => 'None of your plugins have been modified from the original.',
            'plugin_restore'  => 'Plugin Restore',
            'restore'            => 'Restore',
            'restore_failure' => 'Plugin <strong>{name}</strong> could not be restored.',
            'restore_success' => 'Plugin <strong>{name}</strong> restored.',
            'summary'         => 'The following plugins have been modified from the original.',
            'warning'         => '<strong>Note:</strong> Once a plugin has been restored, there is no way to recover the previous modifications made.',
        );

        return strtr($lang[$what], $atts);
    }?>