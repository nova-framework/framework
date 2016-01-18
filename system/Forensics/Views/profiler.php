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
#pQp { margin: 0 auto; width: 85%; min-width: 960px; background-color: #222; border:3px solid #000; border-bottom: none; font-family: "Lucida Grande", Tahoma, Arial, sans-serif; -webkit-border-top-left-radius: 4px; -webkit-border-top-right-radius: 4px; -moz-border-radius-topleft: 6px; -moz-border-radius-topright: 6px; border-top-left-radius: 6px; border-top-right-radius: 6px; overflow: hidden; }
#pQp .pqp-box h3 { font-weight: normal; line-height: 200px; padding: 0 15px; color: #fff; }
.pQp, .pQp td { color: #444; }

/* ----- IDS ----- */
#pqp-metrics { background: #000; width:100%; }
#pqp-console, #pqp-speed, #pqp-queries, #pqp-memory, #pqp-files { background: url(data:image/gif;base64,R0lGODlhAwADAIABAAAAAP///yH5BAEAAAEALAAAAAADAAMAAAIDRG5YADs=); border-top: 1px solid #ccc; height: 200px; overflow: auto; }

/* ----- Colors ----- */
.pQp .green { color: #588E13 !important; }
.pQp .blue { color: #3769A0 !important; }
.pQp .purple { color: #953FA1 !important; }
.pQp .orange { color: #D28C00 !important; }
.pQp .red { color: #B72F09 !important; }

/* ----- Logic ----- */
#pQp, #pqp-console, #pqp-speed, #pqp-queries, #pqp-memory, #pqp-files { display: none; }
.pQp .console, .pQp .speed, .pQp .queries, .pQp .memory, .pQp .files { display: block !important; }
.pQp .console #pqp-console, .pQp .speed #pqp-speed, .pQp .queries #pqp-queries, .pQp .memory #pqp-memory, .pQp .files #pqp-files { display: block; }
.console td.green, .speed td.blue, .queries td.purple, .memory td.orange, .files td.red { background: #222 !important; border-bottom: 6px solid #fff !important; cursor:default !important; }

.tallDetails #pQp .pqp-box { height: 500px; }
.tallDetails #pQp .pqp-box h3 { line-height: 500px; }
.hideDetails #pQp .pqp-box { display: none !important; }
.hideDetails #pqp-footer { border-top:1px dotted #444; }
.hideDetails #pQp #pqp-metrics td { height: 40px; background: #000 !important; border-bottom: none !important; cursor: default !important; }
.hideDetails #pQp var { font-size: 16px; margin: 0 0 2px 0; }
.hideDetails #pQp h4 { font-size: 10px; }
.hideDetails .heightToggle { visibility: hidden; }

/* ----- Metrics ----- */
#pqp-metrics td { height: 50px; width: 20%; text-align: center; cursor: pointer; border: 1px solid #000; border-bottom: 6px solid #444; -webkit-border-top-left-radius: 10px; -moz-border-radius-topleft: 10px; -webkit-border-top-right-radius: 10px; -moz-border-radius-topright: 10px;}
#pqp-metrics td:hover { background: #222; border-bottom: 6px solid #777; }
#pqp-metrics .green {  border-left: none; }
#pqp-metrics .red { border-right: none; }

#pqp-metrics h4 { text-shadow:#000 1px 1px 1px; }
.side var { text-shadow: #444 1px 1px 1px; }

.pQp var { font-size: 20px; font-weight: bold; font-style: normal; margin: 0 0 3px 0; display: block; }
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
.pQp .side{ float: left; width: 20%; background: #000; color: #fff; -webkit-border-bottom-left-radius: 30px; -moz-border-radius-bottomleft: 30px; text-align: center; }
.pQp .side td { padding: 10px 0 5px 0; background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAHRCAYAAABTvCjlAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAG9JREFUeNrsykEKgEAQA8EZQUVW/P9v1+wPPHqoQJ9SPees7t6qarWnI53pSiPd6Vnnp4EgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIK/hK8AAwDLrAbmTOcZRgAAAABJRU5ErkJggg==) repeat-y right; }
.pQp .side var {color: #fff; font-size: 15px; }
.pQp .side h4 { font-weight: normal; color: #F4FCCA; font-size: 11px; }

/* ----- Console ----- */
#pqp-console .side td { padding: 12px 0; }
#pqp-console .side td.alt1 { background: #588E13; width: 51%; }
#pqp-console .side td.alt2 { background-color: #B72F09; }
#pqp-console .side td.alt3 { background: #D28C00; border-bottom: 1px solid #9C6800; border-left: 1px solid #9C6800; -webkit-border-bottom-left-radius: 30px; -moz-border-radius-bottomleft: 30px; }
#pqp-console .side td.alt4 { background-color: #3769A0; border-bottom: 1px solid #274B74; }

#pqp-console .main table { width: 100%; }
#pqp-console td div { width: 100%; overflow: hidden; }
#pqp-console td.type { font-family: "Lucida Grande", Tahoma, Arial, sans-serif; text-align:center; text-transform: uppercase; font-size: 9px; padding-top: 9px; color: #F4FCCA; vertical-align: top; width: 40px; }
.pQp .log-log td.type { background: #47740D !important; }
.pQp .log-error td.type { background: #9B2700 !important; }
.pQp .log-memory td.type { background:#D28C00 !important; }
.pQp .log-speed td.type { background:#2B5481 !important; }

.pQp .log-log pre { color: #999; }
.pQp .log-log td:hover pre { color: #fff; }

.pQp .log-memory em, .pQp .log-speed em { float: left; font-style: normal; display: block; color: #fff; }
.pQp .log-memory pre, .pQp .log-speed pre { float: right; white-space: normal; display: block; color: #FFFD70; background: none; }

/* ----- Speed ----- */
#pqp-speed .side td { padding: 12px 0; }
#pqp-speed .side { background-color: #3769A0; }
#pqp-speed .side td.alt { background-color: #2B5481; border-bottom: 1px solid #1E3C5C; border-left: 1px solid #1E3C5C; -webkit-border-bottom-left-radius: 30px; -moz-border-radius-bottomleft: 30px; }

/* ----- Queries ----- */
#pqp-queries .side{ background-color: #953FA1; border-bottom: 1px solid #662A6E; border-left: 1px solid #662A6E; }
#pqp-queries .side td.alt { background-color: #7B3384; }
#pqp-queries .main b{ float: none; }
#pqp-queries .main em { display: block; padding: 2px 0 0 0; font-style: normal; color: #aaa; }

/* ----- Memory ----- */
#pqp-memory .side td { padding: 12px 0; }
#pqp-memory .side{ background-color: #C48200; }
#pqp-memory .side td.alt { background-color: #AC7200; border-bottom: 1px solid #865900; border-left: 1px solid #865900; -webkit-border-bottom-left-radius: 30px; -moz-border-radius-bottomleft: 30px; }

/* ----- Files ----- */
#pqp-files .side { background-color: #B72F09; border-bottom: 1px solid #7C1F00; border-left: 1px solid #7C1F00; }
#pqp-files .side td.alt { background-color: #9B2700; }

/* ----- Footer ----- */
#pqp-footer { width: 100%; background: #000; font-size: 11px; border-top: 1px solid #ccc; }
#pqp-footer td { padding: 0 !important; border: none !important; }
#pqp-footer strong { color: #fff; }
#pqp-footer a { color: #999; padding: 5px 10px; text-decoration: none; }
#pqp-footer .credit { width: 20%; text-align: left; }
#pqp-footer .actions { width: 80%; text-align: right; }
#pqp-footer .actions a { float: right; width: auto; }
#pqp-footer a:hover, #pqp-footer a:hover strong, #pqp-footer a:hover b { background: #fff; color: blue !important; text-decoration: underline; }
#pqp-footer a:active, #pqp-footer a:active strong, #pqp-footer a:active b { background: #ECF488; color: green !important; }
</style>

<!-- JavaScript -->
<script type="text/javascript">
    var PQP_DETAILS = true;
    var PQP_HEIGHT = "short";

    addEvent(window, 'load', showProfiler);
    addEvent(window, 'load', toggleDetails);

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
</script>

<div id="pqp-container" class="pQp" style="display: none;">
<div id="pQp" class="console">
    <table id="pqp-metrics" cellspacing="0">
        <tr>
            <td class="green" onclick="changeTab('console');">
                <var><?= $logCount; ?></var>
                <h4>Console</h4>
            </td>
            <td class="blue" onclick="changeTab('speed');">
                <var><?= $speedTotal; ?></var>
                <h4>Load Time</h4>
            </td>
            <td class="purple" onclick="changeTab('queries');">
                <var><?= $queryCount; ?> Queries</var>
                <h4>Database</h4>
            </td>
            <td class="orange" onclick="changeTab('memory');">
                <var><?= $memoryUsed; ?></var>
                <h4>Memory Used</h4>
            </td>
            <td class="red" onclick="changeTab('files');">
                <var><?= $fileCount; ?> Files</var>
                <h4>Included</h4>
            </td>
        </tr>
    </table>

    <div id='pqp-console' class='pqp-box'>
        <?php if ($logCount == 0) { ?>
            <h3>This panel has no log items.</h3>
        <?php } else { ?>
            <table class='side' cellspacing='0'>
            <tr>
                <td class='alt1'><var><?= $output['logs']['logCount']; ?></var><h4>Logs</h4></td>
                <td class='alt2'><var><?= $output['logs']['errorCount']; ?></var> <h4>Errors</h4></td>
            </tr>
            <tr>
                <td class='alt3'><var><?= $output['logs']['memoryCount']; ?></var> <h4>Memory</h4></td>
                <td class='alt4'><var><?= $output['logs']['speedCount']; ?></var> <h4>Speed</h4></td>
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
            <h3>This panel has no log items.</h3>
        <?php } else { ?>
            <table class='side' cellspacing='0'>
                <tr><td><var><?= $output['speedTotals']['total']; ?></var><h4>Load Time</h4></td></tr>
                <tr><td class='alt'><var><?= $output['speedTotals']['allowed']; ?> s</var> <h4>Max Execution Time</h4></td></tr>
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
            <h3>This panel has no log items.</h3>
        <?php } else { ?>
            <table class='side' cellspacing='0'>
            <tr><td><var><?= $output['queryTotals']['count'] ?></var><h4>Total Queries</h4></td></tr>
            <tr><td class='alt'><var><?= $output['queryTotals']['time'] ?></var> <h4>Total Time</h4></td></tr>
            <tr><td><var>0</var> <h4>Duplicates</h4></td></tr>
            </table>

                <table class='main' cellspacing='0'>
                <?php $class = ''; ?>
                <?php foreach($output['queries'] as $query) { ?>
                        <tr>
                            <td class="<?= $class; ?>">
                                <?= $query['sql']; ?>
                                <?php if(isset($query['explain'])) { ?>
                                <em>
                                    Possible keys: <b><?= $query['explain']['possible_keys']; ?></b> &middot;
                                    Key Used: <b><?= $query['explain']['key']; ?></b> &middot;
                                    Type: <b><?= $query['explain']['type']; ?></b> &middot;
                                    Rows: <b><?= $query['explain']['rows']; ?></b> &middot;
                                    Speed: <b><?= $query['time']; ?></b>
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
            <h3>This panel has no log items.</h3>
        <?php } else { ?>
            <table class='side' cellspacing='0'>
                <tr><td><var><?= $output['memoryTotals']['used']; ?></var><h4>Used Memory</h4></td></tr>
                <tr><td class='alt'><var><?= $output['memoryTotals']['total']; ?></var> <h4>Total Available</h4></td></tr>
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
                <tr><td><var><?= $output['fileTotals']['count']; ?></var><h4>Total Files</h4></td></tr>
                <tr><td class='alt'><var><?= $output['fileTotals']['size']; ?></var> <h4>Total Size</h4></td></tr>
                <tr><td><var><?= $output['fileTotals']['largest']; ?></var> <h4>Largest</h4></td></tr>
            </table>
            <table class='main' cellspacing='0'>
                <?php $class = ''; ?>
                <?php foreach($output['files'] as $file) { ?>
                    <tr><td class="<?= $class; ?>"><b><?= $file['size']; ?></b> <?= $file['name']; ?></td></tr>
                    <?php $class = ($class == '') ? 'alt' : ''; ?>
                <?php } ?>
            </table>
    </div>

    <table id="pqp-footer" cellspacing="0">
        <tr>
            <td class="credit">
                <a href="http://particletree.com/features/php-quick-profiler/" target="_blank">
                <strong>Nova</strong>
                <b class="green">Q</b><b class="blue">u</b><b class="purple">i</b><b class="orange">c</b><b class="red">k</b>
                Profiler</a></td>
            <td class="actions">
                <a href="#" onclick="toggleDetails();return false">Details</a>
                <a class="heightToggle" href="#" onclick="toggleHeight();return false">Height</a>
            </td>
        </tr>
    </table>
</div>
</div>
