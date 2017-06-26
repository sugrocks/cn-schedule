<template>
  <div id="app">
    <app-about v-show="showAbout"></app-about>
    <app-settings v-show="showSettings"></app-settings>
    <schedule-stats v-if="showStats"></schedule-stats>

    <h1>CN Schedule</h1>

    <!-- Left column with dates -->
    <table :class="datesSettings">
      <day-el
        v-for="day in days"
        v-bind:day="day"
        v-bind:key="day.id">
      </day-el>
      <tr>
        <td class="special-links" v-on:click="showSettings = !showSettings">
          <img src="./assets/icons/settings.svg">
        </td>
        <td class="special-links" v-on:click="showStats = !showStats">
          <img src="./assets/icons/bars.svg">
        </td>
        <td class="special-links" v-on:click="showAbout = !showAbout">
          <img src="./assets/icons/info.svg">
        </td>
      </tr>
    </table>

    <router-view></router-view>
  </div>
</template>

<style lang="scss">
@import './assets/app';
</style>

<script>
var jQuery = require('jquery')
import { count } from './assets/stats.js'
import { getToday, parseDate } from './assets/dates.js'

export default {
  name: 'app',
  data () {
    return {
      isZap: false,
      showSettings: false,
      showAbout: false,
      showStats: false,
      config: {
        showPast: false,
        showZap: true,
        showGlobal: true
      },
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
          past: (getToday().replace('-', '') > i.replace('-', '')),
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
  },
  computed: {
    datesSettings: function () {
      return {
        'dates': true,
        'noZap': !this.config.showZap,
        'noPast': !this.config.showPast
      }
    }
  }
}
</script>
