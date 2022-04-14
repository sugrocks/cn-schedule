<template>
  <div id="app" :class="[mainStore.config.theme, (mainStore.status.offline ? 'offline' : null)]">
    <div>
      <router-link v-slot="{ navigate }" to="/" custom>
        <h1 :title="appTitle" @click="navigate">{{ appTitle }}</h1>
      </router-link>
    </div>

    <transition name="fade">
      <app-loading v-if="!mainStore.status.ready"></app-loading>
    </transition>
    <template v-if="mainStore.status.ready">
      <app-navbar></app-navbar>
      <router-view></router-view>
    </template>
  </div>
</template>

<script>
import { mapStores } from 'pinia'
import { useStore } from '@/store'
import { getDate } from './assets/dates.js'
import store from 'store/dist/store.modern'
import Loading from '@/components/Loading.vue'
import NavBar from '@/components/NavBar.vue'

export default {
  name: 'app',
  components: {
    'app-loading': Loading,
    'app-navbar': NavBar
  },
  data () {
    return {
      appTitle: 'CN Schedule',
      apiBase: 'https://api.ctoon.network/schedule/v2'
    }
  },
  computed: {
    ...mapStores(useStore)
  },
  async mounted () {
    if ('serviceWorker' in navigator) {
      window.addEventListener('updated', e => {
        this.mainStore.status.update = true
      })
    }

    // Get config from store
    const conf = store.get('appDisplayConfig')
    if (conf !== undefined) {
      // Transition
      if (conf.fallback === undefined) conf.fallback = 'zap'
      if (conf.showAS === undefined) conf.showAS = false

      this.mainStore.config = conf
    }

    await this.$router.isReady()

    // Test if online
    if (navigator.onLine) {
      // If we start the app with a selected date
      const sel = this.$route.params.date

      // Fetch our API
      fetch(this.apiBase + '/days')
        .then(response => {
          // Directly return the JSON to our next promise
          return response.json()
        }, err => {
          throw Error(err)
        })
        .then(data => {
          const offset = -1
          let early = data.indexOf(getDate(null, offset))
          let late = 0
          const firstRange = data.slice(late, early)

          if (sel) {
            if (data.indexOf(sel) === -1) {
              this.$router.push({ name: 'NotFound', params: { lastPath: this.$route.path } })
            } else if (firstRange.indexOf(sel) === -1) {
              late = data.indexOf(sel) - early - offset
              early = data.indexOf(sel) - offset

              if (early >= data.length) early = data.length - 1
            }
          }

          this.mainStore.schedule.available = data
          this.loadRange(data[early], data[late])
        })
        .catch(err => {
          this.mainStore.status.error = '' + err
        })
    } else {
      console.warn('You are offline.')
      this.mainStore.status.offline = true
      this.appTitle += ' (offline)'

      const cached = store.get('cachedSchedule')
      if (cached === undefined) {
        this.mainStore.status.error = 'We asked the browser, it says you\'re offline.\n Also, no cached schedule was found.'
      } else {
        this.loadRange()
      }
    }
  },
  methods: {
    loadRange (from, to) {
      this.mainStore.status.ready = false

      if (from !== undefined) {
        fetch(this.apiBase + '/range/' + from + '/' + to)
          .then(response => {
            // Directly return the JSON to our next promise
            return response.json()
          }, err => {
            console.error(err)
          })
          .then(data => {
            this.mainStore.schedule.days = data
            this.mainStore.schedule.selected = Object.keys(data)

            try {
              const cache = {}
              Object.keys(data).forEach(i => {
                cache[i] = data[i]
                const s = data[i].schedule

                if (!s.cn && !s.tvguide && !s.zap) {
                  if (s.as) this.mainStore.schedule.asOnly.push(data[i].date)
                }
              })
              store.set('cachedSchedule', cache)
            } catch (err) {
              console.log('Couldn\'t save to localstorage', err)
            }

            // If the current route isn't in the selection, change path
            if (this.$route.name === 'Schedule' && this.mainStore.schedule.selected.indexOf(this.$route.params.date) === -1) {
              this.$router.push({ name: 'Schedule', params: { date: from } })
            }

            setTimeout(_ => {
              this.mainStore.status.ready = true
            }, 1000)
          })
      } else {
        // load everything we have when offline
        this.mainStore.schedule.days = store.get('cachedSchedule')
        this.mainStore.schedule.selected = Object.keys(this.mainStore.schedule.days)

        setTimeout(_ => {
          this.mainStore.status.ready = true
        }, 1000)
      }
    }
  }
}
</script>

