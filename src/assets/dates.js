export function getDate (date, offset) {
  if (!date) date = new Date()
  if (offset) date.setDate(date.getDate() + offset) // Apply offset

  let d = date.getDate()
  let m = date.getMonth() + 1 // Because January is 0
  let y = date.getFullYear()

  if (d < 10) d = '0' + d
  if (m < 10) m = '0' + m

  return y + '-' + m + '-' + d
}

export function parseDate (date, shorter) {
  let dateSplit = date.split('-')
  let dobj = new Date(dateSplit[0], (dateSplit[1] - 1), dateSplit[2])

  return dobj.toLocaleDateString(
    'en-US',
    {
      weekday: shorter ? undefined : 'short',
      month: 'long',
      day: 'numeric'
    }
  )
}
