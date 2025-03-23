var webSocketsServerPort = 8080;
var http = require('http');
var WebSocketServer = require('websocket').server;

var server = http.createServer(function(request, response) {
    // Not important for us. We're writing WebSocket server, not HTTP server
});
server.listen(webSocketsServerPort, function() {
    console.log("Server is listening on port " + webSocketsServerPort);
});

var clients = [];
var socket = new WebSocketServer({
  httpServer: server,
  autoAcceptConnections: false
});

socket.on('request', function(request) {
//  var connection = request.accept('any-protocol', request.origin);
  var connection = request.accept(null, request.origin);
  clients.push(connection);

  connection.on('message', function(message) {
    console.log(message);
    clients.forEach(function(client) {
      client.send(message.utf8Data);
    });
  });
});
