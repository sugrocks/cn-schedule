<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CN Schedule</title>

    <style>
        .container {
            overflow: scroll;
            width: 100vw;
            height: 100vh;
            position: fixed;
            top: 0;
            white-space: nowrap;
            left: 0;
        }

        .day {
            display: inline-block;
            vertical-align: top;
            width: 150px !important;
            font-size: 10px;
        }

        .time-table {
            display: inline-block;
            vertical-align: top;
            font-size: 10px;
        }

        .time-line {
            height: 25px;
            display:flex;
            align-items: center; /* Vertical center alignment */
            justify-content: center; /* Horizontal center alignment */
        }

        .slot {
            overflow: hidden;
            text-align: center;
            -webkit-box-shadow: inset 0px 0px 0px 1px #000;
            -moz-box-shadow: inset 0px 0px 0px 1px #f00;
            box-shadow: inset 0px 0px 0px 1px #000;
            display:flex;
            align-items: center; /* Vertical center alignment */
            justify-content: center; /* Horizontal center alignment */
        }

        .show {
            text-overflow: ellipsis;
            overflow: hidden;
        }
    </style>
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
    <div class="day">
        <div>
            {{ day }}
        </div>
{% for show in days[day] %}
        <div title="{{ show.title }} ({{ show.time }})" class="slot" style="height: {{ show.slots * 25 }}px; background-color: {{ show.color }};">
            <span class="show">{{ show.show }}</span>
        </div>
{% endfor %}
    </div>
{% endfor %}
</div>
</body>
</html>
