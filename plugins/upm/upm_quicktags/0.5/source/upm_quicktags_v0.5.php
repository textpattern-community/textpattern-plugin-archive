?phpif (txpinterface == 'admin')
    {
        register_callback('upm_quicktags_article', 'article');
        register_callback('upm_quicktags_form', 'form');
        register_callback('upm_quicktags_page', 'page');
    }

// -------------------------------------------------------------

    function upm_quicktags_article($event)
    {
        echo n.'<script id="write_tab" type="text/javascript" src="upm_quicktags/lib/quicktags.js"></script>';
        echo n.'<script type="text/javascript" src="upm_quicktags/write.js"></script>';
        echo n.'<script type="text/javascript" src="upm_quicktags/lib/txp.js"></script>';
        echo n.'<script type="text/javascript" src="upm_quicktags/custom.js"></script>';
    }

// -------------------------------------------------------------

    function upm_quicktags_form($event)
    {
        echo n.'<script id="forms_tab" type="text/javascript" src="upm_quicktags/lib/quicktags.js"></script>';
        echo n.'<script type="text/javascript">var toolbar_width = \'368px\';</script>';
        echo n.'<script type="text/javascript" src="upm_quicktags/forms.js"></script>';
        echo n.'<script type="text/javascript" src="upm_quicktags/lib/txp.js"></script>';
        echo n.'<script type="text/javascript" src="upm_quicktags/custom.js"></script>';
    }

// -------------------------------------------------------------

    function upm_quicktags_page($event)
    {
        echo n.'<script id="pages_tab" type="text/javascript" src="upm_quicktags/lib/quicktags.js"></script>';
        echo n.'<script type="text/javascript">var toolbar_width = \'600px\';</script>';
        echo n.'<script type="text/javascript" src="upm_quicktags/pages.js"></script>';
        echo n.'<script type="text/javascript" src="upm_quicktags/lib/txp.js"></script>';
        echo n.'<script type="text/javascript" src="upm_quicktags/custom.js"></script>';
    }?>