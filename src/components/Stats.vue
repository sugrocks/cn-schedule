<template>
  <transition name="slide">
    <div class="stats modal">
      <div class="day-stats">
        <span class="stats-text day no-data" v-if="dayStats.length === 0">
          No statistics available for this day.
        </span>
        <span class="stats-text day" v-else>
          For {{ day }}, we have {{ totalBlocks }} time blocks with {{ dayStats.length }} shows.
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
          From official sources, we have {{ $parent.globalTotalBlocks }} time blocks with {{ $parent.globalStats.length }} shows.
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
import { count } from '../assets/stats.js'

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
    var d = this.$parent.$data
    this.totalBlocks = d.blocks.length
    var showstats = count(d.blocks)
    for (var k in showstats) {
      var f = showstats[k]
      this.$data.dayStats.push(
        {
          name: f[0],
          count: f[1],
          percentage: Math.round((f[1] / d.blocks.length) * 100)
        }
      )
    }
  }
}
</script>
