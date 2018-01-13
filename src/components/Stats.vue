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
        <stats-chart v-if="ready" :chartData="dayStatsCharts" />
      </div>
    </div>
    <div class="source">
      Source of data: {{ source }}
    </div>
  </div>
</template>

<script>
import { parseDate } from '../assets/dates'
import StatsChart from '../assets/StatsChart'
import ColorHash from 'color-hash'

export default {
  name: 'stats',
  props: ['day'],
  components: { StatsChart },
  data () {
    return {
      totalBlocks: 0,
      dayStats: {},
      source: '',
      ready: false,
      dayStatsCharts: {
        labels: [],
        datasets: [{
          backgroundColor: [],
          data: []
        }]
      }
    }
  },
  methods: {
    pd (date) {
      return parseDate(date, true)
    },
    showStats (day) {
      let s = {}
      this.dayStatsCharts.labels = []
      this.dayStatsCharts.datasets[0].data = []
      this.dayStatsCharts.datasets[0].backgroundColor = []

      if (day.cn) {
        this.source = 'Cartoon Network'
        s = day.cn
      } else {
        this.source = 'Zap2it'
        s = day.zap
      }

      this.dayStats = s
      this.totalBlocks = s.length

      for (let i = 0; i < s.length; i++) {
        let colorHash = new ColorHash({ lightness: (s[i]['title'].length % 10) / 10 })

        this.dayStatsCharts.labels.push(s[i]['title'])
        this.dayStatsCharts.datasets[0].data.push(s[i]['slots'])
        this.dayStatsCharts.datasets[0].backgroundColor.push(colorHash.hex(s[i]['title']))
      }

      setTimeout(_ => {
        this.ready = true
      }, 500)
    }
  },
  watch: {
    day: function () {
      this.ready = false
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
