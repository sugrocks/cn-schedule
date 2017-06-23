<!DOCTYPE html>
<html lang="en" manifest="site.appcache">
<head>
    <!-- Cool, basic stuff -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Quick access to the Cartoon Network schedule.">
    <meta name="author" content="/sug/.rocks">
    <meta name="robots" content="nofollow">

    <title>CN Schedule - Grid View</title>
    <link rel="stylesheet" href="css/grid.css?v0.0.1">

    <!-- Twitter hell -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@sugrocks">
    <meta name="twitter:title" content="CN Schedule - Grid View">
    <meta name="twitter:description" content="Quick access to the Cartoon Network schedule.">
    <meta name="twitter:image" content="https://cn.sug.rocks/img/CN-Pink-Round.png">
    <meta name="twitter:image:alt" content="CN Schedule">

    <!-- Open Graph hell -->
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="CN Schedule">
    <meta property="og:title" content="CN Schedule - Grid View">
    <meta property="og:description" content="Quick access to the Cartoon Network schedule.">
    <meta property="og:url" content="https://cn.sug.rocks/grid.html">
    <meta property="og:image" content="https://cn.sug.rocks/img/CN-Pink-Round.png">

    <!-- App Hell -->
    <meta name="apple-mobile-web-app-title" content="Schedule Grid">
    <meta name="application-name" content="Schedule Grid">
    <link rel="apple-touch-icon" sizes="57x57" href="/img/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/img/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/img/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/img/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/img/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/img/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/img/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/img/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/img/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
    <link rel="manifest" href="manifest.json">
    <meta name="msapplication-TileColor" content="#000000">
    <meta name="msapplication-TileImage" content="/img/ms-icon-144x144.png">
    <meta name="theme-color" content="#000000">
</head>
<body>
<div class="container">
    <div class="time-table">
        <div>
            Times
        </div>
{% for time in times %}
        <div class="time-line">
            <span class="time">{{ time }}</span>
        </div>
{% endfor %}
    </div>
{% for day in days %}
{% if day != '_' %}
    <div class="day">
        <div>
            {{ day }}
{% if days[day]['source'] == 'Screener' %}
            (from Zap2it)
{% endif %}
        </div>
{% if len(days[day]['schedule']) == 0 %}
        <div class="slot" style="height: 1400px; background-color: #ffeb00;">
            <span class="show">Empty<br>Waiting for updates.</span>
        </div>
{% endif %}
{% for show in days[day]['schedule'] %}
        <div title="{{ show.title }} ({{ show.time }})" class="slot" style="height: {{ show.slots * 25 }}px; color: {{ show.color_fg }}; background-color: {{ show.color_bg }};">
            <span class="show">{{ show.show }}</span>
        </div>
{% endfor %}
    </div>
{% endif %}
{% endfor %}
</div>
</body>
</html>
