<template>
  <span class="schedule content">
    <tabs :options="tabSettings">
      <template v-if="!asOnly">
        <tab name="Official" v-if="schedule.cn">
          <div class="message warn" v-if="!schedule.cn">
            No data from Cartoon Network. Try with Zap2it or TVGuide!
          </div>
          <table v-else>
            <schedule-el
              v-for="block in schedule.cn"
              :key="block.timestamp"
              :block="block"
              :config="$parent.config">
            </schedule-el>
          </table>
        </tab>

        <tab name="Zap2it">
          <div class="message warn" v-if="!schedule.zap">
            No data from Zap2it. Try with TVGuide!
          </div>
          <table>
            <schedule-el
              v-for="block in schedule.zap"
              :key="block.timestamp"
              :block="block"
              :config="$parent.config">
            </schedule-el>
          </table>
        </tab>

        <tab name="TVGuide">
          <div class="message warn" v-if="!schedule.tvguide">
            No data from TVGuide. Try with Zap2it!
          </div>
          <table>
            <schedule-el
              v-for="block in schedule.tvguide"
              :key="block.timestamp"
              :block="block"
              :config="$parent.config">
            </schedule-el>
          </table>
        </tab>

        <tab v-if="$parent.config.showAS && schedule.as" name="[as]">
          <div class="message warn" v-if="!schedule.as">
            No data from [adult swim].
          </div>
          <table>
            <schedule-el
              v-for="block in schedule.as"
              :key="block.timestamp"
              :block="block"
              :config="$parent.config">
            </schedule-el>
          </table>
        </tab>

        <tab name="Stats">
          <schedule-stats v-if="stats" :day="stats" :fallback="$parent.config.fallback"></schedule-stats>
          <div class="message warn" v-else>
            No stats available.
          </div>
        </tab>
      </template>
      <template v-else>
        <tab name="[as]">
          <div class="message warn" v-if="!schedule.as">
            No data from Cartoon Network nor [adult swim].<br/>
            Looks like something went wrong or data was removed!
          </div>
          <div class="message warn" v-else-if="!$parent.config.showAS">
            No data available for Cartoon Network.<br/>
            Showing you the [adult swim] schedule.<br/>
            <small>
              Did you know you can now display the <em>[as]</em> tab in the settings?
            </small>
          </div>
          <table>
            <schedule-el
              v-for="block in schedule.as"
              :key="block.timestamp"
              :block="block"
              :config="$parent.config">
            </schedule-el>
          </table>
        </tab>
      </template>
    </tabs>
  </span>
</template>

<script>
import { parseDate } from '../assets/dates'
import ScheduleListEl from '@/components/ScheduleListEl'
import Stats from '@/components/Stats'

export default {
  name: 'schedule',
  components: {
    'schedule-el': ScheduleListEl,
    'schedule-stats': Stats
  },
  data () {
    return {
      schedule: {},
      stats: null,
      asOnly: false,
      pageTitle: 'Loading...',
      tabSettings: {
        useUrlFragment: false,
        disableStorage: true
      }
    }
  },
  metaInfo () {
    return {
      title: this.pageTitle
    }
  },
  methods: {
    getSchedule (route) {
      // Get date from route
      let d = route.params.date
      this.pageTitle = parseDate(d)

      if (!this.$parent.status.ready) {
        setTimeout(this.getSchedule, 1000)
        return
      }

      this.schedule = this.$parent.schedule.days[d]['schedule']

      if (!this.schedule.cn && !this.schedule.zap && !this.schedule.tvguide) {
        // No data from them
        this.asOnly = true
        return
      } else {
        this.asOnly = false
      }

      this.stats = this.$parent.schedule.days[d]['stats']
    }
  },
  watch: {
    // on route changes, load schedule
    '$route': 'getSchedule'
  },
  mounted () {
    this.getSchedule(this.$route)
  }
}
</script>

<style lang="scss" scoped>
@import '../assets/colors';

.schedule {
  .message {
    padding: 20px;
    text-align: center;

    &.warn {
      background-color: $cn-yellow;
      color: $black;
    }

    &.error {
      border: $red 2px solid;
    }
  }

  table {
    width: 100%;
    z-index: 20;
  }
}
</style>
