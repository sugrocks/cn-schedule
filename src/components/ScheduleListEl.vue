<template>
  <tr :data-timestamp-start="block.timestamp" :data-timestamp-end="block.timestamp_end">
    <td class="field-time">{{ block.time }}</td>
    <td>
      <span class="field-show">{{ block.show }}</span>
      <br>
      <span class="field-title">{{ block.title }}</span>
    </td>
    <td class="field-rating">{{ block.rating }}</td>
  </tr>
</template>

<script>
export default {
  name: 'schedule-el',
  props: ['block']
}
</script>
