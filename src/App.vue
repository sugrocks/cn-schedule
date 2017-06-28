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
        <td
          class="special-links"
          title="Settings"
          v-on:click="showSettings = !showSettings">
          <img src="./assets/icons/settings.svg">
        </td>
        <td
          class="special-links"
          title="Stats"
          v-on:click="showStats = !showStats">
          <img src="./assets/icons/bars.svg">
        </td>
        <router-link
          to="grid"
          tag="td"
          title="Grid"
          class="special-links">
          <img src="./assets/icons/grid.svg">
        </router-link>
        <td
          class="special-links"
          title="About"
          v-on:click="showAbout = !showAbout">
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
      json: {},
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
        t.json = JSON.parse(localStorage.getItem('schedule'))

        for (var i in t.json) {
          // Skip json "meta"
          if (i === '_') continue

          t.days.push({
            id: i,
            source: (t.json[i]['source'] === 'Screener' || t.json[i]['source'] === 'Zap2it') ? 'zap2it' : 'cn',
            past: (getToday().replace('-', '') > i.replace('-', '')),
            text: parseDate(i)
          })
        }

        var everythingCN = []
        for (var li in t.json) {
          if (t.json[li]['source'] === 'Cartoon Network') {
            for (var lj in t.json[li]['schedule']) {
              everythingCN.push(t.json[li]['schedule'][lj])
            }
          }
        }

        t.globalTotalBlocks = everythingCN.length
        t.globalStats = getStats(everythingCN)
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
