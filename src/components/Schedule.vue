<template>
  <table class="schedule">
    <tr v-if="isNotfound">
      <td colspan="2" class="schedule-notfound">
        <b>There's nothing at this date!</b><br>
        Either you're trying to load an old entry
        <small>
          (and you should take a look at the <a target="_blank" rel="noopener" href="https://cnschedulearchive.tumblr.com/tagged/cartoon-network">CN Schedule Archive</a>)
        </small>
        or you're way too much in the future.
      </td>
    </tr>
    <tr v-if="isZap">
      <td colspan="2" class="schedule-zap2it">
        This schedule was extracted from Zap2it.<br>
        Expect it to be wrong as changes <i>might</i> happen.
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
import { getToday } from '../assets/dates.js'
var _ = require('lodash')

export default {
  name: 'schedule',
  data () {
    return {
      isZap: false,
      isNotfound: false,
      pageTitle: 'Loading...',
      blocks: []
    }
  },
  metaInfo () {
    return {
      title: this.pageTitle,
      meta: [
        { name: 'twitter:title', content: this.pageTitle + ' | CN Schedule' },
        { name: 'og:title', content: this.pageTitle + ' | CN Schedule' },
        { name: 'og:url', content: 'https://cn.sug.rocks' + this.$route.path }
      ]
    }
  },
  methods: {
    orderBlocks (list) {
      // Order the schedule by date
      return _.orderBy(list, 'timestamp')
    },
    getSchedule () {
      // Get date from route and the saved schedule
      var d = this.$route.params.date
      var schedule = JSON.parse(localStorage.getItem('schedule'))

      if (d === 'undefined' || d === undefined) {
        // Redirect to today's date if route not set
        this.$router.replace({name: 'Schedule', params: {date: getToday()}})
      } else {
        // Change page title early for search engines and stuff
        this.pageTitle = d

        // Check if data is loaded
        if (schedule === null || !this.$parent.scheduleReloaded) {
          console.log('Schedule not ready, wait a second please...')
          setTimeout(this.getSchedule, 1000)
          return
        }

        // Now we try to find that date
        if (d in schedule) {
          // If day found, let's load it
          this.blocks = schedule[d]['schedule']
          this.$parent.$data.blocks = this.$data.blocks
          this.isZap = (schedule[d]['source'] === 'Zap2it')
          this.isNotfound = false
        } else {
          // If day not found from the route
          // Check if it's a proper date
          if (d.match(/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/)) {
            // If it is, say we don't have it
            this.isNotfound = true
            this.pageTitle = '404'
          } else {
            // else, fucking 404
            this.$router.replace({name: 'NotFound'})
          }
        }

        // And our first loading is done!
        this.$parent.isReady = true
      }
    }
  },
  watch: {
    // on route changes, load schedule
    '$route': 'getSchedule'
  },
  mounted () {
    this.getSchedule()
  }
}
</script>