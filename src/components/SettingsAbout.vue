<template>
  <div class="settings-about content">
    <div class="settings">
      <h2>
        Settings
        <transition name="fade">
          <small v-if="saved">
            (Saved!)
          </small>
        </transition>
      </h2>

      <p>
        <label for="showAS">Show [adult swim]: </label>
        <input
          id="showAS"
          v-model="mainStore.config.showAS"
          type="checkbox"
          @click="saveSettings">

        <br/>

        <label for="fallback">Main fallback source: </label>
        <select
          id="fallback"
          v-model="mainStore.config.fallback"
          @change="saveSettings">
          <option value="zap">Zap2it</option>
          <option value="tvguide">TVGuide</option>
        </select>

        <br/>

        <label for="localTime">Convert times to your timezone: </label>
        <input
          id="localTime"
          v-model="mainStore.config.localTime"
          type="checkbox"
          @click="saveSettings">

        <br/><br/>

        <label for="colors">Show colors: </label>
        <input
          id="colors"
          v-model="mainStore.config.colors"
          type="checkbox"
          @click="saveSettings">

        <br/>

        <label for="theme">Theme: </label>
        <select
          id="theme"
          v-model="mainStore.config.theme"
          @change="saveSettings">
          <option value="dark">Dark</option>
          <option value="light" disabled>Light</option>
        </select>

        <br/>

        <label for="listSize">List size: </label>
        <select
          id="listSize"
          v-model="mainStore.config.listSize"
          @change="saveSettings">
          <option value="compact">Compact</option>
          <option value="normal">2-Lines</option>
          <option value="large">Large</option>
        </select>
      </p>
    </div>

    <div class="about">
      <h2>About</h2>

      <p>
        Inspired by
        <span class="as">better [adult swim] schedule</span>.<br/>
        Toonami-like font "<span class="tsoonami">Tsoonami</span>" by subatomicglue.com<br/>
        CN, Zap2it, TVGuide and [adult swim] data comes from their own APIs.
      </p>

      <p>
        <small>
          Love from CTOON ❤️ · 2017-2022
        </small>
      </p>
    </div>
  </div>
</template>

<script>
import { mapStores } from 'pinia'
import { useStore } from '@/store'
import store from 'store/dist/store.modern'

export default {
  name: 'settings-about',
  metaInfo () {
    return {
      title: 'Settings & About'
    }
  },
  data () {
    return {
      saved: false
    }
  },
  computed: {
    ...mapStores(useStore)
  },
  methods: {
    saveSettings () {
      // Save your settings in your browser with store (add a delay to make sure model updates)
      setTimeout(_ => {
        store.set('appDisplayConfig', this.mainStore.config)

        window.clearTimeout(this.saved)
        this.saved = setTimeout(_ => {
          this.saved = false
        }, 3000)
      }, 200)
    }
  }
}
</script>

<style lang="scss" scoped>
@import '../assets/colors';

.settings-about {
  background-color: $line-even;

  div {
    padding: 25px;

    &.settings {
      p {
        line-height: 1.5em;
      }

      small {
        color: $gray;
        font-family: Arial, Helvetica, sans-serif;
        font-size: .5em;
        font-style: italic;
      }
    }

    &.about {
      background-color: $cn-yellow;
      color: $black;

      .as {
        background-color: $black;
        color: $white;
        font-family: 'Helvetica Neue', Helvetica, 'Roboto', Arial, sans-serif;
        font-weight: 900;
        padding: 0 5px;
        text-decoration: none;
      }

      .tsoonami {
        font-family: 'tsoonami';
      }

      .discord,
      .twitter {
        color: $black;
      }
    }
  }
}

@media screen and (min-width: 675px) {
  .settings-about {
    margin-left: 200px;
  }
}
</style>
