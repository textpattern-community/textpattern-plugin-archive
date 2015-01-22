<?phpif (txpinterface == 'admin')
    {
        // link to javascript
        add_privs('upm_savenew_js_link_load', '1,2,3,4,5,6');
        register_callback('upm_savenew_js_link_load', 'article', 'edit', 1);
        register_callback('upm_savenew_js_link_load', 'form', '', 1);

        // load javascript
        add_privs('upm_savenew_js', '1,2,3,4,5,6');
        register_callback('upm_savenew_js', 'upm_savenew_js', '', 1);
    }

// -------------------------------------------------------------

    function upm_savenew_js_link_load()
    {
        ob_start('upm_savenew_js_link');
    }

    function upm_savenew_js_link($buffer)
    {
        $find = '</head>';
        $replace = n.n.t.t.'<script type="text/javascript" src="index.php?event=upm_savenew_js"></script>'.n.t;

        return str_replace($find, $replace.$find, $buffer);
    }

// -------------------------------------------------------------

    function upm_savenew_js()
    {
        while (@ob_end_clean());

        $save_new = gTxt('save_new');

        header("Content-type: text/javascript");

        echo <<<js
/*
upm_savenew
*/

    $(document).ready(function() {
        // create new article submit button
        $('#page-article input[name="save"]').
            after(' <input type="submit" name="publish" value="$save_new" class="publish" />');

        // article save new button
        $('#page-article input[name="save"] + input[name="publish"]').
            // onclick...
            click(function(){
                // check reset time checkbox
                $('#reset_time').attr({
                    name: 'publish_now',
                    checked: true
                });

                // empty URL-only title
                $('#url-title').attr('value', '');
            });

        // create new form submit button
        $('#page-form input[name="save"]').
            after(' <input type="submit" name="savenew" value="$save_new" class="publish" />');

        // forms save new button
        $('#page-form input[name="save"] + input[name="savenew"]').
            // onclick...
            click(function(){
                // change form name from original to original_copy
                $('input[name="name"]').attr('value', $('input[name="name"]').attr('value') + '_copy');
            });
    });

js;
        exit(0);
    }?>