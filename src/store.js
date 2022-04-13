import { defineStore } from 'pinia'

export const useStore = defineStore('main', {
  state: () => {
    return {
      status: {
        error: false,
        offline: false,
        ready: false,
        update: false
      },
      config: {
        colors: false,
        listSize: 'normal',
        localTime: false,
        theme: 'dark',
        fallback: 'zap',
        showAS: true
      },
      schedule: {
        available: [],
        asOnly: [],
        days: {},
        selected: []
      }
    }
  },
  actions: {
    updateStatus (payload) {
      this.status = payload
    },
    updateConfig (payload) {
      this.config = payload
    },
    updateSchedule (payload) {
      this.schedule = payload
    }
  }
})
