<template>
  <!-- Left column with dates -->
  <table class="navbar">
    <tr v-if="mainStore.status.update">
      <td
        class="update"
        colspan="3"
        @click="reload">
        <svg xmlns="http://www.w3.org/2000/svg"
             width="24" height="24" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round"
             class="feather feather-zap">
          <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
        </svg>
        <b>APP UPDATE</b><br>
        Click here to reload.
      </td>
    </tr>

    <flat-pickr
      v-model="date"
      class="pickerInput"
      name="date"
      :config="config"
      @on-close="onDateSelect">
    </flat-pickr>

    <tr class="special">
      <td
        data-toggle
        title="Select Dates"
        colspan="1">
        <svg xmlns="http://www.w3.org/2000/svg"
             width="24" height="24" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round"
             class="feather feather-calendar">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
          <line x1="16" y1="2" x2="16" y2="6"></line>
          <line x1="8" y1="2" x2="8" y2="6"></line>
          <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
      </td>

      <router-link v-slot="{ isActive, navigate }" to="/grid" custom>
        <td title="Grid View" colspan="1" :class="{'selected': isActive}" @click="navigate">
          <svg xmlns="http://www.w3.org/2000/svg"
               width="24" height="24" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round"
               class="feather feather-grid">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
          </svg>
        </td>
      </router-link>

      <router-link v-slot="{ isActive, navigate }" to="/settings" custom>
        <td title="Settings & About" colspan="1" :class="{'selected': isActive}" @click="navigate">
          <svg xmlns="http://www.w3.org/2000/svg"
               width="24" height="24" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round"
               class="feather feather-settings">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
          </svg>
        </td>
      </router-link>
    </tr>

    <template
      v-for="day in this.mainStore.schedule.selected"
      :key="day">
      <tr v-if="!asOnly(day) || asOnly(day) && this.mainStore.config.showAS">
        <router-link v-slot="{ isActive, navigate }" :to="`/view/` + day" custom>
          <td colspan="3" :class="{'selected': isActive}" @click="navigate">
            <template v-if="asOnly(day)">[</template>{{ pd(day) }}<template v-if="asOnly(day)">]</template>
          </td>
        </router-link>
      </tr>
    </template>
  </table>
</template>

<script>
import { mapStores } from 'pinia'
import { useStore } from '@/store'
import { getDate, parseDate } from '../assets/dates'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import 'flatpickr/dist/themes/dark.css'

export default {
  name: 'NavBar',
  components: {
    flatPickr
  },
  data () {
    return {
      date: [],
      lastDate: [],
      config: {
        wrap: true,
        dateFormat: 'Y-m-d',
        mode: 'range',
        minDate: null,
        maxDate: null
      }
    }
  },
  computed: {
    ...mapStores(useStore)
  },
  mounted () {
    this.date = [
      this.mainStore.schedule.selected[0],
      this.mainStore.schedule.selected.slice(-1)[0]
    ]

    this.config.minDate = this.mainStore.schedule.available.slice(-1)[0]
    this.config.maxDate = this.mainStore.schedule.available[0]

    this.lastDate = this.date
  },
  methods: {
    onDateSelect (date) {
      const early = getDate(date[0])
      const late = getDate(date[1])

      if (this.lastDate[0] !== early || this.lastDate[1] !== late) {
        this.$parent.loadRange(
          early,
          late,
          true
        )
      }
    },
    reload () {
      if ('updateSW' in window) window.updateSW()
      else window.location.reload()
    },
    pd (ymd) {
      return parseDate(ymd)
    },
    asOnly (day) {
      return this.mainStore.schedule.asOnly.indexOf(day) > -1
    }
  }
}
</script>

<style lang="scss" scoped>
@import '../assets/colors';

.navbar {
  background-color: $dates-bg;
  color: $white;
  width: 100%;
  z-index: 10;

  .pickerInput {
    height: 50px;
    left: 10px;
    position: absolute;
    visibility: hidden;
    z-index: -999;
  }

  .special {
    text-align: center;
  }

  td {
    border-bottom: 1px solid $semi-transparent-white;
    cursor: pointer;
    height: 20px;
    padding: 15px;

    &:hover {
      background-color: $black;
    }

    &.selected {
      color: $cn-blue;

      svg * {
        stroke: $cn-blue;
      }
    }

    &.update {
      background-color: $red;
    }

    svg {
      height: 1em;
      vertical-align: sub;
      width: 1em;
    }
  }
}

// Make a two-column layout starting 675px in width
@media screen and (min-width: 675px) {
  .navbar {
    float: left;
    position: sticky;
    top: 0;
    width: 200px;

    .pickerInput {
      height: 30px;
    }

    td {
      padding: 5px 10px;
    }
  }
}
</style>
