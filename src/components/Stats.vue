<template>
  <div class="stats">
    <div class="stats-resume">
      There's {{ dayStats.length }} shows listed.
    </div>

    <div class="source">
      Source of data: {{ source }}
    </div>

    <div class="two-columns-stats">
      <table class="top-shows">
        <thead>
          <tr>
            <th>Show</th>
            <th colspan="2">Minutes</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="show in dayStats"
            :key="show.title">
            <td>{{ show.title }}</td>
            <td style="text-align: right;">{{ show.minutes || (show.slots * 15) }}</td>
            <td>~{{ show.percentage }}%</td>
          </tr>
        </tbody>
      </table>

      <div class="show-chart">
        <canvas id="chart" width="200" height="200"></canvas>
      </div>
    </div>
  </div>
</template>

<script>
import ColorHash from 'color-hash'
import { Chart, ArcElement, DoughnutController, Tooltip } from 'chart.js'
Chart.register(ArcElement, DoughnutController, Tooltip)

export default {
  name: 'stats',
  props: ['day', 'fallback'],
  data () {
    return {
      dayStats: {},
      source: '',
      chartObj: undefined,
      statsCharts: {}
    }
  },
  watch: {
    day: function () {
      this.showStats(this.day)
    }
  },
  mounted () {
    this.showStats(this.day)
  },
  methods: {
    showStats (day) {
      // We need to destroy the chart to re-use it
      if (this.chartObj) this.chartObj.destroy()

      // Clear data
      this.statsCharts = {
        labels: [],
        datasets: [{
          label: 'Minutes',
          data: [],
          backgroundColor: []
        }],
        hoverOffset: 4
      }

      // Load available sources
      if (day.cn) {
        this.source = 'Cartoon Network'
        this.dayStats = day.cn
      } else if (day.zap && (this.fallback === 'zap' || !day.tvguide)) {
        this.source = 'Zap2it'
        this.dayStats = day.zap
      } else {
        this.source = 'TVGuide'
        this.dayStats = day.tvguide
      }

      // Add to chart data
      this.dayStats.forEach(el => {
        const title = (el.title.length > 25) ? el.title.substr(0, 22) + '…' : el.title
        let color = '#fff'

        if (el.color) {
          color = el.color
        } else {
          const colorHash = new ColorHash({ lightness: (el.title % 10) / 10 })
          color = colorHash.hex(el.title)
        }

        this.statsCharts.labels.push(title)
        this.statsCharts.datasets[0].data.push(el.minutes || (el.slots * 15))
        this.statsCharts.datasets[0].backgroundColor.push(color)
      })

      // Load chart
      this.chartObj = window.myChart = new Chart('chart', {
        type: 'doughnut',
        data: this.statsCharts,
        options: {
          animation: false,
          layout: {
            padding: 15
          }
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
@import '../assets/colors';

.stats {
  padding: 15px;
  text-align: center;

  .stats-resume {
    font-weight: bold;
  }

  .source {
    font-size: small;
    margin-bottom: 1.5em;
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
}

@media screen and (min-width: 675px) {
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
