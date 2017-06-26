var _ = require('lodash')

function Comparator (a, b) {
  // http://stackoverflow.com/a/5435341
  if (a[1] < b[1]) return -1
  if (a[1] > b[1]) return 1
  return 0
}

export function count (els) {
  // http://stackoverflow.com/a/19395324
  var arrayEls = _.orderBy(els, 'show')
  var results = []
  var current = null
  var cnt = 0

  for (var i = 0; i < arrayEls.length; i++) {
    if (arrayEls[i]['show'] !== current) {
      if (arrayEls[i]['show'] === undefined) continue

      if (cnt > 0) {
        results.push([current, cnt])
      }

      current = arrayEls[i]['show']
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
