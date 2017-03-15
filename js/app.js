/* global jQuery, localStorage */
// TODO: Comments

function getToday () {
  var today = new Date()
  var d = today.getDate()
  var m = today.getMonth() + 1 // Because January is 0
  var y = today.getFullYear()

  if (d < 10) d = '0' + d
  if (m < 10) m = '0' + m

  return y + '-' + m + '-' + d
}

function parseDate (date) {
  var dateSplit = date.split('-')
  var dobj = new Date(dateSplit[0], (dateSplit[1] - 1), dateSplit[2])
  return dobj.toLocaleDateString(
    'en-US',
    {
      weekday: 'short',
      month: 'long',
      day: 'numeric'
    }
  )
}

function loadSchedule () {
  var url = 'https://api.sug.rocks/ccnschedule.json'
  jQuery.getJSON(url, function (result) {
    localStorage.setItem('schedule', JSON.stringify(result))
  })
  .fail(function () {
    jQuery('h1').append('<small>(offline)</small>')
    jQuery('.dates').text('')
    jQuery('.schedule').text('Couldn\'t load the schedule. Once loaded, it\'ll be cached in your browser.')
  })
  .always(function () {
    var date

    if (window.location.hash) {
      date = window.location.hash.substring(1)
    } else {
      date = getToday()
    }

    getSchedule(date)
  })
}

function getSchedule (day) {
  var schedule = JSON.parse(localStorage.getItem('schedule'))
  if (schedule === null) return

  window.location = '#' + day

  jQuery('.dates').empty()
  jQuery('.schedule').empty()

  jQuery('.dates').append('<tr><th>Select a date</th></tr>')
  for (var i in schedule) {
    var date = '<tr><td onclick="getSchedule(\'' + i + '\')"'
    if (day === i) {
      date += ' class="selected"'
    }
    date += '>' + parseDate(i) + '</td></tr>'
    jQuery('.dates').append(date)
  }
  jQuery('.dates').append('<tr><td onclick="toggleAbout()" class="about-link">about...</td></tr>')

  for (var j in schedule[day]) {
    var field = schedule[day][j]
    var eptitle = field['title'] ? field['title'] : '&nbsp;'
    var row = '<tr>'
    row += '<td class="field-time">' + field['time'] + '</td>'
    row += '<td><span class="field-show">' + field['show'] + '</span>'
    row += '<br><span class="field-title">' + eptitle + '</span></td>'
    // row += '<td class="field-rating">' + field['rating'] + '</td>'
    row += '</tr>'
    jQuery('.schedule').append(row)
  }
}

function toggleAbout () { // eslint-disable-line
  jQuery('.about').slideToggle()
}

loadSchedule()
