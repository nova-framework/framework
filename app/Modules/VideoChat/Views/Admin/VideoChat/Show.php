<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div id="chat-video-panel">
    <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-warning"></i> <?= __d('video_chat', 'Warning'); ?></h4>
        <p><?= __d('video_chat', 'Avoid changing page as this will cut your current video chat session.'); ?></p>
    </div>
    <div class="row">
        <div class="col-md-8 col-sm-7">
            <div class="thumbnail video-chat-user">
                <div id="chat-remote-video"></div>
                <div class="caption">
                    <p class="text-muted text-center"><?= __d('video_chat', 'Chat with <b>{0}</b>', $chatUser->present()->name()); ?></p>
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
                    <h3 class="box-title"><?= __d('video_chat', 'Direct Chat'); ?></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- Conversations are loaded here -->
                    <div class="chat direct-chat-messages"></div>
                    <!--/.direct-chat-messages-->
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="input-group">
                        <input name="message" id="direct-chat-message" placeholder="<?= __d('video_chat', 'Type Message ...'); ?>" class="form-control" type="text">
                        <span class="input-group-btn">
                            <button id="direct-chat-button" type="button" class="btn btn-warning btn-flat"><?= __d('video_chat', 'Send'); ?></button>
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

                var displayMessage = function (message, type) {
                    var now = new Date(Date.now());

                    var time = now.getHours() + (now.getMinutes() < 10 ? '0' : '') + ":" + now.getMinutes() + ":" + (now.getSeconds() < 10 ? '0' : '') + now.getSeconds();

                    var html = '<div class="item">' +
                               '  <img src="' + message.picture + '" alt="user image" class="' + type + '">' +
                               '  <p class="message">' +
                               '    <a href="#" class="name">' +
                               '      <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> ' + time + '</small>' +
                               message.userName +
                               '    </a>' +
                               message.message +
                               '  </p>' +
                               '</div>';

                    $('.direct-chat-messages').append(html);

                    // Scroll to bottom to always display the last message.
                    var scrollHeight = $('.direct-chat-messages')[0] .scrollHeight;

                    //$(".direct-chat-messages").animate({ scrollTop: scrollHeight }, 1000);
                    $('.direct-chat-messages').scrollTop(scrollHeight);
                };

                var startVideoChat = function () {
                    var webRTC = new SimpleWebRTC({
                        localVideoEl: 'chat-local-video',
                        remoteVideosEl: '',
                        autoRequestMedia: true
                    });

                    webRTC.on('readyToCall', function () {
                        webRTC.joinRoom('<?= $roomName; ?>');
                    });

                    webRTC.on('videoAdded', function (video, peer) {
                        $(video).addClass('skip');

                        $('#chat-remote-video').html(video);

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

                    webRTC.on('videoRemoved', function (video, peer) {
                        video.src = '';
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
                        else if (data.type == 'message') {
                            displayMessage(data.payload, 'online');

                            console.log('Received message: ' + data.payload + ' from ' + peer.id);
                        }
                    });

                    // The Direct Chat.
                    $('#direct-chat-button').on('click', function() {
                        var text = $('#direct-chat-message') .val();

                        if (text === '') return;

                        $('#direct-chat-message').val('');

                        // Prepare the message object.
                        var message = {
                            picture: '<?= $authUser->present()->picture(); ?>',
                            userName: '<?= $authUser->present()->name(); ?>',
                            message: text
                        };

                        // Send the message via Data Channel.
                        webRTC.sendDirectlyToAll('simplewebrtc', 'message', message);

                        // Show the message locally.
                        displayMessage(message, 'offline');
                    });
                };

                if (! isCompatible) {
                    //notifyNotSupport();

                    $('#chat-video-panel').remove();

                    return;
                }

                //$('#messages').remove();

                startVideoChat();

                window.onbeforeunload = function () {
                    return "<?= __d('video_chat', 'Avoid changing page as this will cut your current video chat session.'); ?>";
                };
            }
        };

        $(document).on('ready', function () {
            $('#direct-chat-message').keypress(function(event) {
                // Send the text message on pressing Ctrl+Enter.
                if(event.ctrlKey && (event.keyCode == '13')) {
                    $('#direct-chat-button').click();
                }
            });

            // Init the VideoChat instance.
            VideoChat.init();
        });
    })();
</script>

</section>
