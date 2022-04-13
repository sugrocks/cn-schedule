<template>
  <div class="loading">
    <div class="loading__container">
      <template v-if="!mainStore.status.error">
        <h3>
          <svg xmlns="http://www.w3.org/2000/svg"
               width="24" height="24" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round"
               class="feather feather-download-cloud">
            <polyline points="8 17 12 21 16 17"></polyline>
            <line x1="12" y1="12" x2="12" y2="21"></line>
            <path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path>
          </svg>
          Loading...
        </h3>
        <loading-spinner></loading-spinner>
      </template>
      <template v-else>
        <h3>
          Something went wrong...
        </h3>
        <p>
          We couldn't fetch the data, try again later.
        </p>
        <code><pre>{{ mainStore.status.error }}</pre></code>
      </template>
    </div>
  </div>
</template>

<script>
import { mapStores } from 'pinia'
import { useStore } from '@/store'
import LoadingSpinner from '@/components/LoadingSpinner'
export default {
  name: 'loading',
  components: {
    'loading-spinner': LoadingSpinner
  },
  computed: {
    ...mapStores(useStore)
  }
}
</script>

<style lang="scss" scoped>
.loading {
  align-items: center;
  background-color: gray;
  display: flex;
  height: 100vh;
  justify-content: center;
  left: 0;
  position: fixed;
  text-align: center;
  top: 0;
  width: 100vw;

  h3 {
    margin: 0;

    svg {
      vertical-align: text-bottom;
    }
  }
}
</style>
