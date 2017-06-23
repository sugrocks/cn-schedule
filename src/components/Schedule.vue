<template>
  <div id="schedule">
    <schedule-stats></schedule-stats>

    <!-- Left column with dates -->
    <table class="dates">
      <day-el
        v-for="day in days"
        v-bind:day="day"
        v-bind:key="day.id">
      </day-el>
    </table>

    <!-- Main content with the schedule -->
    <table class="schedule">
      <tr v-if="isZap">
        <td colspan="3" class="schedule-zap2it">
          This schedule was extracted from Zap2it. Changes might happen.
        </td>
      </tr>
      <schedule-el
        v-for="block in blocks"
        v-bind:block="block"
        v-bind:key="block.id">
      </schedule-el>
    </table>
  </div>
</template>

<script>
export default {
  name: 'schedule',
  data () {
    return {
      isZap: false,
      days: [],
      blocks: []
    }
  },
  methods: {
    toggleAbout () {
      jQuery('.about').slideToggle()
    },
    toggleStats () {
      jQuery('.stats').slideToggle()
    },
    getToday () {
      var today = new Date()
      var d = today.getDate()
      var m = today.getMonth() + 1 // Because January is 0
      var y = today.getFullYear()

      if (d < 10) d = '0' + d
      if (m < 10) m = '0' + m

      return y + '-' + m + '-' + d
    },
    getSchedule () {
      var d = this.$route.params.date
      var schedule = JSON.parse(localStorage.getItem('schedule'))
      if (d === 'undefined' || d === undefined) {
        this.$router.replace({name: 'Schedule', params: {date: this.getToday()}})
      } else {
        this.$data.blocks = schedule[d]['schedule']
        this.$data.isZap = (schedule[d]['source'] === 'Zap2it' || schedule[d]['source'] === 'Screener')
      }
    },
    parseDate (date) {
      var dateSplit = date.split('-')
      var dobj = new Date(dateSplit[0], (dateSplit[1] - 1), dateSplit[2])
      return dobj.toLocaleDateString(
        'en-US',
        {
          weekday: 'short',
          month: 'long',
          day: 'numeric'
        }
      )
    }
  },
  watch: {
    // call again the method if the route changes
    '$route': 'getSchedule'
  },
  mounted () {
    var url = 'https://api.sug.rocks/cnschedule.json'
    var d = this
    jQuery.getJSON(url, function (result) {
      localStorage.setItem('schedule', JSON.stringify(result))
      for (var i in result) {
        // Skip json "meta"
        if (i === '_') continue

        d.$data.days.push({
          id: i,
          source: (result[i]['source'] === 'Screener') ? ' zap2it' : '',
          text: d.parseDate(i)
        })
      }
      d.getSchedule()
    })
    .fail(function () {
      jQuery('h1').append(' <small>(offline)</small>')
    })
  }
}
</script>
