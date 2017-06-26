<template>
  <div id="app">
    <app-about v-show="showAbout"></app-about>
    <schedule-stats v-if="showStats"></schedule-stats>

    <h1>CN Schedule</h1>

    <!-- Left column with dates -->
    <table class="dates">
      <day-el
        v-for="day in days"
        v-bind:day="day"
        v-bind:key="day.id">
      </day-el>
      <tr>
        <td v-on:click="showStats = !showStats">
          Stats
        </td>
      </tr>
      <tr>
        <td v-on:click="showAbout = !showAbout">
          About
        </td>
      </tr>
    </table>

    <router-view></router-view>
  </div>
</template>

<script>
var jQuery = require('jquery')
import { count } from './assets/stats.js'
import { parseDate } from './assets/dates.js'

export default {
  name: 'app',
  data () {
    return {
      isZap: false,
      showAbout: false,
      showStats: false,
      days: [],
      blocks: [],
      globalTotalBlocks: 0,
      globalStats: []
    }
  },
  mounted () {
    var url = 'https://api.sug.rocks/cnschedule.json'
    var d = this
    jQuery.getJSON(url, function (result) {
      localStorage.setItem('schedule', JSON.stringify(result))
      for (var i in result) {
        // Skip json "meta"
        if (i === '_') continue

        d.$data.days.push({
          id: i,
          source: (result[i]['source'] === 'Screener' || result[i]['source'] === 'Zap2it') ? 'zap2it' : 'cn',
          text: parseDate(i)
        })
      }
    })
    .fail(function () {
      document.querySelector('h1').innerHTML += ' <small>(offline)</small>'
    })
    .done(function (result) {
      var everyshow = []

      for (var li in result) {
        if (result[li]['source'] === 'Cartoon Network') {
          for (var lj in result[li]['schedule']) {
            everyshow.push(result[li]['schedule'][lj])
          }
        }
      }

      d.$data.globalTotalBlocks = everyshow.length
      var showstats = count(everyshow)

      for (var lk in showstats) {
        var f = showstats[lk]
        d.globalStats.push({
          name: f[0],
          count: f[1],
          percentage: Math.round((f[1] / everyshow.length) * 100)
        })
      }
    })
  }
}
</script>

<style>
@import './assets/styles.css';
</style>
