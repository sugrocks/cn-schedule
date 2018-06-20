<template>
  <div class="notfound content">
    <h3>Not Found</h3>
    <p>
      Woa there, this page doesn't exists!
      <template v-if="lastPath">
        <br/>You tried to load <code>{{ lastPath }}</code>, check the URL again.
      </template>
    </p>
  </div>
</template>

<script>
export default {
  name: 'NotFound',
  data () {
    return {
      lastPath: this.$route.params.lastPath
    }
  },
  metaInfo () {
    return {
      title: 'Page not found'
    }
  }
}
</script>

<style lang="scss" scoped>
@import '../assets/colors';

.notfound {
  background-color: $line-odd;
  border: $red 2px solid;
  padding: 25px;
}

@media screen and (min-width: 675px) {
  .notfound {
    margin-left: 200px;
  }
}
</style>
