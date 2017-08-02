<template>
  <div id="app" :class="datesSettings">
    <app-about v-show="showAbout"></app-about>
    <app-settings v-show="showSettings"></app-settings>
    <schedule-stats v-if="showStats"></schedule-stats>

    <h1>CN Schedule</h1>

    <transition name="slideOut">
      <h2 v-if="!isReady" class="loading">Loading data...</h2>
    </transition>

    <!-- Left column with dates -->
    <table class="dates">
      <day-el
        v-for="day in days"
        v-bind:day="day"
        v-bind:key="day.id">
      </day-el>
      <tr>
        <td
          class="special-links"
          title="Settings"
          :class="((showSettings) ? 'selected ' : '') + 'special-links'"
          v-on:click="showSettings = !showSettings">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="3" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" fill="none" stroke="#fff" stroke-miterlimit="10" stroke-width="2"/>
          </svg>
        </td>
        <td
          title="Stats"
          :class="((showStats) ? 'selected ' : '') + 'special-links'"
          v-on:click="showStats = !showStats">
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
          v-on:click="showAbout = !showAbout">
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

export default {
  name: 'app',
  data () {
    return {
      isReady: false,
      scheduleReloaded: false,
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
  metaInfo () {
    return {
      title: 'Loading...',
      titleTemplate: '%s | CN Schedule'
    }
  },
  mounted () {
    // Shortcut and URL to our API
    var t = this
    var url = 'https://api.sug.rocks/cnschedule.json'

    // Get config from localStorage
    t.config.showPast = (localStorage.getItem('cfgShowPast') === 'true')
    t.config.showZap = (localStorage.getItem('cfgShowZap') === 'true' || localStorage.getItem('cfgShowZap') === null)
    t.config.showGlobal = (localStorage.getItem('cfgShowGlobal') === 'true')

    // Fetch our API
    t.$http.get(url)
      .then(data => {
        // Save schedule in localStorage (for offline and faster next loads)
        localStorage.setItem('schedule', JSON.stringify(data.body))
      }, err => {
        console.log(err)
      })
      .then(function () {
        // Parse the data saved on the localStorage (now or before)
        t.json = JSON.parse(localStorage.getItem('schedule'))

        // Go through every day
        for (var i in t.json) {
          // Skip json "meta"
          if (i === '_') continue

          // Push available dates to the menu
          t.days.push({
            id: i,
            source: t.json[i]['source'],
            past: (getToday().replace('-', '') > i.replace('-', '')),
            text: parseDate(i)
          })
        }

        // For stats
        var everythingCN = []
        for (var li in t.json) {
          // For every day who the source is Cartoon Network
          if (t.json[li]['source'] === 'Cartoon Network') {
            // Push every episode to the array
            for (var lj in t.json[li]['schedule']) {
              everythingCN.push(t.json[li]['schedule'][lj])
            }
          }
        }

        // Get total number of slots and get stats per-show
        t.globalTotalBlocks = everythingCN.length
        t.globalStats = getStats(everythingCN)
        t.scheduleReloaded = true
      })
  },
  methods: {
    saveSettings () {
      // Save your settings in your browser with localStorage
      localStorage.setItem('cfgShowPast', this.config.showPast)
      localStorage.setItem('cfgShowZap', this.config.showZap)
      localStorage.setItem('cfgShowGlobal', this.config.showGlobal)
    }
  },
  computed: {
    datesSettings: function () {
      // To style or show/hide elements on the menu based on the user settings
      return {
        'noZap': !this.config.showZap,
        'noPast': !this.config.showPast
      }
    }
  }
}
</script>
