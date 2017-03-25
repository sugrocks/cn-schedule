/* global jQuery, localStorage */
// TODO: Comments

var firstStart = true

function Comparator(a, b) {
  // http://stackoverflow.com/a/5435341
  if (a[1] < b[1]) return -1
  if (a[1] > b[1]) return 1
  return 0
}

function count(array_elements) {
  // http://stackoverflow.com/a/19395324
  array_elements.sort()
  var results = []
  var current = null
  var cnt = 0

  for (var i = 0; i < array_elements.length; i++) {
    if (array_elements[i] != current) {
      if (array_elements[i] === undefined) continue

      if (cnt > 0) {
        results.push([current, cnt])
      }

      current = array_elements[i]
      cnt = 1
    } else {
      cnt++
    }
  }

  if (cnt > 0) {
    results.push([current, cnt])
  }

  return results.sort(Comparator).reverse()
}

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
  var everyshow = []

  jQuery('.dates').empty()
  jQuery('.schedule').empty()
  jQuery('.top-shows.day tbody').empty()

  jQuery('.dates').append('<tr><th>Select a date</th></tr>')
  for (var i in schedule) {
    // Skip json "meta"
    if (i === '_') continue

    var date = '<tr><td onclick="getSchedule(\'' + i + '\')"'
    if (day === i) {
      date += ' class="selected"'
    }
    date += '>' + parseDate(i) + '</td></tr>'
    jQuery('.dates').append(date)
  }
  jQuery('.dates').append('<tr><td onclick="toggleStats()" class="about-link">stats...</td></tr>')
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

    everyshow.push(field['show'])
  }

  var totalblocks = everyshow.length
  var showstats = count(everyshow)

  for (var k in showstats) {
    var field = showstats[k]
    var row = '<tr>'
    row += '<td>' + field[0] + '</td>'
    row += '<td>' + field[1] + '</td>'
    row += '<td>~' + Math.round((field[1] / totalblocks) * 100) + '%</td>'
    row += '</tr>'
    jQuery('.top-shows.day tbody').append(row)
  }

  jQuery('.stats-text.day').text('For ' + day + ', we have ' + totalblocks + ' time blocks with ' + showstats.length + ' shows.')

  if (firstStart) {
    for (var li in schedule) {
      for (var lj in schedule[li]) {
        everyshow.push(schedule[li][lj]['show'])
      }
    }

    totalblocks = everyshow.length
    showstats = count(everyshow)

    for (var lk in showstats) {
      var field = showstats[lk]
      var row = '<tr>'
      row += '<td>' + field[0] + '</td>'
      row += '<td>' + field[1] + '</td>'
      row += '<td>~' + Math.round((field[1] / totalblocks) * 100) + '%</td>'
      row += '</tr>'
      jQuery('.top-shows.all tbody').append(row)
    }

    jQuery('.stats-text.all').text('For all the dates available, we have ' + totalblocks + ' time blocks with ' + showstats.length + ' shows.')
    firstStart = false
  }
}

function toggleAbout () { // eslint-disable-line
  jQuery('.about').slideToggle()
}

function toggleStats () { // eslint-disable-line
  jQuery('.stats').slideToggle()
}

loadSchedule()
