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
        <label for="showAS">Show [adult swim] tab: </label>
        <input
          type="checkbox"
          id="showAS"
          @click="saveSettings"
          v-model="$parent.config.showAS">

        <br/>

        <label for="fallback">Main fallback source: </label>
        <select
          id="fallback"
          @change="saveSettings"
          v-model="$parent.config.fallback">
          <option value="zap">Zap2it</option>
          <option value="tvguide">TVGuide</option>
        </select>

        <br/>

        <label for="localTime">Convert times to your timezone: </label>
        <input
          type="checkbox"
          id="localTime"
          @click="saveSettings"
          v-model="$parent.config.localTime">

        <br/><br/>

        <label for="colors">Colorful grid: </label>
        <input
          type="checkbox"
          id="colors"
          @click="saveSettings"
          v-model="$parent.config.colors">

        <br/>

        <label for="theme">Theme: </label>
        <select
          id="theme"
          @change="saveSettings"
          v-model="$parent.config.theme">
          <option value="dark">Dark</option>
          <option value="light" disabled>Light</option>
        </select>

        <br/>

        <label for="listSize">List size: </label>
        <select
          id="listSize"
          @change="saveSettings"
          v-model="$parent.config.listSize">
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
        <a class="as" target="_blank" rel="noopener" href="https://swimpedia.net/oldsched/">
          better [adult swim] schedule</a>.<br/>
        Zap2it, TVGuide and [adult swim] data comes from their APIs.
      </p>

      <p>
        <small>
          Webapp version: {{ appEnv.release }}<br/>
          Want to know when a new day is available? <a class="discord" target="_blank" rel="noopener" href="https://discord.gg/Jr9Pnq9">Join our Discord.</a><br/>
          Love from CTOON ❤️ · 2017-2019 ·
          <a class="twitter" target="_blank" rel="noopener" href="https://twitter.com/CTOONnet">
            @CTOONnet</a>
        </small>
      </p>
    </div>
  </div>
</template>

<script>
import store from 'store/dist/store.modern'

export default {
  name: 'settings-about',
  data () {
    return {
      saved: false
    }
  },
  metaInfo () {
    return {
      title: 'Settings & About'
    }
  },
  methods: {
    saveSettings () {
      // Save your settings in your browser with store (add a delay to make sure model updates)
      setTimeout(_ => {
        store.set('appDisplayConfig', this.$parent.config)

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
        font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-weight: 900;
        padding: 0 5px;
        text-decoration: none;
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
