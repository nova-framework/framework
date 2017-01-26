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
        <div class="col-md-8 col-sm-8">
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
        <div class="col-md-4 col-sm-4">
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
        var PrivateChat = {

            init: function (userId) {
                //var oPChat = $('.priv_dock_wrap .priv_chat_tab#pcid' + id);
            }
        };

        var Chat = {
            // Here we store the SimpleWebRTC object.
            webRTC: null,

            init: function () {
                var userInfo = {
                    userid:   '<?= $authUser->id; ?>',
                    username: '<?= $authUser->username; ?>',
                    realname: '<?= $authUser->present()->name(); ?>',
                    location: '<?= $authUser->location; ?>',
                    picture:  '<?= $authUser->present()->picture(); ?>',
                    role:     '<?= $authUser->role->name; ?>'
                };

                var isCompatible = !!Modernizr.prefixed('RTCPeerConnection', window);

                var startVideoChat = function () {
                    webRTC = new SimpleWebRTC({
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
                        window.setTimeout(function () {
                            peer.sendDirectly('simplewebrtc', 'presence', { status: 'joined' });
                        }, 1000);

                        // Enable the Direct Chat input.
                        $('#direct-chat-message').removeAttr('disabled');
                        $('#direct-chat-button').removeAttr('disabled');
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

                            handleAnnouncedPeer(peer);
                        }
                    });
                };

                // The Direct Chat.
                $('#direct-chat-button').on('click', function () {
                    var message = $('#direct-chat-message') .val();

                    if (message === '') return;

                    $('#direct-chat-message').val('');

                    // Process the EMOJI on message.
                    message = emojione.toShort(message);

                    // Send the message directly via default Data Channel.
                    if (webRTC) {
                        webRTC.sendDirectlyToAll('simplewebrtc', 'message', message);
                    }

                    // Show the message locally.
                    displayChatMessage(userInfo, message, 'offline');
                });

                var handleAnnouncedPeer = function (peer) {
                    var info = peer.nick;

                    if ($("#chat-user-" + info.userid).length !== 0) {
                        setupChatPeerStatus(info.userid, peer, true);

                        return;
                    }

                    var html =
                        '<div id="chat-user-' + info.userid + '" class="media" style="margin-top: 0;">' +
                        '  <div class="pull-left">' +
                        '    <img class="img-responsive img-circle" style="height: 65px; width: 65px" alt="' + info.realname + '" src="' + info.picture + '">' +
                        '  </div>' +
                        '  <div class="media-body">' +
                        '    <h4 class="media-heading pull-left"><a href="#" id="chat-user-link-' + info.userid + '" data-active="1" data-userid="' + info.userid + '" data-id="' + peer.id + '" data-sid="' + peer.sid + '" disabled="disabled"><strong>' + info.username + '</strong></a></h4><div id="chat-peer-status-' + info.userid + '" class="pull-right"></div>' +
                        '    <div class="clearfix"></div>' +
                        '    <p class="no-margin">' + info.role + '</p>' +
                        '    <p class="no-margin text-muted"><small>' + info.realname + '</small></p>' +
                        '  </div>' +
                        '</div>';

                    if ($('#chat-users').html().length !== 0) {
                        html = '<hr style="margin: 0 5px 10px 5px;">' + html;
                    }

                    // Append the message's HTML to Chat messages.
                    $('.direct-chat-users').append(html);

                    $('#chat-user-link-' + info.userid).on('click', function (event) {
                        var active = $(this).data('active');

                        if (active == 1) {
                            var userid = $(this).data('userid');

                            var id  = $(this).data('id');
                            var sid = $(this).data('sid');

                            handlePrivateChat(userid, id, sid);
                        }
                    });

                    setupChatPeerStatus(info.userid, peer, false);
                };

                var handlePrivateChat = function (userid, id, sid) {
                    alert('"' + userid + '"' + ' ' + '"' + '"' + id + '"' + ' ' + '"' + sid + '"');
                }

                var setupChatPeerStatus = function (userid, peer, withLinkData) {
                    if (withLinkData) {
                        $('#chat-user-link-' + userid).data('userid', userid);
                        $('#chat-user-link-' + userid).data('active', 1);

                        $('#chat-user-link-' + userid).data('id', peer.id);
                        $('#chat-user-link-' + userid).data('sid', peer.sid);
                    }

                    if (peer.pc) {
                        handleChatPeerStatus(userid, peer.pc.iceConnectionState);

                        peer.pc.on('iceConnectionStateChange', function () {
                            handleChatPeerStatus(userid, peer.pc.iceConnectionState);
                        });
                    }
                };

                var handleChatPeerStatus = function (userid, status) {
                    var active = 0;

                    var labelSpan = $('<span>').addClass('label');

                    switch (status) {
                        case 'checking':
                            labelSpan.addClass('label-info').html("<?= __d('video_chat', 'Connecting to peer'); ?>");

                            break;
                        case 'connected':
                            //no break
                        case 'completed':
                            active = 1;

                            labelSpan.addClass('label-success').html("<?= __d('video_chat', 'Connection established'); ?>");

                            break;
                        case 'disconnected':
                            labelSpan.addClass('label-info').html("<?= __d('video_chat', 'Disconnected'); ?>");

                            break;
                        case 'failed':
                            labelSpan.addClass('label-danger').html("<?= __d('video_chat', 'Connection failed'); ?>");

                            break;
                        case 'closed':
                            labelSpan.addClass('label-danger').html("<?= __d('video_chat', 'Connection closed'); ?>");

                            break;
                    }

                    $('#chat-peer-status-' + userid).html(labelSpan);

                    if (active === 1) {
                        $('#chat-user-link-' + userid).removeAttr('disabled');
                    } else {
                        $('#chat-user-link-' + userid).attr('disabled', 'disabled');
                    }

                    $('#chat-user-link-' + userid).data('active', active);
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
                    $('#chat-video-panel').remove();

                    return;
                }

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

            // Send the text input on Direct Chat, when is pressed Shift+Enter.
            $('#direct-chat-message').keypress( function (event) {
                var keyCode = (event.keyCode ? event.keyCode : event.which);

                if (event.shiftKey && (keyCode == 13)) {
                    event.preventDefault();

                    // Send the text message on pressing Shift+Enter.
                    $('#direct-chat-button').click();
                }
            });

            // Initialize the SLIMSCROLL.
            $('#chat-box').slimScroll({
                height: '500px'
            });

            $('#chat-users').slimScroll({
                height: '500px'
            });

            // Init the VideoChat.
            Chat.init();
        });
    })();
</script>

</section>
