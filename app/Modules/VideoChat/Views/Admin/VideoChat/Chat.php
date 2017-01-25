<section class="content-header" style="margin: 0 15px; padding-bottom: 15px; border-bottom: 1px solid #FFF;">
    <h1><?= __d('video_chat', 'Chat'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('video_chat', 'Dashboard'); ?></a></li>
        <li><?= __d('messages', 'Chat'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div id="chat-video-panel">
    <div class="row">
        <div class="col-md-9 col-sm-8">
            <!-- Direct Chat -->
            <div class="box box-default direct-chat direct-chat-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __d('video_chat', 'Public Chat'); ?></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- Conversations are loaded here -->
                    <div class="chat direct-chat-messages" id="chat-box" style="min-height: 500px;"></div>
                    <!--/.direct-chat-messages-->
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="input-group">
                        <input name="message" id="direct-chat-message" disabled="disabled" placeholder="<?= __d('video_chat', 'Type Message ...'); ?>" class="form-control" type="text">
                        <span class="input-group-btn">
                            <button id="direct-chat-button" disabled="disabled" type="button" class="btn btn-warning btn-flat"><?= __d('video_chat', 'Send'); ?></button>
                        </span>
                    </div>
                </div>
                <!-- /.box-footer-->
            </div>
        </div>
        <div class="col-md-3 col-sm-4">
            <!-- Direct Chat -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __d('video_chat', 'On-line Users'); ?></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- On-line Users are loaded here -->
                    <div class="direct-chat-users" id="chat-users" style="min-height: 500px;"></div>
                    <!--/.direct-chat-users-->
                </div>
                <!-- /.box-body -->
            </div>
            <div id="connection-status"></div>
        </div>
    </div>
</div>

<script>
    (function () {
        var VideoChat = {
            init: function () {
                var userInfo = {
                    userid:   '<?= $authUser->id; ?>',
                    username: '<?= $authUser->username; ?>',
                    realname: '<?= $authUser->present()->name(); ?>',
                    picture:  '<?= $authUser->present()->picture(); ?>',
                    role:     '<?= $authUser->role->name; ?>'
                };

                var isCompatible = !!Modernizr.prefixed('RTCPeerConnection', window);

                var startVideoChat = function () {
                    var webRTC = new SimpleWebRTC({
                        // The Signaling Server used by SimpleWebRTC.
                        url: '<?= $url; ?>',

                        // We don't do video.
                        localVideoEl: '',
                        remoteVideosEl: '',
                        // Don't ask for camera access.
                        autoRequestMedia: false,
                        // Don't negotiate media.
                        receiveMedia: {
                            mandatory: {
                                OfferToReceiveAudio: false,
                                OfferToReceiveVideo: false
                            }
                        },

                        // We pass the User information via nick.
                        nick: userInfo
                    });

                    webRTC.on('connectionReady', function() {
                        webRTC.joinRoom('<?= $roomName; ?>');
                    });

                    // Called when a peer has joined the room.
                    webRTC.on('createdPeer', function(peer) {
                        peer.send('presence', webRTC.config.nick);

                        /*
                        window.setTimeout(function () {
                            peer.sendDirectly('simplewebrtc', 'presence', { status: 'joined' });
                        }, 1000);
                        */

                        // Enable the Direct Chat input.
                        $('#direct-chat-message').removeAttr('disabled');
                        $('#direct-chat-button').removeAttr('disabled');

                        // Show the ice connection state
                        if (peer && peer.pc) {
                            peer.pc.on('iceConnectionStateChange', function () {
                                var alertDiv = $('<div>')
                                        .addClass('alert');

                                switch (peer.pc.iceConnectionState) {
                                    case 'checking':
                                        alertDiv
                                            .addClass('alert-info')
                                            .html('<em class="fa fa-spinner fa-spin"></em> ' + "<?= __d('video_chat', 'Connecting to peer'); ?>");
                                        break;
                                    case 'connected':
                                        //no break
                                    case 'completed':
                                        alertDiv
                                            .addClass('alert-success')
                                            .html('<em class="fa fa-commenting"></em> ' + "<?= __d('video_chat', 'Connection established'); ?>");
                                        break;
                                    case 'disconnected':
                                        alertDiv
                                            .addClass('alert-info')
                                            .html('<em class="fa fa-frown-o"></em> ' + "<?= __d('video_chat', 'Disconnected'); ?>");
                                        break;
                                    case 'failed':
                                        alertDiv
                                            .addClass('alert-danger')
                                            .html('<em class="fa fa-times"></em> ' + "<?= __d('video_chat', 'Connection failed'); ?>");
                                        break;
                                    case 'closed':
                                        alertDiv
                                            .addClass('alert-danger')
                                            .html('<em class="fa fa-close"></em> ' + "<?= __d('video_chat', 'Connection closed'); ?>");
                                        break;
                                }

                                $('#connection-status').html(alertDiv);
                            });
                        }
                    });

                    webRTC.on('iceFailed', function (peer) {
                        var alertDiv = $('<div>')
                            .addClass('alert-danger')
                            .html('<em class="fa fa-close"></em> ' + "<?= __d('video_chat', 'Local connection failed'); ?>");

                        $('#connection-status').html(alertDiv);
                    });

                    webRTC.on('connectivityError', function (peer) {
                        var alertDiv = $('<div>')
                            .addClass('alert-danger')
                            .html('<em class="fa fa-close"></em> ' + "<?= __d('video_chat', 'Remote connection failed'); ?>");

                        $('#connection-status').html(alertDiv);
                    });

                    webRTC.on('channelMessage', function (peer, label, data) {
                        // Only handle messages from your dataChannel
                        if (label !== 'simplewebrtc') return;
                        else if (data.type === 'message') {
                            displayChatMessage(peer.nick, data.payload, 'online');
                        } else if (data.type === 'presence') {
                            console.log('presence', data.payload, peer.id, peer.nick);
                        }
                    });

                    webRTC.connection.on('message', function(message) {
                        if (message .type === 'presence') {
                            console.log('presence', message);
                        }
                    });

                    // The Direct Chat.
                    $('#direct-chat-button').on('click', function () {
                        var message = $('#direct-chat-message') .val();

                        if (message === '') return;

                        $('#direct-chat-message').val('');

                        // Process the EMOJI on message.
                        message = emojione.toShort(message);

                        // Send the message directly via default Data Channel.
                        webRTC.sendDirectlyToAll('simplewebrtc', 'message', message);

                        // Show the message locally.
                        displayChatMessage(webRTC.config.nick, message, 'offline');
                    });
                };

                var displayChatMessage = function (userinfo, message, type) {
                    var now = new Date(Date.now());

                    var receivedAt = now.getHours() +
                        ":" + ((now.getMinutes() < 10) ? '0' : '') + now.getMinutes() +
                        ":" + ((now.getSeconds() < 10) ? '0' : '') + now.getSeconds();

                    var html = '<div class="item">' +
                               '  <img src="' + userinfo.picture + '" alt="user image" class="' + type + '">' +
                               '  <p class="message">' +
                               '    <a href="javascript::void();" class="name">' +
                               '      <small class="text-muted pull-right" style="padding-right: 5px;"><i class="fa fa-clock-o"></i> ' + receivedAt + '</small>' +
                               userinfo.realname +
                               '    </a>' +
                               emojione.toImage(message) +
                               '  </p>' +
                               '</div>';

                    if ($.trim( $('#chat-box').html() ).length !== 0) {
                        html = '<hr style="margin: 0 5px 10px 5px;">' + html;
                    }

                    // Append the message's HTML to Chat messages.
                    $('.direct-chat-messages').append(html);

                    // Scroll to bottom, to always display the last message.
                    var scrollTo = $('#chat-box').prop('scrollHeight') + 'px';

                    $('#chat-box').slimScroll({
                        scrollTo: scrollTo,
                        railVisible: true,
                        alwaysVisible: true
                    });
                };

                if (! isCompatible) {
                    //notifyNotSupport();

                    $('#chat-video-panel').remove();

                    return;
                }

                //$('#messages').remove();

                startVideoChat();

                $(window).on('beforeunload', function (event) {
                    var message = "<?= __d('video_chat', 'Avoid changing page as this will cut your current video chat session.'); ?>";

                    event.returnValue = message; // Gecko, Trident, Chrome 34+

                    return message;              // Gecko, WebKit, Chrome <34
                });
            }
        };

        $(document).on('ready', function () {
            emojione.ascii = true;

            // Send the text input on Direct Chat, when is pressed Ctrl+Enter.
            $('#direct-chat-message').keypress( function (event) {
                var keyCode = (event.keyCode ? event.keyCode : event.which);

                if (event.shiftKey && (keyCode == 13)) {
                    event.preventDefault();

                    // Send the text message on pressing Ctrl+Enter.
                    $('#direct-chat-button').click();
                }
            });

            // Setup the SLIMSCROLL for the Direct Chat widget.
            $('#chat-box').slimScroll({
                height: '500px'
                //start: 'bottom',
                //railVisible: true,
                //alwaysVisible: true
            });

            // Init the VideoChat.
            VideoChat.init();
        });
    })();
</script>

</section>
