<template>
  <table class="schedule">
    <tr v-if="isZap">
      <td colspan="3" class="schedule-zap2it">
        This schedule was extracted from Zap2it. Changes might happen.
      </td>
    </tr>
    <schedule-el
      v-for="block in orderBlocks(blocks)"
      v-bind:block="block"
      v-bind:key="block.timestamp">
    </schedule-el>
  </table>
</template>

<script>
var _ = require('lodash')
import { getToday } from '../assets/dates.js'

export default {
  name: 'schedule',
  data () {
    return {
      isZap: false,
      blocks: []
    }
  },
  methods: {
    orderBlocks (list) {
      return _.orderBy(list, 'timestamp')
    },
    getSchedule () {
      var d = this.$route.params.date
      var schedule = JSON.parse(localStorage.getItem('schedule'))

      try {
        console.log('Schedule generated', (new Date(schedule['_']['generated'] * 1000)))
      } catch (e) {
        console.log('Schedule not ready\n', e)
        setTimeout(this.getSchedule, 1000)
        return
      }

      if (d === 'undefined' || d === undefined) {
        this.$router.replace({name: 'Schedule', params: {date: getToday()}})
      } else {
        this.$data.blocks = schedule[d]['schedule']
        this.$parent.$data.blocks = this.$data.blocks
        this.$data.isZap = (schedule[d]['source'] === 'Zap2it' || schedule[d]['source'] === 'Screener')
      }
    }
  },
  watch: {
    // on route changes
    '$route': 'getSchedule'
  },
  mounted () {
    this.getSchedule()
  }
}
</script>
