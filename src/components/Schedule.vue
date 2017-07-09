<template>
  <table class="schedule">
    <tr v-if="isNotfound">
      <td colspan="3" class="schedule-notfound">
        <b>There's nothing at this date!</b><br>
        Either you're trying to load an old entry <small>(and you should take a look at the <a href="https://cnschedulearchive.tumblr.com/tagged/cartoon-network">CN Schedule Archive</a>)</small> or you're way too much in the future.
      </td>
    </tr>
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
      isNotfound: false,
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

      if (!('_' in schedule)) {
        console.log('Schedule not ready')
        setTimeout(this.getSchedule, 1000)
        return
      }

      if (d === 'undefined' || d === undefined) {
        this.$router.replace({name: 'Schedule', params: {date: getToday()}})
      } else {
        if (d in schedule) {
          this.blocks = schedule[d]['schedule']
          this.$parent.$data.blocks = this.$data.blocks
          this.isZap = (schedule[d]['source'] === 'Zap2it')
          this.isNotfound = false
        } else {
          // Check if it's a proper date
          if (d.match(/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/)) {
            // If it is, say we don't have it
            this.isNotfound = true
          } else {
            // else, fucking 404
            this.$router.replace({name: 'NotFound'})
          }
        }
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
