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
      class="day"
      v-for="(day, index) in $parent.json"
      v-if="index !== '_'">
      <div
        class="date"
        :style="getHeadBg(day.source)">
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
        v-for="show in day.schedule"
        :title="show.title + ' (' + show.time +')'"
        :style="getSlotStyle(show)">
        <span class="show">{{ show.show }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'grid',
  methods: {
    getSlotStyle (show) {
      return {
        'height': show.slots * 25 + 'px',
        'background-color': (show.time.search('00') > 1 || show.time.search('15') > 1) ? '#333' : '#3e3e3e'
      }
    },
    getHeadBg (source) {
      return {
        'background-color': (source === 'Zap2it') ? '#ffeb00' : '#00aeef'
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
  }
}
</script>
