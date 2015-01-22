<?phpif (txpinterface == 'admin')
    {
        add_privs('upm_insert_tab_js','1,2,3,4,5,6');

        register_callback('upm_insert_tab', 'article');
        register_callback('upm_insert_tab', 'link');
        register_callback('upm_insert_tab', 'image');
        register_callback('upm_insert_tab', 'file');
        register_callback('upm_insert_tab', 'discuss');

        register_callback('upm_insert_tab', 'page');
        register_callback('upm_insert_tab', 'form');
        register_callback('upm_insert_tab', 'css');

        register_callback('upm_insert_tab_js', 'upm_insert_tab_js', '', 1);
    }

// -------------------------------------------------------------

    function upm_insert_tab()
    {
        echo n.'<script type="text/javascript" src="index.php?event=upm_insert_tab_js"></script>';
    }

// -------------------------------------------------------------

    function upm_insert_tab_js()
    {
        header("Content-type: text/javascript");

        echo <<<js

// -------------------------------------------------------------

    var areas = document.getElementsByTagName('textarea');

    for (var i = 0; i < areas.length; i++)
    {
        if (document.all)
        {
            areas[i].onkeydown = function ()
            {
                return catchKey(event, this);
            };

            areas[i].onkeyup = function ()
            {
                return catchKey(event, this);
            };

            areas[i].onkeypress = function ()
            {
                return catchKey(event, this);
            };
        }

        else
        {
            areas[i].setAttribute('onkeydown', "return catchKey(event, this);");
            areas[i].setAttribute('onkeyup', "return catchKey(event, this);");
            areas[i].setAttribute('onkeypress', "return catchKey(event, this);");
        }
    }

// -------------------------------------------------------------

    function insertTab(obj)
    {
        if (obj.setSelectionRange)
        {
            var s = obj.selectionStart;
            var e = obj.selectionEnd;
            var t = obj.scrollTop;

            obj.value = obj.value.substring(0, s) + '\u0009' + obj.value.substr(e);
            obj.setSelectionRange(s + 1, s + 1);

            obj.focus();
            obj.scrollTop = t;
        }

        else if (obj.createTextRange)
        {
            sel = document.selection.createRange().text = '\u0009';
            obj.onblur = function() { this.onblur = null; };
        }
    }

// -------------------------------------------------------------

    function catchKey(event, obj)
    {
        var keycode = (event.which) ? event.which : event.keyCode;

        if (keycode == 9)
        {
            if (event.type == 'keydown')
            {
                insertTab(obj);

                if (event.preventDefault)
                {
                    event.preventDefault();
                }

                else if (event.returnValue)
                {
                    event.returnValue = false;
                }
            }

            return false;
        }

        if (keycode == 27)
        {
            if (event.type == 'keydown')
            {
                obj.blur();
            }
        }

        return true;
    }
js;

        exit(0);
    }?>