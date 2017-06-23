<template>
  <tr>
    <router-link :to="day.id" active-class="selected" :class="day.source" tag="td">
      {{ day.text }}
    </router-link>
  </tr>
</template>

<script>
export default {
  name: 'day-el',
  props: ['day']
}
</script>
