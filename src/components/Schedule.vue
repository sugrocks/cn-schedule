<template>
  <div class="schedule content">
    <tabs cache-lifetime="0" :options="tabSettings">
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
              :config="mainStore.config">
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
              :config="mainStore.config">
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
              :config="mainStore.config">
            </schedule-el>
          </table>
        </tab>

        <tab v-if="mainStore.config.showAS" name="[as]">
          <div class="message warn" v-if="!schedule.as">
            No data from [adult swim].
          </div>
          <table>
            <schedule-el
              v-for="block in schedule.as"
              :key="block.timestamp"
              :block="block"
              :config="mainStore.config">
            </schedule-el>
          </table>
        </tab>

        <tab name="Stats">
          <schedule-stats v-if="stats" :day="stats" :fallback="mainStore.config.fallback"></schedule-stats>
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
          <div class="message warn" v-else-if="!mainStore.config.showAS">
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
              :config="mainStore.config">
            </schedule-el>
          </table>
        </tab>
      </template>
    </tabs>
  </div>
</template>

<script>
import { mapStores } from 'pinia'
import { useStore } from '@/store'
import { parseDate } from '../assets/dates'
import { Tabs, Tab } from 'vue3-tabs-component'
import ScheduleListEl from '@/components/ScheduleListEl'
import Stats from '@/components/Stats'

export default {
  name: 'schedule',
  components: {
    tabs: Tabs,
    tab: Tab,
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
  computed: {
    ...mapStores(useStore)
  },
  watch: {
    // on route changes, load schedule
    $route: 'getSchedule'
  },
  mounted () {
    this.getSchedule(this.$route)
  },
  methods: {
    getSchedule (route) {
      // Get date from route
      const d = route.params.date
      if (d === undefined) return

      this.pageTitle = parseDate(d)

      if (!this.mainStore.status.ready) {
        setTimeout(this.getSchedule, 1000)
        return
      }

      this.schedule = this.mainStore.schedule.days[d].schedule

      if (!this.schedule.cn && !this.schedule.zap && !this.schedule.tvguide) {
        // No data from them
        this.asOnly = true
        return
      } else {
        this.asOnly = false
      }

      this.stats = this.mainStore.schedule.days[d].stats
    }
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
