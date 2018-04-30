(function (global, undefined) {

    function extend() {
        var result = arguments[0];

        for (var i = 1; i < arguments.length; i++) {
            var argument = arguments[i];

            if (! argument) {
                continue;
            }

            for (var key in argument) {
                if (argument.hasOwnProperty(key)) {
                    result[key] = argument[key];
                }
            }
        }

        return result;
    };

    //--------------------------------------------------------------------------
    // Channels
    //--------------------------------------------------------------------------

    function Channel(subscription, name) {
        this.type = 'public';

        this.name         = name;
        this.subscription = subscription;

        //
        var channel = this;

        channel.listen = function (event, callback) {
            channel.subscription.bind(event, callback);

            return channel;
        };

        channel.stopListening = function (event, callback) {
            channel.subscription.unbind(event, callback);
        };

        channel.notification = function (callback) {
            var eventName = 'Nova.Notification.Events.BroadcastNotificationCreated';

            channel.subscription.bind(eventName, callback);

            return channel;
        };

        channel.listenForWhispering = function (event, data) {
            var eventName = 'client-' + event;

            channel.subscription.bind(eventName, callback);

            return channel;
        };
    };

    function PrivateChannel(subscription, name) {
        // Handle the class inheritance.
        this.prototype = Object.create(Channel.prototype);

        Channel.call(this, subscription, name);

        //
        var channel = this;

        channel.type = 'private';

        channel.whisper = function (event, data) {
            var eventName = 'client-' + event;

            channel.subscription.trigger(eventName, data);

            return channel;
        };
    };

    function PresenceChannel(subscription, name, prefix) {
        // Handle the class inheritance.
        this.prototype = Object.create(PrivateChannel.prototype);

        PrivateChannel.call(this, subscription, name);

        //
        var channel = this;

        channel.type = 'presence';

        // The channel mode could be 'quasar' or 'pusher'
        channel.prefix = prefix;

        channel.here = function (callback) {
            var event = channel.prefix + ':subscription_succeeded';

            channel.subscription.bind(event, function (data) {
                var members = Object.keys(data.members).map(key => data.members[key]);

                callback(members);
            });

            return channel;
        };

        channel.joining = function (callback) {
            var event = channel.prefix + ':member_added';

            channel.subscription.bind(event, function (member) {
                callback(member.info);
            });

            return channel;
        };

        channel.leaving = function (callback) {
            var event = channel.prefix + ':member_removed';

            channel.subscription.bind(event, function (member) {
                callback(member.info);
            });

            return channel;
        };
    };

    //--------------------------------------------------------------------------
    // Quasar Channel/Subscription
    //--------------------------------------------------------------------------

    function QuasarChannel(socket, name, type) {
        this.socket = socket;
        this.name   = name;
        this.type   = type; // public, private or presence

        //
        var channel = this;

        channel.bind = function () {
            var parameters = channel.prepareParameters(arguments);

            socket.on.apply(socket, parameters);

            return channel;
        };

        channel.unbind = function () {
            if (arguments.length > 0) {
                var parameters = channel.prepareParameters(arguments);

                socket.off.apply(socket, parameters);
            } else {
                var prefix = channel.name + '#';

                for (var event in socket._callbacks) {
                    if (event.startsWith(prefix)) {
                        socket.off(event);
                    }
                }
            }

            return channel;
        };

        channel.trigger = function () {
            var parameters = Array.from(arguments);

            parameters.unshift('channel:event', channel.name);

            socket.emit.apply(socket, parameters);

            return channel;
        };

        channel.prepareParameters = function (args) {
            var parameters = Array.from(args);

            // First argument is always the event name.
            var event = parameters[0];

            parameters[0] = channel.name + '#' + event;

            return parameters;
        };
    };

    //--------------------------------------------------------------------------
    // Drivers
    //--------------------------------------------------------------------------

    function Driver(broadcaster, appKey) {
        this.name = 'default';

        //
        var driver = this;

        driver.broadcaster = broadcaster;

        driver.appKey = appKey;

        driver.createChannel = function(subscription, channel, prefix, type) {
            if (typeof type === 'undefined') {
                type = driver.channelType(channel);
            }

            if (type === 'public') {
                return new Channel(subscription, channel);
            } else if (type === 'private') {
                return new PrivateChannel(subscription, channel);
            } else if (type === 'presence') {
                return new PresenceChannel(subscription, channel, prefix);
            }
        };

        driver.channelType = function (channel) {
            if (matches = channel.match(/^(private|presence)\-.+$/)) {
                return matches[1];
            }

            return 'public';
        };
    }

    function PusherDriver(broadcaster, appKey, options) {
        // Handle the class inheritance.
        this.prototype = Object.create(Driver.prototype);

        Driver.call(this, broadcaster, appKey);

        //
        var driver = this;

        driver.name = 'pusher';

        // Create a new Pusher instance.
        var pusher = driver.pusher = new Pusher(appKey, options);

        driver.subscribe = function (channel) {
            var subscription = pusher.subscribe(channel);

            return driver.createChannel(subscription, channel, 'pusher');
        };

        driver.unsubscribe = function (channel) {
            pusher.unsubscribe(channel);

            return driver;
        };
    };

    function QuasarDriver(broadcaster, appKey, options) {
        // Handle the class inheritance.
        this.prototype = Object.create(Driver.prototype);

        Driver.call(this, broadcaster, appKey);

        //
        var driver = this,

        // Additional default settings for the Quasar driver.
        defaultSettings = {
            httpHost: '127.0.0.1',

            socketPort: 2120,
        };

        driver.name = 'quasar';

        // Override default settings with supplied options.
        var settings = driver.settings = extend({}, defaultSettings, options);

        // Connect to the SocketIO server from Quasar instance.
        var socketHost = settings['socketHost'];

        if (! socketHost) {
            socketHost = settings['httpHost'];
        }

        // Create a new SocketIO instance.
        var socket = driver.socket = io.connect(socketHost + ':' + settings.socketPort + '/' + appKey);

        driver.subscribe = function (channel) {
            var type = driver.channelType(channel);

            socket.on('connect', function () {
                if (type === 'public') {
                    socket.emit('subscribe', channel);

                    return;
                }

                var headers = extend({}, settings.auth.headers, {
                    'Content-type': 'application/x-www-form-urlencoded',
                    'X-Socket-ID':  socket.id
                });

                var xhr = new XMLHttpRequest();

                xhr.open('POST', settings.authEndpoint);

                for(var header in headers) {
                    if (headers.hasOwnProperty(header)) {
                        xhr.setRequestHeader(header, headers[header]);
                    }
                }

                xhr.responseType = 'json';

                xhr.onload = function () {
                    var status = xhr.status;

                    if ((status >= 200) && (status < 400)) {
                        var data = xhr.response;

                        socket.emit('subscribe', channel, data.auth, data.payload || '');
                    } else {
                        // We reached our auth endpoint, but it returned an error.
                    }
                }

                xhr.onerror = function () {
                    // There was a connection error of some sort.
                };

                // Create the POST content.
                var query = 'channel_name=' + encodeURIComponent(channel) + '&socket_id=' + encodeURIComponent(socket.id);

                xhr.send(query);
            });

            var subscription = new QuasarChannel(socket, channel, type);

            return driver.createChannel(subscription, channel, 'quasar', type);
        };

        driver.unsubscribe = function (channel) {
            socket.emit('unsubscribe', channel);

            return driver;
        };
    };

    //--------------------------------------------------------------------------
    // Broadcaster
    //--------------------------------------------------------------------------

    global.Broadcaster = function (appKey, options) {
        this.appKey = appKey;

        this.channels = [];

        //
        var broadcaster = this,

         // Default settings.
        defaultSettings = {
            auth: {
                headers: {}
            },

            // Authorization endpoint.
            authEndpoint: '/broadcasting/auth'
        };

        var settings = extend({}, defaultSettings , options);

        if (settings.broadcaster === 'pusher') {
            var driver = new PusherDriver(broadcaster, appKey, settings);
        } else {
            var driver = new QuasarDriver(broadcaster, appKey, settings);
        }

        broadcaster.driver = driver;

        broadcaster.join = function (channel) {
            var channelName = 'presence-' + channel;

            return broadcaster.subscribe(channelName);
        };

        broadcaster.private = function (channel) {
            var channelName = 'private-' + channel;

            return broadcaster.subscribe(channelName);
        };

        broadcaster.subscribe = function (channelName) {
            var channel = driver.subscribe(channelName);

            broadcaster.channels[channelName] = channel;

            return channel;
        };

        broadcaster.unsubscribe = function (channelName) {
            driver.unsubscribe(channelName);

            var channel = broadcaster.channels[channelName];

            if (!! channel) {
                channel.stopListening();

                delete broadcaster.channels[channelName];
            }

            return broadcaster;
        };

        return broadcaster;
    };

})(window);
