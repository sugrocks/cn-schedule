<template>
  <VuePerfectScrollbar class="grid content">
    <div class="time-table">
      <div class="time-head">
        Times
      </div>
      <div
        class="time-line"
        v-for="time in times"
        :key="time">
        <span class="time">{{ time }}</span>
      </div>
    </div>
    <div
      v-for="(day, index) in $parent.schedule.days"
      :key="index"
      :class="displaySource(day, index)">
      <div class="date">
        {{ pd(index) }}
        <span class="zapSrc">
          (Zap2it)
        </span>
        <span class="tvguideSrc">
          (TVGuide)
        </span>
      </div>
      <div
        class="slot"
        v-for="(show, sIndex) in getSchedule(day)"
        :key="sIndex"
        :title="show.title + ' (' + getTime(show.timestamp, show.time) +')'"
        :style="getSlotStyle(show, sIndex)">
        <span class="show">{{
            (show.show === 'SPECIAL') ? show.title + ' (SPECIAL)' :
              (show.show === 'MOVIE') ? show.title + ' (MOVIE)' : show.show
          }}</span>
      </div>
    </div>
  </VuePerfectScrollbar>
</template>

<script>
import { getDate, parseDate } from '../assets/dates'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import ColorHash from 'color-hash'
import { DateTime } from 'luxon'

export default {
  name: 'grid',
  components: {
    VuePerfectScrollbar
  },
  metaInfo () {
    return {
      title: 'Grid'
    }
  },
  methods: {
    getSlotStyle (show, index) {
      // Get alternative color background every half hour
      // And set height of slots based on their length
      // Also add margin top if somehow day starts later than 6:00
      let colorPlz = '#fff'
      let bgPlz = ''
      let marginTopPlz = 0

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

      if (this.$parent.config.colors && show.colors) {
        colorPlz = show.colors.foreground
        bgPlz = show.colors.background
      } else if (this.$parent.config.colors && (show.show !== 'SPECIAL' && show.show !== 'MOVIE')) {
        const li = (show.show.length % 10) / 10
        const colorHash = new ColorHash({ lightness: li })
        if (li >= 0.8) colorPlz = '#000'
        bgPlz = colorHash.hex(show.show)
      } else {
        bgPlz = (show.time.search(':00') > 0 || show.time.search(':15') > 0) ? '#333' : '#3e3e3e'
      }

      if (!show.minutes) {
        show.minutes = (show.timestamp_end - show.timestamp) / 60
      }

      return {
        color: colorPlz,
        'background-color': bgPlz,
        height: show.minutes / 0.6 + 'px',
        'margin-top': marginTopPlz + 'px',
        'box-shadow': 'inset 0 1px #000'
      }
    },
    displaySource (day, index) {
      day = day.schedule
      // Add classes to specify source and if it's an old entry
      return {
        hidden: this.$parent.schedule.asOnly.indexOf(index) > -1,
        zap2it: (day.cn === null && day.zap && (this.$parent.config.fallback === 'zap' || !day.tvguide)),
        tvguide: (day.cn === null && day.tvguide && (this.$parent.config.fallback === 'tvguide' || !day.zap)),
        cn: (day.cn),
        past: (getDate().replace('-', '') > index.replace('-', '')),
        day: true
      }
    },
    getSchedule (day) {
      day = day.schedule
      if (day.cn) return day.cn
      if (day.zap && (this.$parent.config.fallback === 'zap' || !day.tvguide)) return day.zap
      if (day.tvguide) return day.tvguide
      return day.other
    },
    getTime (ts, str) {
      if (!this.$parent.config.localTime) return str

      const dt = DateTime.fromMillis(ts * 1000)
      return dt.toLocaleString(DateTime.TIME_SIMPLE)
    },
    pd (date) {
      return parseDate(date)
    }
  },
  mounted () {
    this.times = []

    for (let i = 6; i < 21; i += 0.5) {
      let dt = DateTime.fromObject({
        hour: Math.floor(i),
        minute: (i % 1 === 0 ? 0 : 30),
        zone: 'America/New_York'
      })
      if (this.$parent.config.localTime) dt = dt.setZone('local')
      this.times.push(
        dt.toLocaleString(DateTime.TIME_SIMPLE)
      )
    }
  },
  data () {
    return {
      times: []
    }
  }
}
</script>

<style lang="scss" scoped>
@import '../assets/colors';

.grid {
  overflow-x: scroll;
  white-space: nowrap;
  width: 100%;
  z-index: 20;

  .time-head,
  .date {
    background-color: $cn-pink;
    color: $black;
    font-style: italic;
    font-weight: bold;
    padding: 5px 0;
    position: sticky;
    text-align: center;
    top: 0;
    z-index: 23;

    span {
      display: none;
    }
  }

  .zap2it .date {
    background-color: $zap2it;

    .zapSrc {
      display: inline;
    }
  }

  .tvguide .date {
    color: $white;
    background-color: $tvguide;

    .tvguideSrc {
      display: inline;
    }
  }

  .cn .date {
    background-color: $cn-blue;
  }

  .past .date {
    background-color: $cn-yellow !important;
  }

  .time-table {
    display: inline-block;
    font-size: 10px;
    left: 0;
    margin-right: -3px;
    padding: 0 1px 5px 0;
    position: sticky;
    vertical-align: top;
    z-index: 26;

    .time-line {
      // Vertical center alignment
      align-items: center;
      box-shadow: inset 0 0 0 1px $black;
      display: flex;
      height: 50px;
      // Horizontal center alignment
      justify-content: center;
      padding: 0 5px;

      &:nth-child(even) {
        background-color: $line-even;
      }

      &:nth-child(odd) {
        background-color: $line-odd;
      }
    }
  }

  .day {
    display: inline-block;
    font-size: 10px;
    padding: 0 1px;
    vertical-align: top;
    width: 150px;

    &.hidden {
      display: none;
    }

    .slot {
      // Vertical center alignment
      align-items: center;
      box-shadow: inset 0 0 0 1px $black;
      display: flex;
      // Horizontal center alignment
      justify-content: center;
      overflow: hidden;
      text-align: center;

      .show {
        font-weight: 600;
        overflow: hidden;
        white-space: normal;
        word-break: break-word;
      }
    }
  }
}

@media screen and (min-width: 675px) {
  .grid {
    height: calc(100vh - 45px);
    overflow-y: scroll;
    width: calc(100% - 200px);
  }
}
</style>
