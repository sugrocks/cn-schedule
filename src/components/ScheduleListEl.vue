<template>
  <tr :data-timestamp-start="block.timestamp" :data-timestamp-end="block.timestamp_end" :class="isOnAir">
    <td class="field-time">{{ block.time }}</td>
    <td>
      <span class="field-show">{{ block.show }}</span>
      <br>
      <span class="field-title">{{ block.title }}</span>
    </td>
  </tr>
</template>

<script>
export default {
  name: 'schedule-el',
  props: ['block'],
  computed: {
    isOnAir: function () {
      // Get current timestamp and check if current element is on air
      var tsCurr = parseInt(Date.now() / 1000, 10)
      return {
        'on-air': this.block.timestamp < tsCurr && tsCurr < this.block.timestamp_end
      }
    }
  }
}
</script>
