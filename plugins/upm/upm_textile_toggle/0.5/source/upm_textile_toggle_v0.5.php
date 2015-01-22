<?phpif (txpinterface == 'admin')
    {
        add_privs('upm_textile_toggle', '1,2,3,4,5');
        register_callback('upm_textile_toggle', 'article', 'create');
    }

    function upm_textile_toggle($event)
    {
        global $use_textile;

        if ($use_textile == 1)
        {
            echo n.'<script type="text/javascript">document.getElementById(\'markup-excerpt\').selectedIndex = 2;</script>';
        }
    }?>