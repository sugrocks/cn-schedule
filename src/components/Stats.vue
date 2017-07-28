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
        <table class="top-shows day" v-if="dayStats.length > 0">
          <thead>
            <tr>
              <th>Show</th>
              <th colspan="2">Slots</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="show in dayStats">
              <td>{{ show.name }}</td>
              <td style="text-align: right;">{{ show.count }}</td>
              <td>~{{ show.percentage }}%</td>
            </tr>
          </tbody>
        </table>
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
        <table class="top-shows all" v-if="$parent.globalStats.length > 0">
          <thead>
            <tr>
              <th>Show</th>
              <th colspan="2">Slots</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="show in $parent.globalStats">
              <td>{{ show.name }}</td>
              <td style="text-align: right;">{{ show.count }}</td>
              <td>~{{ show.percentage }}%</td>
            </tr>
          </tbody>
        </table>
      </div>
      <br>
      <small><a v-on:click="$parent.showStats = !$parent.showStats">(close)</a></small>
    </div>
  </transition>
</template>

<script>
import { getStats } from '../assets/stats.js'

export default {
  name: 'stats',
  data () {
    return {
      day: this.$route.params.date,
      totalBlocks: 0,
      dayStats: []
    }
  },
  mounted () {
    // Load data in this component
    this.totalBlocks = this.$parent.$data.blocks.length
    this.dayStats = getStats(this.$parent.$data.blocks)
  }
}
</script>
