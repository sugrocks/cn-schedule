<template>
  <transition name="slide">
    <div class="about modal">
      Inspired by <a class="as" target="_blank" rel="noopener" href="http://adultswim.x10.mx/">better [adult swim] schedule</a>.<br>
      Date and time from the eastern timezone.<br>
      <small>
        Love from CTOON ❤️ -
        <a href="https://www.tipeee.com/ctoon">Want to tip us?</a><br>
        <a @click="$parent.showAbout = !$parent.showAbout">(close)</a>
      </small>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'about'
}
</script>
