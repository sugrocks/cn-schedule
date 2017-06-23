<template>
  <!-- The "stats" block, big -->
  <div class="stats" style="display: none;">
    <span class="stats-text day">No statistics available for the moment.</span>
    <br>
    <table class="top-shows day">
      <thead>
        <tr>
          <th>Show</th>
          <th colspan="2">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
    <br>
    <span class="stats-text all">No statistics available for the moment.</span>
    <br>
    <table class="top-shows all">
      <thead>
        <tr>
          <th>Show</th>
          <th colspan="2">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
    <br>
    <small><a onclick="toggleStats()">(close)</a></small>
  </div>
</template>

<script>
export default {
  name: 'stats'
}
</script>
