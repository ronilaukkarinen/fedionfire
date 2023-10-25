<?php
// Require composer
require __DIR__ . '/vendor/autoload.php';

// Set up phpdotenv
$dotenv = \Dotenv\Dotenv::createImmutable( __DIR__ );
$dotenv->load();
?>
<!doctype html>
<html>

<head>
  <title>Firemasto</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta property="og:title" content="Firemasto" />
  <meta property="og:description" content="Watch every Mastodon post in real-time - filter the firehose" />

  <style type="text/css">
  html {
    line-height: 1.5;´
    -webkit-text-size-adjust: 100%;
    -moz-tab-size: 4;
    -o-tab-size: 4;
      tab-size: 4;
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    font-feature-settings: normal;
    font-variation-settings: normal;
  }
  </style>
</head>
<body>

<div id="firemasto-live-feed"></div>

<script>
  // DomContentLoaded
  document.addEventListener('DOMContentLoaded', function() {

    // Define instance URLs
    var instanceUrls = [
      'https://mastodon.social',
      'https://pb.todon.de',
      'https://kopimi.space',
      'https://sunnygarden.org',
      'https://mementomori.social'
    ];

    // liveStreamingService.js for /api/v1/streaming/public/remote
    var liveStreamingService = function() {
      var instanceUrl = instanceUrls[Math.floor(Math.random() * instanceUrls.length)];
      var url = instanceUrl + '/api/v1/streaming/public/remote';
      var xhr = new XMLHttpRequest();
      xhr.open('GET', url, true);
      xhr.responseType = 'blob';
      xhr.onload = function() {
        if (this.status === 200) {
          var blob = this.response;
          var reader = new FileReader();
          reader.readAsText(blob);
          reader.onload = function(e) {
            var text = reader.result;
            var json = JSON.parse(text);
            var data = json.payload;
            var html = '<div class="firemasto-live-feed-item">';
            html += '<div class="firemasto-live-feed-item__header">';
            html += '<div class="firemasto-live-feed-item__header__avatar">';
            html += '<img src="' + data.account.avatar + '" />';
            html += '</div>';
            html += '<div class="firemasto-live-feed-item__header__account">';
            html += '<div class="firemasto-live-feed-item__header__account__display-name">';
            html += data.account.display_name;
            html += '</div>';
            html += '<div class="firemasto-live-feed-item__header__account__username">';
            html += '@' + data.account.username;
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '<div class="firemasto-live-feed-item__content">';
            html += data.content;
            html += '</div>';
            html += '</div>';
            document.getElementById('firemasto-live-feed').innerHTML = html + document.getElementById('firemasto-live-feed').innerHTML;
          };
        }
      };
      xhr.send();
    };

    // poll() function
    var poll = function() {
      liveStreamingService();
      setTimeout(poll, 1000);
    };

    // Start polling
    poll();

  });
</script>

</body>
</html>
