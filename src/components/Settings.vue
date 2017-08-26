<template>
  <transition name="slide">
    <div class="settings modal">
      <label for="cbxPast">Show past dates: </label>
      <input
        type="checkbox"
        id="cbxPast"
        @click="$parent.saveSettings"
        v-model="$parent.config.showPast">
      <br>
      <label for="cbxZap">Show entries from Zap2it: </label>
      <input
        type="checkbox"
        id="cbxZap"
        @click="$parent.saveSettings"
        v-model="$parent.config.showZap">
      <br>
      <label for="cbxGlobal">Show global stats: </label>
      <input
        type="checkbox"
        id="cbxGlobal"
        @click="$parent.saveSettings"
        v-model="$parent.config.showGlobal">
      <br>
      <small><a @click="$parent.showSettings = !$parent.showSettings">(close)</a></small>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'settings'
}
</script>
