<template>
  <div id="app" :class="datesSettings">
    <app-about v-show="showAbout"></app-about>
    <app-settings v-show="showSettings"></app-settings>
    <schedule-stats v-if="showStats"></schedule-stats>

    <h1>CN Schedule</h1>

    <transition name="slideOut">
      <h2 v-if="!isReady || !scheduleReloaded" class="loading">Loading data...</h2>
    </transition>

    <!-- Left column with dates -->
    <table class="dates">
      <tr v-if="newUpdate">
        <td
          class="update"
          colspan="4"
          @click="reload">
          <b>APP UPDATE</b><br>
          Click here to reload.
        </td>
      </tr>
      <day-el
        v-for="day in days"
        :day="day"
        :key="day.id">
      </day-el>
      <tr>
        <td
          class="special-links"
          title="Settings"
          :class="((showSettings) ? 'selected ' : '') + 'special-links'"
          @click="showSettings = !showSettings">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="3" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" fill="none" stroke="#fff" stroke-miterlimit="10" stroke-width="2"/>
          </svg>
        </td>
        <td
          title="Stats"
          :class="((showStats) ? 'selected ' : '') + 'special-links'"
          @click="showStats = !showStats">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <rect x="10" y="3" width="4" height="18" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            <rect x="18" y="8" width="4" height="13" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            <rect x="2" y="13" width="4" height="8" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
          </svg>
        </td>
        <router-link
          to="grid"
          tag="td"
          title="Grid"
          class="special-links">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="7" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            <rect x="14" y="3" width="7" height="7" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            <rect x="14" y="14" width="7" height="7" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            <rect x="3" y="14" width="7" height="7" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
          </svg>
        </router-link>
        <td
          title="About"
          :class="((showAbout) ? 'selected ' : '') + 'special-links'"
         @click="showAbout = !showAbout">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12" y2="8"/>
          </svg>
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
import ColorHash from 'color-hash'
import store from 'store/dist/store.modern'

export default {
  name: 'app',
  data () {
    return {
      newUpdate: false,
      isReady: false,
      scheduleReloaded: false,
      isZap: false,
      isNotFound: false,
      showSettings: false,
      showAbout: false,
      showStats: false,
      config: {
        showPast: false,
        showZap: true,
        showGlobal: false
      },
      days: [],
      json: undefined,
      globalTotalBlocks: 0,
      globalMinMax: [],
      globalStats: [],
      globalStatsCharts: {
        labels: [],
        datasets: [{
          backgroundColor: [],
          data: []
        }]
      }
    }
  },
  metaInfo () {
    return {
      title: 'Loading...',
      titleTemplate: '%s | CN Schedule'
    }
  },
  mounted () {
    // Shortcut and URL to our API
    var t = this
    var url = 'https://api.ctoon.network/schedule/all.json'

    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.addEventListener('controllerchange', () => {
        t.newUpdate = true
      })
    }

    // Get config from store
    let conf = store.get('appConfig')
    if (conf === undefined) {
      conf = {
        showPast: false,
        showZap: true,
        showGlobal: false
      }
    }

    t.config = conf

    // Test if online
    if (navigator.onLine) {
      // Fetch our API
      fetch(url)
        .then(response => {
          // Directly return the JSON to our next promise
          return response.json()
        }, err => {
          console.error(err)
          t.loadData(store.get('schedule'))
        })
        .then(data => {
          // Save schedule in store (for offline use)
          store.set('schedule', data)
          // Save json in an accessible variable
          t.json = data
          t.loadData(data)
        })
    } else {
      console.log('You are offline.')
      t.loadData(store.get('schedule'))
    }
  },
  methods: {
    saveSettings () {
      // Save your settings in your browser with store (add a delay to make sure model updates)
      setTimeout(() => {
        store.set('appConfig', this.config)
      }, 500)
    },
    loadData (data) {
      var t = this

      // Go through every day
      for (var i in data) {
        // Skip json "meta"
        if (i === '_') continue

        // Push available dates to the menu
        t.days.push({
          id: i,
          source: data[i]['source'],
          past: (getToday().replace('-', '') > i.replace('-', '')),
          text: parseDate(i)
        })
      }

      // For stats
      var everythingCN = []
      for (var li in data) {
        // For every day who the source is Cartoon Network
        if (data[li]['source'] === 'Cartoon Network') {
          // Push every episode to the array
          for (var lj in data[li]['schedule']) {
            everythingCN.push(data[li]['schedule'][lj])
          }
        }
      }

      t.globalMinMax = {
        min: everythingCN[0].date,
        max: everythingCN[everythingCN.length - 1].date
      }

      // Get total number of slots and get stats per-show
      t.globalTotalBlocks = everythingCN.length
      var s = getStats(everythingCN)
      t.globalStats = s[0]
      t.globalStatsCharts.labels = s[1]
      t.globalStatsCharts.datasets[0].data = s[2]

      for (var si = 0; si < s[1].length; si++) {
        var colorHash = new ColorHash({lightness: 0.5})
        t.globalStatsCharts.datasets[0].backgroundColor.push(colorHash.hex(s[1][si]))
      }

      t.scheduleReloaded = true
    },
    reload () {
      window.location.reload()
    }
  },
  computed: {
    datesSettings: function () {
      // To style or show/hide elements on the menu based on the user settings
      return {
        'app': true,
        'no-zap': !this.config.showZap,
        'no-past': !this.config.showPast
      }
    }
  }
}
</script>
