<template>
  <tr :class="displaySource">
    <router-link :to="day.id" tag="td" colspan="4">
      {{ day.text }}
    </router-link>
  </tr>
</template>

<script>
export default {
  name: 'day-el',
  props: ['day'],
  computed: {
    displaySource: function () {
      // Load data in this component
      return {
        'zap2it': (this.day.source === 'Zap2it'),
        'cn': (this.day.source === 'Cartoon Network'),
        'past': this.day.past
      }
    }
  }
}
</script>
