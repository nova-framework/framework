<section class="content-header">
    <h1><?= __d('chat', 'Chat'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('chat', 'Dashboard'); ?></a></li>
        <li><?= __d('system', 'Chat'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="row">
    <div class="col-md-9">
        <div class="box box-primary direct-chat direct-chat-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('users', 'WebRTC Chat'); ?></h3>
            </div>
            <div class="box-body">
                <div class="direct-chat-messages" id="chat-output" style="height: 550px;">
                    <div id="chat-spinner" align="center" style="padding-top: 40px;">
                        <i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw" style="color: #d2d6de;"></i>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="input-group">
                    <input type="text" id="chat-input" class="form-control" placeholder="<?= __d('chat', 'Type Message ...'); ?>" disabled="disabled">
                    <span class="input-group-btn">
                        <button type="submit" id="chat-button" class="btn btn-primary btn-flat" disabled="disabled"><?= __d('chat', 'Send'); ?></button>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('users', 'On-line Users'); ?></h3>
            </div>
            <div class="box-body">
                <div id="chat-list" style="height: 586px; overflow:auto;">
                    <p id="chat-alone" align="center"><?= __d('chat', 'No Users are on-line.'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

</section>

<script src="https://webrtcexperiment-webrtc.netdna-ssl.com/DataChannel.js"> </script>
<script src="https://webrtcexperiment-webrtc.netdna-ssl.com/socket.io.js"> </script>

<script>

var userInfo = {
    userid:   '<?= $authUser->id; ?>',
    username: '<?= $authUser->username; ?>',
    realname: '<?= $authUser->realname(); ?>',
    roles:    '<?= implode(', ', $authUser->roles->lists("name")); ?>',
    picture:  '<?= $authUser->picture(); ?>'
};

//-------------------------------------------------------
// UI Code
//-------------------------------------------------------


var chatOutput = $('#chat-output');
var chatInput = $('#chat-input');
var chatButton = $('#chat-button');
var chatList = $('#chat-list');

//
chatInput.keypress(function (e) {
    if (e.keyCode != 13) return;

    sendMessage();
});

chatButton.click(function (e) {
    sendMessage();
});


//-------------------------------------------------------
// TextChat Code
// ......................................................

var addLogMessage = function(message, type) {
    var value = '<div class="callout callout-' + type + '" style="padding: 6px 12px 6px 12px;">' + message + '<span class="pull-right">' + getTimestamp() + '</span></div>' +
                '<div class="clearfix"></div>'

    // Hide the spinner.
    $('#chat-spinner').hide();

    chatOutput.append(value);

    chatOutput.scrollTop(chatOutput.height());
}

var addChatMessage = function(message, name, image, position) {
    var position = position ? position : 'left';

    var reverse = (position == 'left') ? 'right' : 'left';

    var value = '<div class="direct-chat-msg ' + position + '">' +
                '  <div class="direct-chat-info clearfix">' +
                '    <span class="direct-chat-name pull-' + position + '">' + name + '</span>' +
                '    <span class="direct-chat-timestamp pull-' + reverse + '">' + getTimestamp() + '</span>' +
                '  </div>' +
                '  <img class="direct-chat-img" src="'+ image +'" alt="User Picture">' +
                '  <div class="direct-chat-text">' +
                message +
                '  </div>' +
                '</div>' +
                '<div class="clearfix"></div>';

     // Hide the spinner.
    $('#chat-spinner').hide();

    chatOutput.append(value);

    chatOutput.scrollTop(chatOutput.height());
}

var addOnlineUser = function(userid, data) {
    var value = '<div id="user-' + userid + '">'+
                '  <div class="media" style="margin-top: 0;">' +
                '    <a class="pull-left" href="javascript::void();">' +
                '      <img class="img-responsive img-circle" style="height: 60px; width: 60px" alt="' + data.realname + '" src="' + data.picture + '">' +
                '    </a>' +
                '    <div class="media-body">' +
                '      <h4 class="media-heading"><strong>' + data.realname + '</strong></h4>' +
                '      <p class="no-margin">' + data.username + '</p>' +
                '      <p class="text-muted" style="margin-bottom: 5px;">' + data.roles + '</p>' +
                '    </div>' +
                '  </div>' +
                '  <div class="clearfix"></div>' +
                '  <hr style="margin-top: 0; margin-bottom: 10px;">' +
                '</div>';

    $('#chat-alone').hide();

    chatList.prepend(value);
}

function getTimestamp() {
  var totalSec = new Date().getTime() / 1000;

  var hours = parseInt(totalSec / 3600) % 24;
  var minutes = parseInt(totalSec / 60) % 60;
  var seconds = parseInt(totalSec % 60);

  var result = (hours < 10 ? '0' + hours : hours) + ':' +
    (minutes < 10 ? '0' + minutes : minutes) + ':' +
    (seconds < 10 ? '0' + seconds : seconds);

  return result;
}

//-------------------------------------------------------
// DataChannel Code
//-------------------------------------------------------

var channel = new DataChannel();

var chatUsers = {};

// https://github.com/muaz-khan/WebRTC-Experiment/tree/master/socketio-over-nodejs
var SIGNALING_SERVER = 'https://webrtcweb.com:9559/';

channel.openSignalingChannel = function(config) {
    var channel = config.channel || this.channel || 'default-namespace';

    var sender = Math.round(Math.random() * 9999999999) + 9999999999;

    io.connect(SIGNALING_SERVER).emit('new-channel', {
        channel: channel,
        sender : sender
    });

    console.log('Channels:', config.channel, this.channel, 'default-namespace');

    console.log('Using channel and sender:', channel, sender);

    var socket = io.connect(SIGNALING_SERVER + channel);

    socket.channel = channel;

    socket.on('connect', function() {
        if (config.callback) {
            config.callback(socket);
        }
    });

    socket.send = function(message) {
        socket.emit('message', {
            sender: sender,
            data  : message
        });
    };

    socket.on('message', config.onmessage);
};

channel.onopen = function (userid) {
    console.debug(userid, 'is connected with you.');

    setTimeout(function() {
        sendUserInfo(userid);

        // Enable the input.
        chatInput.attr("disabled", false);
        chatButton.attr("disabled", false);

        chatInput.focus();

    }, 5000);
};

// Error to open data ports.
channel.onerror = function(event) {
    console.warn('channel.onerror', event);
}

// Data ports suddenly dropped.
channel.onclose = function(event) {
    console.warn('channel.onerror', event);
}

channel.onmessage = function(message, userid, latency) {
    console.log('Latency:', latency, 'milliseconds');

    console.debug('Message from', userid, ':', message);

    var data = JSON.parse(message);

    if (data.type == 'message') {
        addChatMessage(data.message, data.realname, data.picture, 'left');
    }

    // Further, we will handle only the INFO messages.
    else if (data.type != 'info') {
        return;
    }

    if (! chatUsers[userid]) {
        chatUsers[userid] = data;

        addOnlineUser(userid, data);

        //
        var text = sprintf("<?= __d('chat', '%s (%s) joined the chat.'); ?>", data.realname, data.username);

        addLogMessage(text, 'success');
    }
};

channel.onleave = function(userid) {
    if (!! chatUsers[userid]) {
        var data = chatUsers[userid];

        var text = sprintf("<?= __d('chat', '%s (%s) left the chat.'); ?>", data.realname, data.username);

        addLogMessage(text, 'warning');

        // Update the Online Users.
        $('#user-' + userid).remove();

        // Finally, we will delete the record.
        delete chatUsers[userid];
    }

    if (chatUsers.length === 0) {
        $('#chat-alone').show();
    }
};

function sendUserInfo(userid) {
    var value = JSON.stringify({
        type:     'info',
        username: userInfo.username,
        realname: userInfo.realname,
        roles:    userInfo.roles,
        picture:  userInfo.picture,
    });

    console.debug('Sending User info to', userid);

    channel.channels[userid].send(value);
}

function sendMessage() {
    var message = chatInput.val();

    var value = JSON.stringify({
        type:     'message',
        username: userInfo.username,
        realname: userInfo.realname,
        picture:  userInfo.picture,
        message:  message
     });

    channel.send(value);

    //
    addChatMessage(message, userInfo.realname, userInfo.picture, 'right');

    chatInput.val('');
}

function checkPresence() {
    var socket = io.connect(SIGNALING_SERVER);

    socket.on('presence', function (isChannelPresent) {
        if (! isChannelPresent) {
            channel.open();
        } else {
            channel.openNewSession(false, true);
        }
    });

    socket.emit('presence', channel.channel);
}

// Search for existing data channels.
channel.connect();

checkPresence();

</script>
