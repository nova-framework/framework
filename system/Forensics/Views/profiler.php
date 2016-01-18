<?php
/**
 * Profiler's View Fragment
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 16th, 2016
 */

?>
<style type="text/css">

.pQp { width:100%; text-align: center; position: fixed; bottom: 0; }
* html .pQp { position: absolute; }
.pQp * { margin: 0; padding: 0; border: none; }
#pQp { margin: 0 auto; width: 85%; min-width: 960px; background-color: #222; border: 3px solid #000; border-bottom: none; font-family: "Lucida Grande", Tahoma, Arial, sans-serif; -webkit-border-top-left-radius: 10px; -webkit-border-top-right-radius: 10px; -moz-border-radius-topleft: 10px; -moz-border-radius-topright: 10px; border-top-left-radius: 10px; border-top-right-radius: 10px; overflow: hidden; }
#pQp .pqp-box h3 { font-weight: normal; line-height: 200px; padding: 0 15px; color: #fff; }
.pQp, .pQp td { color: #444; }

/* ----- IDS ----- */
#pqp-metrics { background: #000; width: 100%; }
#pqp-console, #pqp-speed, #pqp-queries, #pqp-memory, #pqp-files, #pqp-variables { background: url(data:image/gif;base64,R0lGODlhAwADAIABAAAAAP///yH5BAEAAAEALAAAAAADAAMAAAIDRG5YADs=); border-top: 1px solid #ccc; height: 200px; overflow: auto; }

/* ----- Colors ----- */
.pQp .green { color: #588E13 !important; }
.pQp .blue { color: #3769A0 !important; }
.pQp .purple { color: #953FA1 !important; }
.pQp .orange { color: #D28C00 !important; }
.pQp .red { color: #B72F09 !important; }
.pQp .white { color: #FFFFFF !important; }

/* ----- Logic ----- */
#pQp, #pqp-console, #pqp-speed, #pqp-queries, #pqp-memory, #pqp-files, #pqp-variables { display: none; }
.pQp .console, .pQp .speed, .pQp .queries, .pQp .memory, .pQp .files, .pQp .variables { display: block !important; }
.pQp .console #pqp-console, .pQp .speed #pqp-speed, .pQp .queries #pqp-queries, .pQp .memory #pqp-memory, .pQp .files #pqp-files, .pQp .variables #pqp-variables { display: block; }
.console td.green, .speed td.blue, .queries td.purple, .memory td.orange, .files td.red, .variables td.white { background: #222 !important; border-bottom: 6px solid #fff !important; cursor:default !important; }

.tallDetails #pQp .pqp-box { height: 500px; }
.tallDetails #pQp .pqp-box h3 { line-height: 500px; }
.hideDetails #pQp .pqp-box { display: none !important; }
.hideDetails #pqp-footer { border-top:1px dotted #444; }
.hideDetails #pQp #pqp-metrics td { height: 30px; background: #000 !important; border-bottom: none !important; cursor: default !important; }
.hideDetails #pQp var { font-size: 14px; margin: 2px 0 0 0; }
.hideDetails #pQp h4 { font-size: 10px; }
.hideDetails .heightToggle { visibility: hidden; }

/* ----- Metrics ----- */
#pqp-metrics td { height: 50px; width: 15%; text-align: center; cursor: pointer; border: 1px solid #000; border-bottom: 6px solid #444; border-top-left-radius: 10px; border-top-right-radius: 10px; }
#pqp-metrics td:hover { background: #222; border-bottom: 6px solid #777; }
#pqp-metrics .green {  border-left: none; }
#pqp-metrics .red { border-right: none; }

#pqp-metrics h4 { text-shadow:#000 1px 1px 1px; }
.side var { text-shadow: #444 1px 1px 1px; }

.pQp var { font-size: 18px; font-weight: bold; font-style: normal; margin: 0; display: block; }
.pQp h4 { font-size: 12px; color: #fff; margin: 0 0 4px 0; }

/* ----- Main ----- */
.pQp .main { width: 80%; }
*+html .pQp .main { width: 78%; }
* html .pQp .main { width: 77%; }
.pQp .main td { padding: 7px 15px; text-align: left; background: #151515; border-left: 1px solid #333; border-right: 1px solid #333; border-bottom: 1px dotted #323232; color: #FFF; }
.pQp .main td, pre { font-family: Monaco, "Consolas", "Lucida Console", "Courier New", monospace; font-size: 11px; }
.pQp .main td.alt { background: #111; }
.pQp .main tr.alt td { background: #2E2E2E; border-top: 1px dotted #4E4E4E; }
.pQp .main tr.alt td.alt { background:#333; }
.pQp .main td b { float: right; font-weight: normal; color: #E6F387; }
.pQp .main td:hover { background: #2E2E2E; }

/* ----- Side ----- */
.pQp .side { float: left; width: 20%; background: #000; color: #fff; border-bottom-left-radius: 30px; text-align: center; }
.pQp .side td { padding: 10px 0 5px 0; background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAHRCAYAAABTvCjlAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAG9JREFUeNrsykEKgEAQA8EZQUVW/P9v1+wPPHqoQJ9SPees7t6qarWnI53pSiPd6Vnnp4EgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIK/hK8AAwDLrAbmTOcZRgAAAABJRU5ErkJggg==) repeat-y right; }
.pQp .side var {color: #fff; font-size: 15px; }
.pQp .side h4 { font-weight: normal; color: #F4FCCA; font-size: 11px; }

/* ----- Console ----- */
#pqp-console .side td { padding: 12px 0; }
#pqp-console .side td.alt1 { background: #588E13; width: 51%; }
#pqp-console .side td.alt2 { background-color: #B72F09; }
#pqp-console .side td.alt3 { display : block; background: #D28C00; border-bottom: 1px solid #9C6800; border-left: 1px solid #9C6800; border-bottom-left-radius: 30px; }
#pqp-console .side td.alt4 { background-color: #3769A0; border-bottom: 1px solid #274B74; }

#pqp-console .main table { width: 100%; }
#pqp-console td div { width: 100%; overflow: hidden; }
#pqp-console td.type { font-family: "Lucida Grande", Tahoma, Arial, sans-serif; text-align:center; text-transform: uppercase; font-size: 9px; padding-top: 9px; color: #F4FCCA; vertical-align: top; width: 40px; }
.pQp .log-log td.type { background: #47740D !important; }
.pQp .log-error td.type { background: #9B2700 !important; }
.pQp .log-memory td.type { background:#D28C00 !important; }
.pQp .log-speed td.type { background:#2B5481 !important; }

.pQp .log-log pre { white-space: normal; display: block; color: #FFFD70; background: none; }
.pQp .log-log td:hover pre { color: #fff; }

.pQp .log-memory em, .pQp .log-speed em { float: left; font-style: normal; display: block; color: #fff; }
.pQp .log-memory pre, .pQp .log-speed pre { float: right; white-space: normal; display: block; color: #FFFD70; background: none; }

/* ----- Speed ----- */
#pqp-speed .side td { padding: 12px 0; }
#pqp-speed .side { background-color: #3769A0; }
#pqp-speed .side td.alt { display : block; background-color: #2B5481; border-bottom: 1px solid #1E3C5C; border-left: 1px solid #1E3C5C; border-bottom-left-radius: 30px; }

/* ----- Queries ----- */
#pqp-queries .side { background-color: #953FA1; }
#pqp-queries .side td.alt { background-color: #7B3384; }
#pqp-queries .side td.last { display : block; border-bottom: 1px solid #662A6E; border-left: 1px solid #662A6E; border-bottom-left-radius: 30px; }
#pqp-queries .main b { float: none; }
#pqp-queries .main em { display: block; padding: 2px 0 0 0; font-style: normal; color: #aaa; }

/* ----- Memory ----- */
#pqp-memory .side td { padding: 12px 0; }
#pqp-memory .side{ background-color: #C48200; }
#pqp-memory .side td.alt { display : block; background-color: #AC7200; border-bottom: 1px solid #865900; border-left: 1px solid #865900; border-bottom-left-radius: 30px; }

/* ----- Files ----- */
#pqp-files .side { background-color: #B72F09; }
#pqp-files .side td.alt { background-color: #9B2700; }
#pqp-files .side td.last { display : block; border-bottom: 1px solid #7C1F00; border-left: 1px solid #7C1F00; border-bottom-left-radius: 30px; }

/* ----- Variables ----- */

/* ----- Footer ----- */
#pqp-footer { width: 100%; background: #000; font-size: 11px; border-top: 1px solid #ccc; }
#pqp-footer td { padding: 0 !important; border: none !important; }
#pqp-footer strong { color: #fff; }
#pqp-footer a { color: #999; padding: 5px 10px; text-decoration: none; }
#pqp-footer .credit { width: 20%; text-align: left; }
#pqp-footer .credit .logo { padding: 5px 10px; }
#pqp-footer .actions { width: 80%; text-align: right; }
#pqp-footer .actions a { font-weight: bold; float: right; width: auto; }
#pqp-footer a:hover, #pqp-footer a:hover strong, #pqp-footer a:hover b { background: #fff; color: blue !important; }
#pqp-footer a:active, #pqp-footer a:active strong, #pqp-footer a:active b { background: #ECF488; color: green !important; }
</style>

<!-- JavaScript -->
<script type="text/javascript">
    var PQP_DETAILS = true;
    var PQP_HEIGHT = "short";

    addEvent(window, 'load', setProfilerState);

    function changeTab(tab) {
        var pQp = document.getElementById('pQp');

        hideAllTabs();

        addClassName(pQp, tab, true);
    }

    function hideAllTabs() {
        var pQp = document.getElementById('pQp');

        removeClassName(pQp, 'console');
        removeClassName(pQp, 'speed');
        removeClassName(pQp, 'queries');
        removeClassName(pQp, 'memory');
        removeClassName(pQp, 'files');
        removeClassName(pQp, 'variables');
    }

    function toggleDetails(){
        var container = document.getElementById('pqp-container');

        if(PQP_DETAILS){
            addClassName(container, 'hideDetails', true);

            PQP_DETAILS = false;
        }
        else{
            removeClassName(container, 'hideDetails');

            PQP_DETAILS = true;
        }
    }
    function toggleHeight(){
        var container = document.getElementById('pqp-container');

        if(PQP_HEIGHT == "short"){
            addClassName(container, 'tallDetails', true);

            PQP_HEIGHT = "tall";
        }
        else{
            removeClassName(container, 'tallDetails');

            PQP_HEIGHT = "short";
        }
    }

    function showProfiler() {
        setTimeout(function(){document.getElementById("pqp-container").style.display = "block"}, 10);
        setTimeout(function(){document.getElementById("pqp-button").style.display = "none"}, 10);

        setCookie('open');
    }

    function hideProfiler() {
        setTimeout(function(){document.getElementById("pqp-container").style.display = "none"}, 10);
        setTimeout(function(){document.getElementById("pqp-button").style.display = "block"}, 10);

        setCookie('closed');
    }

    //http://www.bigbold.com/snippets/posts/show/2630
    function addClassName(objElement, strClass, blnMayAlreadyExist){
       if ( objElement.className ){
          var arrList = objElement.className.split(' ');
          if ( blnMayAlreadyExist ){
             var strClassUpper = strClass.toUpperCase();
             for ( var i = 0; i < arrList.length; i++ ){
                if ( arrList[i].toUpperCase() == strClassUpper ){
                   arrList.splice(i, 1);
                   i--;
                 }
               }
          }
          arrList[arrList.length] = strClass;
          objElement.className = arrList.join(' ');
       }
       else{
          objElement.className = strClass;
          }
    }

    //http://www.bigbold.com/snippets/posts/show/2630
    function removeClassName(objElement, strClass){
       if ( objElement.className ){
          var arrList = objElement.className.split(' ');
          var strClassUpper = strClass.toUpperCase();
          for ( var i = 0; i < arrList.length; i++ ){
             if ( arrList[i].toUpperCase() == strClassUpper ){
                arrList.splice(i, 1);
                i--;
             }
          }
          objElement.className = arrList.join(' ');
       }
    }

    //http://ejohn.org/projects/flexible-javascript-events/
    function addEvent( obj, type, fn ) {
      if ( obj.attachEvent ) {
        obj["e"+type+fn] = fn;
        obj[type+fn] = function() { obj["e"+type+fn]( window.event ) };
        obj.attachEvent( "on"+type, obj[type+fn] );
      }
      else{
        obj.addEventListener( type, fn, false );
      }
    }

    function readCookie() {
        var nameEQ = "Profiler=";

        var ca = document.cookie.split(';');

        for (var i=0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function setCookie(value) {
        var date = new Date();
        date.setTime(date.getTime() + (365*24*60*60*1000));
        var expires = "; expires=" + date.toGMTString();

        document.cookie = "Profiler=" + value + expires + "; path=/";
    }

    function setProfilerState() {
        var cookie_state = readCookie();

        if (cookie_state == 'open') {
            showProfiler();
        } else {
            hideProfiler();
        }
    }
</script>
<!--
<img id="pqp-button" style="z-index: 10001; width: 48px; height: 48px; position: fixed; cursor: pointer; bottom: 1em; left: 1em;" onclick="showProfiler(); return false;" title="" alt="" src="data:image/png;base64,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" />
-->
<img  id="pqp-button" style="z-index: 10001; display: none; width: 64px; height: 64px; position: fixed; cursor: pointer; bottom: 1em; left: 1em;" onclick="showProfiler(); return false;" title="" alt="" src="data:image/png;base64,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" />

<div id="pqp-container" class="pQp" style="display: none;">
<div id="pQp" class="console">
    <table id="pqp-metrics" cellspacing="0">
        <tr>
            <td class="green" onclick="changeTab('console');">
                <var><?= $logCount; ?></var>
                <h4><?= __d('system', 'Console'); ?></h4>
            </td>
            <td class="blue" onclick="changeTab('speed');">
                <var><?= $speedTotal; ?></var>
                <h4><?= __d('system', 'Load Time'); ?></h4>
            </td>
            <td class="purple" onclick="changeTab('queries');">
                <var><?= __d('system', '{0} Queries', $queryCount); ?></var>
                <h4><?= __d('system', 'Database'); ?></h4>
            </td>
            <td class="orange" onclick="changeTab('memory');">
                <var><?= $memoryUsed; ?></var>
                <h4><?= __d('system', 'Memory Used'); ?></h4>
            </td>
            <td class="red" onclick="changeTab('files');">
                <var><?= __d('system', '{0} Files', $fileCount); ?></var>
                <h4><?= __d('system', 'Included'); ?></h4>
            </td>
            <td class="white" onclick="changeTab('variables');">
                <var><?= __d('system', 'Variables'); ?></var>
                <h4><?= __d('system', '& Server Headers'); ?></h4>
            </td>
        </tr>
    </table>

    <div id='pqp-console' class='pqp-box'>
        <?php if ($logCount == 0) { ?>
            <h3><?= __d('system', 'This panel has no log items.'); ?></h3>
        <?php } else { ?>
            <table class='side' cellspacing='0'>
            <tr>
                <td class='alt1'><var><?= $output['logs']['logCount']; ?></var> <h4><?= __d('system', 'Logs'); ?></h4></td>
                <td class='alt2'><var><?= $output['logs']['errorCount']; ?></var> <h4><?= __d('system', 'Errors'); ?></h4></td>
            </tr>
            <tr>
                <td class='alt3'><var><?= $output['logs']['memoryCount']; ?></var> <h4><?= __d('system', 'Memory'); ?></h4></td>
                <td class='alt4'><var><?= $output['logs']['speedCount']; ?></var> <h4><?= __d('system', 'Speed'); ?></h4></td>
            </tr>
            </table>
            <table class='main' cellspacing='0'>
                <?php $class = ''; ?>
                <?php foreach($output['logs']['console'] as $log) { ?>
                    <tr class='log-<?= $log['type']; ?>'>
                        <td class='type'><?= $log['type']; ?></td>
                        <td class="<?= $class; ?>">
                            <?php if($log['type'] == 'log') { ?>
                                <div><pre><?= $log['data'] ?></pre></div>
                            <?php } else if($log['type'] == 'memory') { ?>
                                <div><pre><?= $log['data']; ?></pre> <em><?= $log['dataType']; ?></em>: <?= $log['name']; ?> </div>
                            <?php } else if($log['type'] == 'speed') { ?>
                                <div><pre><?= $log['data']; ?></pre> <em><?= $log['name']; ?></em></div>
                            <?php } else if($log['type'] == 'error') { ?>
                                <div><em>Line <?= $log['line']; ?></em> : <?= $log['data']; ?> <pre><?= $log['file']; ?></pre></div>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php $class = ($class == '') ? 'alt' : ''; ?>
                <?php } ?>
            </table>
        <?php } ?>
    </div>

    <div id="pqp-speed" class="pqp-box">
        <?php if ($speedTotal == 0) { ?>
            <h3><?= __d('system', 'This panel has no log items.'); ?></h3>
        <?php } else { ?>
            <table class='side' cellspacing='0'>
                <tr><td><var><?= $output['speedTotals']['total']; ?></var><h4><?= __d('system', 'Load Time'); ?></h4></td></tr>
                <tr><td class='alt'><var><?= $output['speedTotals']['allowed']; ?> s</var> <h4><?= __d('system', 'Max Execution Time'); ?></h4></td></tr>
            </table>

            <table class='main' cellspacing='0'>
            <?php $class = ''; ?>
            <?php foreach($output['logs']['console'] as $log) { ?>
                <?php if($log['type'] == 'speed') { ?>
                    <tr class='log-<?= $log['type']; ?>'>
                        <td class="<?= $class; ?>"><b><?= $log['data']; ?></b> <?= $log['name']; ?></td>
                    </tr>
                    <?php $class = ($class == '') ? 'alt' : ''; ?>
                <?php } ?>
            <?php } ?>
            </table>
        <?php } ?>
    </div>

    <div id='pqp-queries' class='pqp-box'>
        <?php if($output['queryTotals']['count'] ==  0) { ?>
            <h3><?= __d('system', 'This panel has no log items.'); ?></h3>
        <?php } else { ?>
            <table class='side' cellspacing='0'>
            <tr><td><var><?= $output['queryTotals']['count'] ?></var><h4><?= __d('system', 'Total Queries'); ?></h4></td></tr>
            <tr><td class='alt'><var><?= $output['queryTotals']['time'] ?></var> <h4><?= __d('system', 'Total Time'); ?></h4></td></tr>
            <tr><td class='last'><var>0</var> <h4><?= __d('system', 'Duplicates'); ?></h4></td></tr>
            </table>

                <table class='main' cellspacing='0'>
                <?php $class = ''; ?>
                <?php foreach($output['queries'] as $query) { ?>
                        <tr>
                            <td class="<?= $class; ?>">
                                <?= $query['sql']; ?>
                                <?php if(isset($query['explain'])) { ?>
                                <em>
                                    <?= __d('system', 'Possible keys: <b>{0}</b>', isset($query['explain']['possible_keys']) ? $query['explain']['possible_keys'] : ''); ?> &middot;
                                    <?= __d('system', 'Key Used: <b>{0}</b>', isset($query['explain']['key']) ? $query['explain']['key'] : ''); ?> &middot;
                                    <?= __d('system', 'Type: <b>{0}</b>', isset($query['explain']['type']) ? $query['explain']['type'] : ''); ?> &middot;
                                    <?= __d('system', 'Rows: <b>{0}</b>', isset($query['explain']['rows']) ? $query['explain']['rows'] : ''); ?> &middot;
                                    <?= __d('system', 'Speed: <b>{0}</b>', $query['time']); ?>
                                </em>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php $class = ($class == '') ? 'alt' : ''; ?>
                <?php } ?>
                </table>
        <?php } ?>
    </div>

    <div id="pqp-memory" class="pqp-box">
        <?php if($output['logs']['memoryCount'] == 0) { ?>
            <h3><?= __d('system', 'This panel has no log items.'); ?></h3>
        <?php } else { ?>
            <table class='side' cellspacing='0'>
                <tr><td><var><?= $output['memoryTotals']['used']; ?></var><h4><?= __d('system', 'Used Memory'); ?></h4></td></tr>
                <tr><td class='alt'><var><?= $output['memoryTotals']['total']; ?></var> <h4><?= __d('system', 'Total Available'); ?></h4></td></tr>
            </table>

            <table class='main' cellspacing='0'>
            <?php $class = ''; ?>
            <?php foreach($output['logs']['console'] as $log) { ?>
                <?php if($log['type'] == 'memory') { ?>
                    <tr class='log-<?= $log['type']; ?>'>
                        <td class="<?= $class; ?>"><b><?= $log['data']; ?></b> <em><?= $log['dataType']; ?></em>: <?= $log['name']; ?></td>
                    </tr>
                <?php } ?>
                <?php $class = ($class == '') ? 'alt' : ''; ?>
            <?php } ?>
            </table>
        <?php } ?>
    </div>

    <div id='pqp-files' class='pqp-box'>
            <table class='side' cellspacing='0'>
                <tr><td><var><?= $output['fileTotals']['count']; ?></var><h4><?= __d('system', 'Total Files'); ?></h4></td></tr>
                <tr><td class='alt'><var><?= $output['fileTotals']['size']; ?></var> <h4><?= __d('system', 'Total Size'); ?></h4></td></tr>
                <tr><td class='last'><var><?= $output['fileTotals']['largest']; ?></var> <h4><?= __d('system', 'Largest'); ?></h4></td></tr>
            </table>
            <table class='main' cellspacing='0'>
                <?php $class = ''; ?>
                <?php foreach($output['files'] as $file) { ?>
                    <tr><td class="<?= $class; ?>"><b><?= $file['size']; ?></b> <?= $file['name']; ?></td></tr>
                    <?php $class = ($class == '') ? 'alt' : ''; ?>
                <?php } ?>
            </table>
    </div>

    <div id='pqp-variables' class='pqp-box'>
        <?php if(empty($output['variables'])) { ?>
            <h3><?= __d('system', 'This panel has no log items.'); ?></h3>
        <?php } else { ?>
            <?php $sections = $output['variables']; ?>
            <?php foreach(array('get', 'post', 'headers') as $section) { ?>
                <?php
                    if ($section == 'get') {
                        $title = __d('system', 'GET Variables');
                    } else if($section == 'post') {
                        $title = __d('system', 'POST Variables');
                    } else if($section == 'headers') {
                        $title = __d('system', 'Server Headers');
                    }
                ?>
                <h3 style="text-align: left; font-size: 16px; font-weight: bold; line-height: 40px;"><?= $title; ?></h3>
            <table class='main' cellspacing='0' style="width: 100%; margin-bottom: 25px;">
                <?php $class = ''; ?>
                <?php if (is_array($sections[$section])) { ?>
                    <?php foreach($sections[$section] as $key => $value) { ?>
                        <tr><td class="<?= $class; ?>" style="width: 33%; vertical-align: middle;"><b><?= $key; ?></td><td class="<?= $class; ?>" style="vertical-align: middle;"></b> <?=  $value; ?></td></tr>
                        <?php $class = ($class == '') ? 'alt' : ''; ?>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td class="<?= $class; ?>"><h5 class="orange" style="text-align: center; vertical-align: middle; font-weight: bold;"><?= $sections[$section]; ?></h5></td></tr>
                <?php } ?>
            </table>
            <?php } ?>
        <?php } ?>
    </div>

    <table id="pqp-footer" cellspacing="0">
        <tr>
            <td class="credit">
                <div class="logo"><strong>Nova Forensics - Profiler</strong></div>
            </td>
            <td class="actions">
                <a href="#" onclick="hideProfiler(); return false"><?= __d('system', 'Hide'); ?></a>
                <a href="#" onclick="toggleDetails(); return false"><?= __d('system', 'Details'); ?></a>
                <a class="heightToggle" href="#" onclick="toggleHeight(); return false"><?= __d('system', 'Height'); ?></a>
            </td>
        </tr>
    </table>
</div>
</div>
