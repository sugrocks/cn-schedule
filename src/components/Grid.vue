<template>
  <div class="grid">
    <div class="time-table">
      <div class="time-head">
        Times
      </div>
      <div class="time-line" v-for="time in times">
        <span class="time">{{ time }}</span>
      </div>
    </div>
    <div
      v-for="(day, index) in $parent.json"
      :class="displaySource(day, index)"
      v-if="index !== '_'">
      <div
        class="date">
        {{ index }}
        <span v-if="day.source === 'Zap2it'">
          (Zap2it)
        </span>
      </div>
      <div
        class="slot"
        v-if="day.schedule.length === 0"
        style="height: 1400px; background-color: #ffeb00;">
        <span class="show">Empty<br>Waiting for updates.</span>
      </div>
      <div
        class="slot"
        v-for="(show, sIndex) in day.schedule"
        :title="show.title + ' (' + show.time +')'"
        :style="getSlotStyle(show, sIndex)">
        <span class="show">{{
            (show.show === 'SPECIAL') ? show.title + ' (SPECIAL)' :
              (show.show === 'MOVIE') ? show.title + ' (MOVIE)' : show.show
          }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import { getToday } from '../assets/dates.js'
export default {
  name: 'grid',
  metaInfo () {
    return {
      title: 'Grid',
      meta: [ // Metadata for social media and stuff
        { name: 'twitter:title', content: 'Grid | CN Schedule' },
        { name: 'og:title', content: 'Grid | CN Schedule' },
        { name: 'og:url', content: 'https://cn.sug.rocks/grid' }
      ]
    }
  },
  methods: {
    getSlotStyle (show, index) {
      // Get alternative color background every half hour
      // And set height of slots based on their length
      // Also add margin top if somehow day starts later than 6:00
      var marginTopPlz = 0
      if (index === 0) {
        if (show.time === '6:15 am') {
          marginTopPlz = 25
        } else if (show.time === '6:30 am') {
          marginTopPlz = 50
        } else if (show.time === '6:45 am') {
          marginTopPlz = 75
        } else if (show.time === '7:00 am') {
          marginTopPlz = 100
        }
      }

      return {
        'background-color': (show.time.search(':00') > 0 || show.time.search(':15') > 0) ? '#333' : '#3e3e3e',
        'height': show.slots * 25 + 'px',
        'margin-top': marginTopPlz + 'px'
      }
    },
    displaySource (day, index) {
      // Add classes to specify source and if it's an old entry
      return {
        'zap2it': (day.source === 'Zap2it'),
        'cn': (day.source === 'Cartoon Network'),
        'past': (getToday().replace('-', '') > index.replace('-', '')),
        'day': true
      }
    }
  },
  data () {
    return {
      times: [
        '6:00 am', '6:30 am',
        '7:00 am', '7:30 am',
        '8:00 am', '8:30 am',
        '9:00 am', '9:30 am',
        '10:00 am', '10:30 am',
        '11:00 am', '11:30 am',
        '12:00 pm', '12:30 pm',
        '1:00 pm', '1:30 pm',
        '2:00 pm', '2:30 pm',
        '3:00 pm', '3:30 pm',
        '4:00 pm', '4:30 pm',
        '5:00 pm', '5:30 pm',
        '6:00 pm', '6:30 pm',
        '7:00 pm', '7:30 pm'
      ]
    }
  },
  mounted () {
    this.$parent.isReady = true
  }
}
</script>
