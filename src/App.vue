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
import { getStats } from './assets/stats.js'
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
        showGlobal: false
      },
      days: [],
      blocks: [],
      globalTotalBlocks: 0,
      globalStats: []
    }
  },
  mounted () {
    var t = this
    var url = 'https://api.sug.rocks/cnschedule.json'

    t.config.showPast = (localStorage.getItem('cfgShowPast') === 'true')
    t.config.showZap = (localStorage.getItem('cfgShowZap') === 'true' || localStorage.getItem('cfgShowZap') === null)
    t.config.showGlobal = (localStorage.getItem('cfgShowGlobal') === 'true')

    t.$http.get(url)
      .then(data => {
        localStorage.setItem('schedule', JSON.stringify(data.body))
      }, err => {
        console.log(err)
      })
      .then(function () {
        var j = JSON.parse(localStorage.getItem('schedule'))

        for (var i in j) {
          // Skip json "meta"
          if (i === '_') continue

          t.$data.days.push({
            id: i,
            source: (j[i]['source'] === 'Screener' || j[i]['source'] === 'Zap2it') ? 'zap2it' : 'cn',
            past: (getToday().replace('-', '') > i.replace('-', '')),
            text: parseDate(i)
          })
        }

        var everythingCN = []
        for (var li in j) {
          if (j[li]['source'] === 'Cartoon Network') {
            for (var lj in j[li]['schedule']) {
              everythingCN.push(j[li]['schedule'][lj])
            }
          }
        }

        t.globalTotalBlocks = everythingCN.length
        getStats(everythingCN, t.globalStats)
      })
  },
  methods: {
    saveSettings () {
      localStorage.setItem('cfgShowPast', this.config.showPast)
      localStorage.setItem('cfgShowZap', this.config.showZap)
      localStorage.setItem('cfgShowGlobal', this.config.showGlobal)
    }
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
