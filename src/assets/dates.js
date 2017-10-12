export function getToday () {
  var today = new Date()
  var d = today.getDate()
  var m = today.getMonth() + 1 // Because January is 0
  var y = today.getFullYear()

  if (d < 10) d = '0' + d
  if (m < 10) m = '0' + m

  return y + '-' + m + '-' + d
}

export function parseDate (date, shorter) {
  var dateSplit = date.split('-')
  var dobj = new Date(dateSplit[0], (dateSplit[1] - 1), dateSplit[2])
  return dobj.toLocaleDateString(
    'en-US',
    {
      weekday: shorter ? undefined : 'short',
      month: 'long',
      day: 'numeric'
    }
  )
}
