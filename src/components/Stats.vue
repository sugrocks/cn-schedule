<template>
  <transition name="slide">
    <div class="stats modal">
      <div class="day-stats" v-if="$route.name === 'grid'">
        <span class="stats-text day no-data">
          Please select a day to get stats from it.
        </span>
      </div>
      <div class="day-stats" v-else>
        <span class="stats-text day no-data" v-if="dayStats.length === 0">
          No statistics available for this day.
        </span>
        <span class="stats-text day" v-else>
          For {{ day }}, we have {{ totalBlocks }} time slots with {{ dayStats.length }} shows.
        </span>
        <br>
        <div class="two-columns-stats" v-if="dayStats.length > 0">
          <table class="top-shows day">
            <thead>
              <tr>
                <th>Show</th>
                <th colspan="2">Slots</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="show in dayStats"
                :key="show.name">
                <td>{{ show.name }}</td>
                <td style="text-align: right;">{{ show.count }}</td>
                <td>~{{ show.percentage }}%</td>
              </tr>
            </tbody>
          </table>
          <stats-chart :chartData="dayStatsCharts" />
        </div>
      </div>
      <div class="global-stats" v-if="$parent.config.showGlobal">
        <br>
        <span class="stats-text all no-data" v-if="$parent.globalStats.length === 0">
          No global statistics available for the moment.
        </span>
        <span class="stats-text all" v-else>
          From official sources, we have {{ $parent.globalTotalBlocks }} time slots with {{ $parent.globalStats.length }} shows.
        </span>
        <br>
        <div class="two-columns-stats">
          <table class="top-shows all" v-if="$parent.globalStats.length > 0">
            <thead>
              <tr>
                <th>Show</th>
                <th colspan="2">Slots</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="show in $parent.globalStats"
                :key="show.name">
                <td>{{ show.name }}</td>
                <td style="text-align: right;">{{ show.count }}</td>
                <td>~{{ show.percentage }}%</td>
              </tr>
            </tbody>
          </table>
          <stats-chart :chartData="$parent.globalStatsCharts" />
        </div>
      </div>
      <br>
      <small><a @click="$parent.showStats = !$parent.showStats">(close)</a></small>
    </div>
  </transition>
</template>

<script>
import { getStats } from '../assets/stats.js'
import StatsChart from './StatsChart'
import ColorHash from 'color-hash'

export default {
  name: 'stats',
  components: { StatsChart },
  data () {
    return {
      day: this.$route.params.date,
      totalBlocks: 0,
      dayStats: [],
      dayStatsCharts: {
        labels: [],
        datasets: [{
          backgroundColor: [],
          data: []
        }]
      }
    }
  },
  mounted () {
    // Don't fetch data that doesn't exist if 404
    if (!this.$parent.$data.isNotFound && this.$route.params.date !== undefined) {
      // Load data in this component
      this.totalBlocks = this.$parent.$data.blocks.length
      var s = getStats(this.$parent.$data.blocks)
      this.dayStats = s[0]
      this.dayStatsCharts.labels = s[1]
      this.dayStatsCharts.datasets[0].data = s[2]
      for (var si = 0; si < s[1].length; si++) {
        var colorHash = new ColorHash({lightness: 0.5})
        this.dayStatsCharts.datasets[0].backgroundColor.push(colorHash.hex(s[1][si]))
      }
    }
  }
}
</script>
