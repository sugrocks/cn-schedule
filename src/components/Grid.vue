<template>
  <div class="grid content">
    <div class="time-table">
      <div class="time-head">
        Times
      </div>
      <div
        v-for="time in times"
        :key="time"
        class="time-line">
        <span class="time">{{ time }}</span>
      </div>
    </div>
    <div
      v-for="(day, index) in mainStore.schedule.days"
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
        v-for="(show, sIndex) in getSchedule(day)"
        :key="sIndex"
        :title="show.title + ' (' + getTime(show.timestamp, show.time) +')'"
        :style="getSlotStyle(show, sIndex)"
        class="slot">
        <span class="show">
          {{
            (show.show === 'SPECIAL') ? show.title + ' (SPECIAL)' :
            (show.show === 'MOVIE') ? show.title + ' (MOVIE)' : show.show
          }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import { mapStores } from 'pinia'
import { useStore } from '@/store'
import { getDate, parseDate } from '../assets/dates'
import { DateTime } from 'luxon'

export default {
  name: 'grid',
  data () {
    return {
      times: []
    }
  },
  computed: {
    ...mapStores(useStore)
  },
  mounted () {
    document.title = 'Grid | CN Schedule'
    this.times = []
    const max = this.mainStore.config.showAS ? 24 : 14

    for (let i = 0; i < max; i += 0.5) {
      let dt = DateTime.fromObject({
        hour: Math.floor((i + 6) % 24),
        minute: (i % 1 === 0 ? 0 : 30)
      }, {
        zone: 'America/New_York'
      })
      if (this.mainStore.config.localTime) dt = dt.setZone('local')
      this.times.push(
        dt.toLocaleString(DateTime.TIME_SIMPLE)
      )
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

      if (this.mainStore.config.colors && show.colors) {
        colorPlz = show.colors.foreground
        bgPlz = show.colors.background
      } else {
        bgPlz = (show.time.search(':00') > 0 || show.time.search(':15') > 0) ? '#333' : '#3e3e3e'
      }

      if (!show.minutes) {
        show.minutes = (show.timestamp_end - show.timestamp) / 60
      }

      let toonami = {}
      if (colorPlz === '#17b7dd' && bgPlz === '#1b0120') {
        toonami = {
          'font-family': 'tsoonami',
          'font-weight': '300'
        }
      }

      return {
        color: colorPlz,
        'background-color': bgPlz,
        height: show.minutes * 2 + 'px',
        'margin-top': marginTopPlz + 'px',
        'box-shadow': 'inset 0 1px #000',
        ...toonami
      }
    },
    displaySource (day, index) {
      day = day.schedule
      // Add classes to specify source and if it's an old entry
      return {
        hidden: this.mainStore.schedule.asOnly.indexOf(index) > -1,
        zap2it: (day.cn === null && day.zap && (this.mainStore.config.fallback === 'zap' || !day.tvguide)),
        tvguide: (day.cn === null && day.tvguide && (this.mainStore.config.fallback === 'tvguide' || !day.zap)),
        cn: (day.cn),
        past: (getDate().replace('-', '') > index.replace('-', '')),
        day: true
      }
    },
    getSchedule (day) {
      const sAs = this.mainStore.config.showAS
      day = day.schedule

      if (day.cn) return (sAs && day.as ? day.cn.concat(day.as) : day.cn)
      if (day.zap && (this.mainStore.config.fallback === 'zap' || !day.tvguide)) return (sAs && day.as ? day.zap.concat(day.as) : day.zap)
      if (day.tvguide) return (sAs && day.as ? day.tvguide.concat(day.as) : day.tvguide)
      return (sAs && day.as ? day.other.concat(day.as) : day.other)
    },
    getTime (ts, str) {
      if (!this.mainStore.config.localTime) return str

      const dt = DateTime.fromMillis(ts * 1000)
      return dt.toLocaleString(DateTime.TIME_SIMPLE)
    },
    pd (date) {
      return parseDate(date)
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
    position: sticky;
    vertical-align: top;
    z-index: 26;

    .time-line {
      // Vertical center alignment
      align-items: center;
      box-shadow: inset 0 0 0 1px $black;
      display: flex;
      height: 60px;
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
      font-weight: 600;
      // Horizontal center alignment
      justify-content: center;
      overflow: hidden;
      text-align: center;

      .show {
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
