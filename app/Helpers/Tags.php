<?php
namespace Helpers;

/*
 * Tags Class
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.2
 * @date May 18 2015
 */

use Helpers\Database;

class Tags
{

    /**
     * clean functon to convert data into an array
     * @param  string $data contains the options
     * @return array array of option and values
     */
    public static function clean($data)
    {
        //replace spacer code for a space
        $data[1] = str_replace("&nbsp;", " ", $data[1]);
        $parts = explode(" ", $data[1]);
        $params = array();

        foreach ($parts as $part) {
            if (!empty($part)) {
                $part = trim($part);

                list($opt, $val) = explode("=", $part);

                //remove any quotes
                $val = str_replace('&quot;', '', $val);
                $val = str_replace('&rdquo;', '', $val);
                $val = str_replace('&rsquo;', '', $val);

                $params[$opt] = trim($val);
            }
        }
        return $params;
    }

    public static function get($string)
    {

        //current year
        $string = str_replace('[year]', date('Y'), $string);

        //name of website
        $string = str_replace('[sitetitle]', SITETITLE, $string);

        //site email address
        $string = str_replace('[siteemail]', SITEEMAIL, $string);

        //facebook like box
        $string = preg_replace_callback("(\[facebooklikebox(.*?)])is", function ($matches) {
            $params = tags::clean($matches);

            //if key exits use it
            $username  = (isset($params['username']) ? $params['username'] : '');
            $width  = (isset($params['width']) ? $params['width'] : '300px');
            $height  = (isset($params['height']) ? $params['height'] : '258px');
            $colorscheme  = (isset($params['colorscheme']) ? $params['colorscheme'] : 'light');
            $showfaces  = (isset($params['showfaces']) ? $params['showfaces'] : 'true');
            $header  = (isset($params['header']) ? $params['header'] : 'true');
            $stream  = (isset($params['stream']) ? $params['stream'] : 'false');
            $showborder  = (isset($params['showborder']) ? $params['showborder'] : 'false');
            $scrolling  = (isset($params['scrolling']) ? $params['scrolling'] : 'no');

            $likebox = "<iframe src='//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2F$username&amp;
            width=$width&amp;
            height=$height&amp;
            colorscheme=$colorscheme&amp;
            show_faces=$showfaces&amp;
            header=$header&amp;
            stream=$stream&amp;
            show_border=$showborder&amp;
            appId=262411407271009'
            scrolling='$scrolling'
            frameborder='0'
            style='border:none; overflow:hidden; width:$width; height:$height;'
            allowTransparency='true'></iframe>";
            return $likebox;
        }, $string);

        //feedburner subscribe form
        $string = preg_replace_callback("(\[feedburner(.*?)])is", function ($matches) {
            $params = tags::clean($matches);

            $username  = (isset($params['username']) ? $params['username'] : '');

            $form = "<form action='https://feedburner.google.com/fb/a/mailverify' method='post' target='popupwindow' onsubmit='window.open('http://feedburner.google.com/fb/a/mailverify?uri=$username', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true' class='navbar-form'>
            <input type='hidden' value='$username' name='uri'>
            <input type='hidden' name='loc' value='en_US'>
              <p><input type='text' class='form-control' placeholder='Enter Email Address' name='email' id='srch-term'></p>
              <p><input class='btn btn-success' type='submit' value='Subscribe by Email'></p>
            </form>";
            return $form;
        }, $string);

        //google plus box
        $string = preg_replace_callback("(\[googleplusbox(.*?)])is", function ($matches) {
            $params = tags::clean($matches);
            $username  = (isset($params['username']) ? $params['username'] : '');

            return "<script src='https://apis.google.com/js/platform.js' async defer></script>
                    <g:page href='https://plus.google.com/+$username'></g:page>";
        }, $string);

        //google adbox
        $string = preg_replace_callback("(\[googleadbox])is", function ($matches) {
            ob_start();
            ?>
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- dcsidebar -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-0401085377924210"
                 data-ad-slot="3065264368"
                 data-ad-format="auto"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            <?php
            $ad = ob_get_clean();
            return $ad;
        }, $string);

        //twitter follow button
        $string = preg_replace_callback("(\[twitterfollowbutton(.*?)])is", function ($matches) {
            $params = tags::clean($matches);

            if (!isset($params['count'])) {
                $params['count'] = null;
            }

            if (!isset($params['size'])) {
                $params['size'] = null;
            }

            //if key exits use it
            $username   = (isset($params['username']) ? $params['username'] : '');
            $count      = ($params['count'] == 'no' ? "data-show-count='false'" : '');
            $size       = ($params['size'] == 'large' ? "data-size='large'" : '');

            return "<a href='https://twitter.com/$username' class='twitter-follow-button' $count $size>Follow @$username</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>";
        }, $string);


        //twitter share button
        $string = preg_replace_callback("(\[twittersharebutton(.*?)])is", function ($matches) {
            $params = tags::clean($matches);

            if (!isset($params['count'])) {
                $params['count'] = null;
            }

            if (!isset($params['size'])) {
                $params['size'] = null;
            }

            //if key exits use it
            $count      = ($params['count'] == 'no' ? "data-count='none'" : '');
            $size       = ($params['size'] == 'large' ? "data-size='large'" : '');
            $via        = (isset($params['via']) ? "data-via='{$params['via']}'" : '');
            $hash       = (isset($params['hash']) ? "data-hashtags='{$params['hash']}'" : '');

            return "<a href='https://twitter.com/share' class='twitter-share-button' $count $size $via $hash>Tweet</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>";
        }, $string);


        //youtube embeds
        $string = preg_replace_callback("(\[youtube (.*?)])is", function ($matches) {
            $params = tags::clean($matches);

            //if key exits use it
            $video  = (isset($params['video']) ? $params['video'] : '');
            $video = str_replace("https://www.youtube.com/watch?v=", "", $video);
            $width  = (isset($params['width']) ? $params['width'] : '640');
            $height  = (isset($params['height']) ? $params['height'] : '360');

            return "<iframe width='$width' height='$height' src='//www.youtube.com/embed/$video' frameborder='0' allowfullscreen></iframe>";
        }, $string);

        //youtube subscribe
        $string = preg_replace_callback("(\[youtubesub(.*?)])is", function ($matches) {
            $params = tags::clean($matches);

            if (!isset($params['count'])) {
                $params['count'] = null;
            }

            if (!isset($params['size'])) {
                $params['size'] = null;
            }

            $username  = (isset($params['username']) ? $params['username'] : '');
            $layout  = ($params['layout'] == 'full' ? 'full' : 'default');
            $count  = ($params['count'] == 'no' ? 'hidden' : 'default');

            return "<script src='https://apis.google.com/js/platform.js'></script>
                <div class='g-ytsubscribe' data-channel='$username' data-layout='$layout' data-count='$count'></div>";
        }, $string);

        //vimeo embeds
        $string = preg_replace_callback("(\[vimeo (.*?)])is", function ($matches) {
            $params = tags::clean($matches);

            //if key exits use it
            $video  = (isset($params['video']) ? $params['video'] : '');
            $video = str_replace("https://vimeo.com/", "", $video);
            $width  = (isset($params['width']) ? $params['width'] : '640');
            $height  = (isset($params['height']) ? $params['height'] : '360');

            return "<iframe width='$width' height='$height' src='https://player.vimeo.com/video/$video' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>";
        }, $string);

        //pages get children
        $string = preg_replace_callback("(\[pages (.*?)])is", function ($matches) {
            $params = tags::clean($matches);
            $items = null;

            //if key exits use it
            $parent  = (isset($params['parent']) ? $params['parent'] : '');
            $class   = (isset($params['class']) ? $params['class'] : '');
            $id      = (isset($params['id']) ? $params['id'] : '');
            $subclass = (isset($params['subclass']) ? $params['subclass'] : '');
            $subid    = (isset($params['subid']) ? $params['subid'] : '');

            $db = Database::get();

            $q = $db->select("SELECT pageID,pageParent,pageMenuTitle,pageSlug FROM ".PREFIX."pages WHERE pageParent=:parent AND pageStandAlone='0' ORDER BY pageOrder", array(':parent' => $parent));

            //if page is more then 0 then show drop down menu
            if (count($q) > 0) {
                $items = "<ul class='$class' id='$id'>\n";
                foreach ($q as $row) {
                    //if slug is external use it
                    if (preg_match("/http/i", $row->pageSlug)) {
                        $rowSlug = $row->pageSlug;
                    } else {
                        //otherwise add in the DIR
                        $rowSlug = DIR.$row->pageSlug;
                    }

                    $items.="<li><a href='$rowSlug'>$row->pageMenuTitle</a>";

                    $q2 = $db->select("SELECT pageMenuTitle,pageSlug FROM ".PREFIX."pages WHERE pageParent=:parent AND pageStandAlone='0' ORDER BY pageOrder", array(':parent' => $row->pageID));
                    if (count($q2) > 0) {
                        $items.= "<ul class='$subclass' id='$subid'>\n";
                        foreach ($q2 as $row2) {
                            //if slug is external use it
                            if (preg_match("/http/i", $row2->pageSlug)) {
                                $row2Slug = $row2->pageSlug;
                            } else {
                                //otherwise add in the DIR
                                $row2Slug = DIR.$row2->pageSlug;
                            }

                            $items.="<li><a href='$row2Slug'>$row2->pageMenuTitle</a></li>";
                        }
                        $items.="</ul>";
                    }
                    $items.="</li>";

                }
                $items.="</ul>";
            }

            return $items;

        }, $string);

        return $string;

    }
}
