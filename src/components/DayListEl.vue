<template>
  <tr :class="displaySource">
    <router-link :to="day.id" tag="td" colspan="3">
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
      return {
        'zap2it': (this.day.source === 'zap2it'),
        'cn': (this.day.source === 'cn'),
        'past': this.day.past
      }
    }
  }
}
</script>
