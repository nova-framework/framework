<?php
/**
 * Tags Class
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date Sept 19, 2015
 */

namespace Helpers;

use Helpers\Database;

/**
 * Collection of useful methods.
 */
class Tags
{
    /**
     * Clean function to convert data into an array.
     *
     * @param  string $data contains the options.
     *
     * @return array array of option and values.
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

    /**
     * Get
     *
     * @param  string $string content to scan
     *
     * @return string returns modified content
     */
    public static function get($string)
    {
        //current year
        $string = str_replace('[year]', date('Y'), $string);

        //name of website
        $string = str_replace('[sitetitle]', SITETITLE, $string);

        //site email address
        $string = str_replace('[siteemail]', SITEEMAIL, $string);

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

        return $string;

    }
}
