<template>
  <tr
    :data-timestamp-start="block.timestamp"
    :data-timestamp-end="block.timestamp_end"
    :class="lineStyle">
    <td class="field-time">
      {{ getTime(block.timestamp, block.time) }}
      <template v-if="config.localTime && config.listSize === 'large'">
        - {{ getTime(block.timestamp_end) }}
      </template>
      <span class="on-air-text">
        &nbsp;[ON AIR]
      </span>
    </td>
    <td class="field-data">
      <span class="field-show">{{ block.show }}</span>
      <br v-if="config.listSize !== 'compact'"/>
      <span class="field-title">{{ block.title }}</span>
    </td>
  </tr>
</template>

<script>
import { DateTime } from 'luxon'

export default {
  name: 'schedule-el',
  props: ['block', 'config'],
  methods: {
    getTime (ts, str) {
      if (!this.config.localTime) return str

      let dt = DateTime.fromMillis(ts * 1000)
      return dt.toLocaleString(DateTime.TIME_SIMPLE)
    }
  },
  computed: {
    lineStyle: function () {
      // Get current timestamp and check if current element is on air
      let tsCurr = parseInt(Date.now() / 1000, 10)

      return {
        'compact': this.config.listSize === 'compact',
        'large': this.config.listSize === 'large',
        'on-air': this.block.timestamp < tsCurr && tsCurr < this.block.timestamp_end
      }
    }
  }
}
</script>

<style lang="scss" scoped>
@import '../assets/colors';

tr {
  .field-time {
    color: $white;
    min-width: 80px;
    text-align: right;
    width: 80px;

    .on-air-text {
      color: $cn-yellow;
      display: none;
      font-weight: bold;
    }
  }

  .field-data {
    .field-show {
      color: $cn-blue;
      font-weight: bold;
    }

    .field-title {
      color: $white;
      font-style: italic;
    }
  }

  &.compact {
    white-space: nowrap;

    .field-data {
      max-width: 0;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  }

  &.large {
    .field-time,
    .field-data {
      display: block;
      max-width: initial;
      min-width: initial;
      width: initial;
    }

    .field-time {
      margin-top: 10px;
      text-align: left;
    }

    .field-data {
      margin-bottom: 10px;
      padding-left: 20px;
    }

    &.on-air {
      .on-air-text {
        display: inline;
      }
    }
  }

  &:nth-child(even) {
    background-color: $line-even;
  }

  &:nth-child(odd) {
    background-color: $line-odd;
  }

  &.on-air {
    background-color: $dark-red !important;
  }
}
</style>
