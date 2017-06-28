<template>
  <transition name="slide">
    <div class="about modal">
      Inspired by <a class="as" href="http://adultswim.x10.mx/">better [adult swim] schedule</i></a>.<br>
      Date and time from the eastern timezone.<br>
      <small>Love from /sug/.rocks ❤️ - <a v-on:click="$parent.showAbout = !$parent.showAbout">(close)</a></small>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'about'
}
</script>