<style lang="scss">
@import './assets/colors';

// Basic stuff
@font-face {
  font-family: 'cnbold';
  font-style: normal;
  font-weight: normal;
  src: url('./assets/fonts/cn_bold.woff') format('woff'), url('./assets/fonts/cn_bold.ttf') format('ttf'), url('./assets/fonts/cn_bold.otf') format('otf'), url('./assets/fonts/cn_bold.eot'), url('./assets/fonts/cn_bold.eot?#iefix') format('embedded-opentype');
}

@font-face {
  font-family: 'tsoonami';
  src: url('./assets/fonts/subatomic.tsoonami.ttf');
}

body {
  background-color: $black;
  color: $white;
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
}

// Titles
h1,
h2 {
  font-family: 'cnbold', sans-serif;
  font-weight: 900;
  margin: 0;
}

h1 {
  color: $cn-pink;
  cursor: pointer;
  display: inline-block;
  font-size: 28px;
  height: 38px;
  padding: 5px 10px 0;
  z-index: 5;
}

h2 {
  margin-bottom: .5em;
}

// Transitions
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s
}

.fade-enter, .fade-leave-to {
  opacity: 0
}

// Offline
.offline {
  td[data-toggle] {
    opacity: 0.2;
    pointer-events: none;
  }
}

// Default <table> styles
table {
  border-collapse: collapse;
  border-spacing: 2px;

  th,
  td {
    line-height: 1.2rem;
    padding: 5px 10px;
    vertical-align: middle;
  }

  th {
    font-weight: bold;
    text-align: center;
  }
}

// Have a minimum height to our pages, nicer for mobile too
.content:not(.home) {
  min-height: calc(100vh - 45px);
}

// Tabs
.tabs-component {
  .tabs-component-tabs {
    margin: 0;
    padding: 0;
    border-bottom: 1px solid $black;
    border-top: 1px solid $black;

    .tabs-component-tab {
      background-color: $gray;
      color: $black;
      font-size: 14px;
      font-weight: 600;
      list-style: none;
      margin: 0;

      &.is-active {
        background-color: $dark-red;
        color: $white;

        .tabs-component-tab-a[href="#stats"]::before {
          background-color: #fff;
        }
      }

      .tabs-component-tab-a {
        align-items: center;
        color: inherit;
        display: flex;
        outline: 0;
        padding: .75em 1em;
        text-decoration: none;

        &::before {
          background-repeat: no-repeat;
          background-size: contain;
          content: '';
          height: 16px;
          margin-right: 5px;
          width: 16px;
        }

        &[href="#official"]::before {
          background-image: url('./assets/cn.png');
        }

        &[href="#zap2it"]::before {
          background-image: url('./assets/zap2it.png');
        }

        &[href="#tvguide"]::before {
          background-image: url('./assets/tvguide.png');
        }

        &[href="#[as]"]::before {
          background-image: url('./assets/as.png');
        }

        &[href="#stats"]::before {
          -webkit-mask-image: url('./assets/pie-chart.svg');
          -webkit-mask-size: contain;
          mask-image: url('./assets/pie-chart.svg') contain;
          mask-size: contain;
          background-color: #000;
        }
      }
    }
  }

  .tabs-component-panel {
    background-color: $line-even;
  }
}

// If the browser width >= 675px
@media screen and (min-width: 675px) {
  .tabs-component {
    .tabs-component-tabs {
      border-bottom: 0;
      border-top: 0;
      display: flex;
      flex-direction: row;

      .tabs-component-tab {
        border-bottom: 3px solid $black;
        flex-basis: 0;
        flex-grow: 1;

        &.is-active {
          border-bottom: 3px solid $dark-red;
        }
      }
    }

    .tabs-component-panel {
      margin-left: 200px;
    }
  }
}
</style>
