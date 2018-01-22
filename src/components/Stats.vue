<template>
  <div class="stats">
    <div class="stats-resume">
      We have {{ totalBlocks }} time slots with {{ dayStats.length }} shows.
    </div>
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
    <div class="source">
      Source of data: {{ source }}
    </div>
  </div>
</template>

<script>
import { parseDate } from '../assets/dates'

export default {
  name: 'stats',
  props: ['day'],
  data () {
    return {
      totalBlocks: 0,
      dayStats: {},
      source: '',
      ready: false
    }
  },
  methods: {
    pd (date) {
      return parseDate(date, true)
    },
    showStats (day) {
      if (day.cn) {
        this.source = 'Cartoon Network'
        this.dayStats = day.cn
      } else {
        this.source = 'Zap2it'
        this.dayStats = day.zap
      }

      this.totalBlocks = 0
      this.dayStats.forEach(el => {
        this.totalBlocks += el.slots
      })

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
    display: inline-block;
    text-align: left;

    tr + tr {
      border-top: 1px solid $semi-transparent-black;
    }
  }

  .source {
    font-size: small;
    margin-top: 1em;
  }
}
</style>
