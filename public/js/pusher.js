// Enable pusher logging - don't include this in production
Pusher.log = function(message) {
    if (window.console && window.console.log) {
        window.console.log(message);
    }
};

var pusher = new Pusher('e5ebcadcd1351179b7d7', {
    encrypted: true
});
var channel = pusher.subscribe('server.1');

function currentTime()
{
    var m = new Date();
    return ("0" + m.getUTCHours()).slice(-2) + ":" +
        ("0" + m.getUTCMinutes()).slice(-2) + ":" +
        ("0" + m.getUTCSeconds()).slice(-2);
}

channel.bind('App\\Events\\EmailServer\\EmailServerConnectionFailed', function(data) {
    $('#email-server-status').html(currentTime() + ' <span class="status-icon failed">&nbsp;</span> ' +
    '<span style="color: red; font-weight: bold">SendGrid connection failed</span>');
});

channel.bind('App\\Events\\EmailServer\\EmailWasNotSent', function(data) {
    $('#email-server-status').html(currentTime() + ' <span class="status-icon failed">&nbsp;</span> ' +
    '<span style="color: red; font-weight: bold">SendGrid is not delivering e-mails</span>');
});

channel.bind('App\\Events\\EmailServer\\EmailDeliveryWasSlow', function(data) {
    $('#email-server-status').html(currentTime() + ' <span class="status-icon failing">&nbsp;</span> SendGrid is taking ' +
    'longer than usual to deliver e-mails (' + data.time_taken + ' seconds)</span>');
});

channel.bind('App\\Events\\EmailServer\\EmailWasSent', function(data) {
    console.log(data.time_taken);
    $('#email-server-status').html(currentTime() + ' <span class="status-icon success">&nbsp;</span> SendGrid ' +
    'status normal (e-mails sending in ' + data.time_taken + ' seconds)');
});