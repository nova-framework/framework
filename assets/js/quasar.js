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

    function Channel(socket, name, type) {
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

    global.Quasar = function (appKey, options) {
        this.appKey = appKey;

        this.channels = [];

        //
        var quasar = this,

         // Default settings.
        defaultSettings = {
            auth: {
                headers: {}
            },

            // Authorization endpoint.
            authEndpoint: '/broadcasting/auth',

            // HTTP server hostname.
            httpHost: '127.0.0.1',

            // SocketIO server port.
            socketPort: 2120,
        };

        var settings = extend({}, defaultSettings , options);

        // Connect to the SocketIO server from Quasar instance.
        var socketHost = settings['socketHost'];

        if (! socketHost) {
            socketHost = settings['httpHost'];
        }

        // Create a new SocketIO instance.
        var socket = quasar.socket = io.connect(socketHost + ':' + settings.socketPort + '/' + appKey);

        quasar.subscribe = function (channelName) {
            var type = 'public';

            if (matches = channelName.match(/^(private|presence)\-.+$/)) {
                type = matches[1];
            }

            socket.on('connect', function () {
                if (type === 'public') {
                    socket.emit('subscribe', channelName);

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

                        socket.emit('subscribe', channelName, data.auth, data.payload || '');
                    } else {
                        // We reached our auth endpoint, but it returned an error.
                    }
                }

                xhr.onerror = function () {
                    // There was a connection error of some sort.
                };

                // Create the POST content.
                var query = 'channel_name=' + encodeURIComponent(channelName) + '&socket_id=' + encodeURIComponent(socket.id);

                xhr.send(query);
            });

            var channel = new Channel(socket, channelName, type);

            quasar.channels[channelName] = channel;

            return channel;
        };

        quasar.unsubscribe = function (channelName) {
            socket.emit('unsubscribe', channel);

            var channel = quasar.channels[channelName];

            if (!! channel) {
                channel.stopListening();

                delete quasar.channels[channelName];
            }

            return quasar;
        };

        return quasar;
    };

})(window);
