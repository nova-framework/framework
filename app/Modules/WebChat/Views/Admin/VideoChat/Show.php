<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div id="chat-video-panel">
    <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-warning"></i> <?= __d('web_chat', 'Warning'); ?></h4>
        <p><?= __d('web_chat', 'Avoid changing page as this will cut your current video chat session.'); ?></p>
    </div>
    <div class="row">
        <div class="col-md-8 col-sm-7">
            <div class="thumbnail video-chat-user">
                <div id="chat-remote-video"></div>
                <div class="caption">
                    <p class="text-muted text-center"><?= __d('web_chat', 'Chat with <b>{0}</b>', $chatUser->present()->name()); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-5">
            <div class="thumbnail">
                <div id="chat-local-video"></div>
                <div class="caption">
                    <p class="text-muted text-center"><?= $authUser->present()->name(); ?></p>
                </div>
            </div>
            <!-- Direct Chat -->
            <div class="box box-warning direct-chat direct-chat-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __d('web_chat', 'Direct Chat'); ?></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- Conversations are loaded here -->
                    <div class="chat direct-chat-messages" id="chat-box"></div>
                    <!--/.direct-chat-messages-->
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="input-group">
                        <input name="message" id="direct-chat-message" disabled="disabled" placeholder="<?= __d('web_chat', 'Type Message ...'); ?>" class="form-control" type="text">
                        <span class="input-group-btn">
                            <button id="direct-chat-button" disabled="disabled" type="button" class="btn btn-warning btn-flat"><?= __d('web_chat', 'Send'); ?></button>
                        </span>
                    </div>
                </div>
                <!-- /.box-footer-->
            </div>
            <div id="connection-status"></div>
        </div>
    </div>
</div>

