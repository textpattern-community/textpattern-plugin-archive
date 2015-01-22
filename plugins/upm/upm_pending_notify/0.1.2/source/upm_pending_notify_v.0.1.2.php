<?phpif (txpinterface == 'admin')
    {
        // i quite hate doing it this way, the write tab's setup is screwy

        add_privs('upm_pending_notify_created', '1,2,3,4,5');
        register_callback('upm_pending_notify_created', 'article', 'edit');

        add_privs('upm_pending_notify_updated_pre', '1,2,3,4,5');
        register_callback('upm_pending_notify_updated_pre', 'article', '', 1);

        add_privs('upm_pending_notify_updated_post', '1,2,3,4,5');
        register_callback('upm_pending_notify_updated_post', 'article', 'edit');
    }

//--------------------------------------------------------------

    function upm_pending_notify_created()
    {
        global $ID;

        // not trying to create a new article
        // begone!
        if (!gps('publish'))
        {
            return;
        }

        $Status = (int) ps('Status');

        if (!has_privs('article.publish') && $Status >= 4)
        {
            $Status = 3;
        }

        if ($Status == 3)
        {
            $ID    = (int) $ID;
            $Title = ps('Title');

            upm_pending_notify_publisher($ID, $Title);
        }
    }

//--------------------------------------------------------------

    function upm_pending_notify_updated_pre()
    {
        $GLOBALS['upm_pending_notify'] = false;

        // article updated
        if (gps('save'))
        {
            $ID = (int) ps('ID');

            $Status = (int) ps('Status');
            $old_status = (int) safe_field('Status', 'textpattern', "ID = $ID");

            // only notify if...

            // ...marked as pending
            // publishers are notified
            // see: upm_pending_notify_updated_post()
            if ($Status == 3)
            {
                $GLOBALS['upm_pending_notify'] = 'publishers';
            }

            // ...marked as live/sticky and was previously pending
            // author is notified
            // see: upm_pending_notify_updated_post()
            elseif ($Status > 3 and $old_status == 3)
            {
                $GLOBALS['upm_pending_notify'] = 'author';
            }
        }
    }

//--------------------------------------------------------------

    function upm_pending_notify_updated_post()
    {
        global $upm_pending_notify, $txp_user, $prefs;

        if (!$upm_pending_notify)
        {
            return;
        }

        $ID = (int) ps('ID');
        $Title = ps('Title');

        switch ($upm_pending_notify)
        {
            case 'publishers':
                upm_pending_notify_publisher($ID, $Title);
            break;

            case 'author':
                $AuthorID = ps('AuthorID');
                $LastModID = ps('LastModID');

                upm_pending_notify_author($ID, $Title, $AuthorID, $LastModID);
            break;
        }
    }

//--------------------------------------------------------------

    function upm_pending_notify_publisher($ID, $Title)
    {
        global $txp_user, $prefs;

        $publishers = safe_rows_start('RealName, email', 'txp_users', "privs = 1 and name != '".doSlash($txp_user)."'");

        if ($publishers)
        {
            $Title = ($Title) ? strip_tags($Title) : gTxt('untitled');
            $author = get_author_name($txp_user);

            $subject = upm_pending_notify_gTxt('email_subject_publisher', array(
                '{sitename}' => $prefs['sitename'],
                '{title}'         => $Title,
            ));

            while ($publisher = nextRow($publishers))
            {
                $body = upm_pending_notify_gTxt('email_message_publisher', array(
                    '{article_url}' => hu.'textpattern/index.php?event=article&step=edit&ID='.$ID,
                    '{author}'            => $author,
                    '{publisher}'        => $publisher['RealName'],
                    '{title}'                => $Title,
                ));

                upm_pending_notify_mail($publisher['RealName'], $publisher['email'], $subject, $body);
            }
        }
    }

//--------------------------------------------------------------

    function upm_pending_notify_author($ID, $Title, $AuthorID, $LastModID)
    {
        global $txp_user, $prefs;

        $author = safe_row('RealName, email', 'txp_users', "name = '".doSlash($AuthorID)."' and name != '".doSlash($txp_user)."'");

        if ($author)
        {
            include_once txpath.'/publish/taghandlers.php';

            $Title = ($Title) ? strip_tags($Title) : gTxt('untitled');
            $url = permlinkurl_id($ID);
            $publisher = get_author_name($txp_user);

            $subject = upm_pending_notify_gTxt('email_subject_author', array(
                '{sitename}' => $prefs['sitename'],
                '{title}'         => $Title,
            ));

            $body = upm_pending_notify_gTxt('email_message_author', array(
                '{article_url}' => $url,
                '{author}'            => $author['RealName'],
                '{publisher}'        => $publisher,
                '{title}'                => $Title,
            ));

            upm_pending_notify_mail($author['RealName'], $author['email'], $subject, $body);
        }
    }

//--------------------------------------------------------------

    function upm_pending_notify_mail($name, $email, $subject, $body)
    {
        global $prefs;

        if ($prefs['override_emailcharset'])
        {
            $charset = 'ISO-8859-1';

            if (is_callable('utf8_decode'))
            {
                $name         = utf8_decode($name);
                $email     = utf8_decode($email);

                $subject = utf8_decode($subject);
                $body         = utf8_decode($body);
            }
        }

        else
        {
            $charset = 'UTF-8';
        }

        $name = encode_mailheader(strip_rn($name), 'phrase');
        $email = strip_rn($email);

        $subject = encode_mailheader(strip_rn($subject), 'text');

        $sep = !is_windows() ? "\n" : "\r\n";

        $body = str_replace("\r\n", "\n", $body);
        $body = str_replace("\r", "\n", $body);
        $body = str_replace("\n", $sep, $body);

        return mail("$name <$email>", $subject, $body,
            "From: $name <$email>".
            $sep.'X-Mailer: upm_pending_notify Textpattern plugin'.
            $sep.'Content-Transfer-Encoding: 8bit'.
            $sep.'Content-Type: text/plain; charset="'.$charset.'"'.
            $sep
        );
    }

//--------------------------------------------------------------

    function upm_pending_notify_gTxt($what, $vars = array())
    {
        $lang = array();

        $lang['email_subject_author']         = '[{sitename}] Article published: {title}';
        $lang['email_message_author']         = <<<eml
Dear {author},

{publisher} has published your article:

{title}
{article_url}
eml;

        $lang['email_subject_publisher'] = '[{sitename}] Article submitted: {title}';
        $lang['email_message_publisher'] = <<<eml
Dear {publisher},

{author} has submitted an article for review:

{title}
{article_url}
eml;

        return strtr($lang[$what], $vars);
    }?>