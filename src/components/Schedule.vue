<template>
  <span class="schedule">
    <div v-if="hasAlt" class="alt-toggle">
      <label for="alt">Use Zap2it</label>
      <input id="alt" type="checkbox" v-model="showAlt"></a>
    </div>
    <table>
      <tr v-if="$parent.isNotFound">
        <td colspan="2" class="schedule-notfound">
          <b>There's nothing at this date!</b><br>
          Either you're trying to load an old entry
          <small>
            (and you should take a look at the <a target="_blank" rel="noopener" href="https://cnschedulearchive.tumblr.com/tagged/cartoon-network">CN Schedule Archive</a>)
          </small>
          or you're way too much in the future.
        </td>
      </tr>
      <tr v-if="isZap || showAlt">
        <td colspan="2" class="schedule-zap2it">
          This schedule was extracted from Zap2it.<br>
          Expect it to be wrong as changes <i>might</i> happen.
        </td>
      </tr>
      <tr v-if="$parent.isReady && blocks.length == 0 && !$parent.isNotFound">
        <td colspan="2" class="schedule-info">
          <b>This schedule is empty and is waiting for an update.</b><br>
          Please come later when CN has provided new data.
        </td>
      </tr>
      <schedule-el
        v-if="!showAlt || !hasAlt"
        v-for="block in blocks"
        :block="block"
        :key="block.timestamp">
      </schedule-el>
      <schedule-el
        v-if="hasAlt && showAlt"
        v-for="block in altBlocks"
        :block="block"
        :key="block.timestamp">
      </schedule-el>
    </table>
  </span>
</template>

<script>
import { getToday } from '../assets/dates.js'
var _ = require('lodash')

export default {
  name: 'schedule',
  data () {
    return {
      isZap: false,
      pageTitle: 'Loading...',
      hasAlt: false,
      showAlt: false,
      blocks: [],
      altBlocks: []
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
      var schedule = this.$parent.json

      if (d === 'undefined' || d === undefined) {
        // Redirect to today's date if route not set
        this.$router.replace({name: 'Schedule', params: {date: getToday()}})
      } else {
        // Change page title early for search engines and stuff
        this.pageTitle = d

        // Check if data is loaded
        if (schedule === undefined || !this.$parent.scheduleReloaded) {
          setTimeout(this.getSchedule, 1000)
          return
        }

        // Now we try to find that date
        if (d in schedule) {
          // If day found, let's load it
          this.blocks = this.orderBlocks(schedule[d]['schedule'])
          this.$parent.$data.blocks = this.$data.blocks
          this.isZap = (schedule[d]['source'] === 'Zap2it')
          this.$parent.$data.isNotFound = false

          this.hasAlt = ('alt' in schedule[d])
          if (this.hasAlt) {
            this.altBlocks = this.orderBlocks(schedule[d]['alt']['schedule'])
          }
        } else {
          // If day not found from the route
          // Check if it's a proper date
          if (d.match(/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/)) {
            // If it is, say we don't have it
            this.$parent.$data.isNotFound = true
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