<script>
    (function () {
        var VideoChat = {
            init: function () {
                var isCompatible = !!Modernizr.prefixed('RTCPeerConnection', window);

                var notifyNotSupport = function () {
                    $.get("<?= site_url('chat/ajax'); ?>", {
                        action: 'notify_not_support',
                        to: <?= $chatUser->id; ?>
                    });
                };

                var startVideoChat = function () {
                    var webRTC = new SimpleWebRTC({
                        // The Signaling Server used by SimpleWebRTC.
                        url: "<?= $url; ?>",

                        // The local and remote Media configuration.
                        localVideoEl: 'chat-local-video',
                        remoteVideosEl: '',
                        autoRequestMedia: true,
                        nick: {
                            userid: '<?= $authUser->id; ?>',
                            username: '<?= $authUser->username; ?>',
                            realname: '<?= $authUser->present()->name(); ?>',
                            picture: '<?= $authUser->present()->picture(); ?>',
                            role: '<?= $authUser->role->name; ?>',
                        }
                    });

                    webRTC.on('readyToCall', function () {
                        webRTC.joinRoom('<?= $roomName; ?>');
                    });

                    webRTC.on('videoAdded', function (video, peer) {
                        $(video).addClass('skip');

                        $('#chat-remote-video').html(video);

                        // Enable the Direct Chat input.
                        $('#direct-chat-message').removeAttr('disabled');
                        $('#direct-chat-button').removeAttr('disabled');

                        if (peer && peer.pc) {
                            peer.pc.on('iceConnectionStateChange', function () {
                                var alertDiv = $('<div>')
                                        .addClass('alert');

                                switch (peer.pc.iceConnectionState) {
                                    case 'checking':
                                        alertDiv
                                            .addClass('alert-info')
                                            .html('<em class="fa fa-spinner fa-spin"></em> ' + "<?= __d('web_chat', 'Connecting to peer'); ?>");
                                        break;
                                    case 'connected':
                                        //no break
                                    case 'completed':
                                        alertDiv
                                            .addClass('alert-success')
                                            .html('<em class="fa fa-commenting"></em> ' + "<?= __d('web_chat', 'Connection established'); ?>");
                                        break;
                                    case 'disconnected':
                                        alertDiv
                                            .addClass('alert-info')
                                            .html('<em class="fa fa-frown-o"></em> ' + "<?= __d('web_chat', 'Disconnected'); ?>");
                                        break;
                                    case 'failed':
                                        alertDiv
                                            .addClass('alert-danger')
                                            .html('<em class="fa fa-times"></em> ' + "<?= __d('web_chat', 'Connection failed'); ?>");
                                        break;
                                    case 'closed':
                                        alertDiv
                                            .addClass('alert-danger')
                                            .html('<em class="fa fa-close"></em> ' + "<?= __d('web_chat', 'Connection closed'); ?>");
                                        break;
                                }

                                $('#connection-status').html(alertDiv);
                            });
                        }
                    });

                    webRTC.on('videoRemoved', function (video, peer) {
                        video.src = '';

                        // Disable the Direct Chat input.
                        $('#direct-chat-message').attr('disabled', 'disabled');
                        $('#direct-chat-button').attr('disabled', 'disabled');
                    });

                    webRTC.on('iceFailed', function (peer) {
                        var alertDiv = $('<div>')
                            .addClass('alert-danger')
                            .html('<em class="fa fa-close"></em> ' + "<?= __d('web_chat', 'Local connection failed'); ?>");

                        $('#connection-status').html(alertDiv);
                    });

                    webRTC.on('connectivityError', function (peer) {
                        var alertDiv = $('<div>')
                            .addClass('alert-danger')
                            .html('<em class="fa fa-close"></em> ' + "<?= __d('web_chat', 'Remote connection failed'); ?>");

                        $('#connection-status').html(alertDiv);
                    });

                    // Called when a peer has joined the room.
                    webRTC.on('createdPeer', function(peer) {
                        // logic to add audio into a grouping element.
                        console.log(peer);
                    });

                    webRTC.on('message', function(message) {
                        //console.log(message);
                    });

                    webRTC.on('channelMessage', function (peer, label, data) {
                        // Only handle messages from your dataChannel
                        if (label !== 'simplewebrtc') return;
                        else if (data.type === 'chatMessage') {
                            displayChatMessage(data.payload, 'online');
                        }
                    });

                    // The Direct Chat.
                    $('#direct-chat-button').on('click', function () {
                        var input = $('#direct-chat-message') .val();

                        if (input === '') return;

                        $('#direct-chat-message').val('');

                        // Prepare the message object.
                        var message = {
                            picture: '<?= $authUser->present()->picture(); ?>',
                            userName: '<?= $authUser->present()->name(); ?>',
                            message: emojione.toShort(input)
                        };

                        // Send the message directly via default Data Channel.
                        webRTC.sendDirectlyToAll('simplewebrtc', 'chatMessage', message);

                        // Show the message locally.
                        displayChatMessage(message, 'offline');
                    });
                };

                var displayChatMessage = function (message, type) {
                    var now = new Date(Date.now());

                    var receivedAt = now.getHours() +
                        ":" + ((now.getMinutes() < 10) ? '0' : '') + now.getMinutes() +
                        ":" + ((now.getSeconds() < 10) ? '0' : '') + now.getSeconds();

                    var html = '<div class="item">' +
                               '  <img src="' + message.picture + '" alt="user image" class="' + type + '">' +
                               '  <p class="message">' +
                               '    <a href="javascript::void();" class="name">' +
                               '      <small class="text-muted pull-right" style="padding-right: 5px;"><i class="fa fa-clock-o"></i> ' + receivedAt + '</small>' +
                               message.userName +
                               '    </a>' +
                               emojione.toImage(message.message) +
                               '  </p>' +
                               '</div>';

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
                    var message = "<?= __d('web_chat', 'Avoid changing page as this will cut your current video chat session.'); ?>";

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

            // Setup the SLIMSCROLL for the Direct Chat widget.
            $('#chat-box').slimScroll({
                height: '250px'
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
