<template>
  <div class="stats">
    <div class="stats-resume">
      We have {{ totalBlocks }} time slots with {{ dayStats.length }} shows.
    </div>
    <div class="two-columns-stats">
      <table class="top-shows">
        <thead>
          <tr>
            <th>Show</th>
            <th colspan="2">Slots</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="show in dayStats"
            :key="show.title">
            <td>{{ show.title }}</td>
            <td style="text-align: right;">{{ show.slots }}</td>
            <td>~{{ show.percentage }}%</td>
          </tr>
        </tbody>
      </table>
      <div class="show-chart">
        <canvas id="chart" width="200" height="200"></canvas>
      </div>
    </div>
    <div class="source">
      Source of data: {{ source }}
    </div>
  </div>
</template>

<script>
import { parseDate } from '../assets/dates'
import ColorHash from 'color-hash'
import Chart from 'chart.js/dist/Chart.min.js'

export default {
  name: 'stats',
  props: ['day'],
  data () {
    return {
      totalBlocks: 0,
      dayStats: {},
      source: '',
      ready: false,
      chartObj: undefined,
      statsCharts: {}
    }
  },
  methods: {
    pd (date) {
      return parseDate(date, true)
    },
    showStats (day) {
      // Clear data
      this.statsCharts = {
        labels: [],
        datasets: [{
          backgroundColor: [],
          data: []
        }]
      }

      // Load available sources
      if (day.cn) {
        this.source = 'Cartoon Network'
        this.dayStats = day.cn
      } else {
        this.source = 'Zap2it'
        this.dayStats = day.zap
      }

      // Count blocks and add to chart
      this.totalBlocks = 0
      this.dayStats.forEach(el => {
        let color = '#fff'

        if (el.color) {
          color = el.color
        } else {
          let colorHash = new ColorHash({ lightness: (el.title % 10) / 10 })
          color = colorHash.hex(el.title)
        }

        this.totalBlocks += el.slots
        this.statsCharts.labels.push(el.title)
        this.statsCharts.datasets[0].data.push(el.slots)
        this.statsCharts.datasets[0].backgroundColor.push(color)
      })

      // Load chart
      this.chartObj = new Chart('chart', {
        type: 'doughnut',
        data: this.statsCharts,
        options: {
          layout: {
            padding: 15
          },
          legend: {
            display: false
          }
        }
      })

      // Set as ready
      setTimeout(_ => {
        this.ready = true
      }, 500)
    }
  },
  watch: {
    day: function () {
      this.ready = false
      this.chartObj.destroy() // We need to destroy the chart to re-use it
      this.showStats(this.day)
    }
  },
  mounted () {
    this.ready = false
    this.showStats(this.day)
  }
}
</script>

<style lang="scss" scoped>
@import '../assets/colors';

.stats {
  padding: 15px;
  text-align: center;

  .stats-resume {
    margin-bottom: 1em;
    font-weight: bold;
  }

  .top-shows {
    text-align: left;
    width: 100%;

    tr + tr {
      border-top: 1px solid $semi-transparent-black;
    }
  }

  .show-chart {
    padding: 50px;
  }

  .source {
    font-size: small;
    margin-top: 1em;
  }
}

@media screen and (min-width: 630px) {
  .stats {
    .two-columns-stats {
      .top-shows {
        display: inline-block;
        width: initial;
      }

      .show-chart {
        display: inline-block;
        padding: 0;
        vertical-align: top;
        width: 200px;
      }
    }
  }
}
</style>
