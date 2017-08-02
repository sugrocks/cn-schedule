// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import VueResource from 'vue-resource'
import VueAnalytics from 'vue-analytics'
// Our components
import About from '@/components/About'
import Settings from '@/components/Settings'
import Stats from '@/components/Stats'
import DayListEl from '@/components/DayListEl'
import ScheduleListEl from '@/components/ScheduleListEl'
// The router full code
import router from './Router'

// Stop posting tips
Vue.config.productionTip = false

// Load components
Vue.component('app-about', About)
Vue.component('app-settings', Settings)
Vue.component('schedule-stats', Stats)
Vue.component('day-el', DayListEl)
Vue.component('schedule-el', ScheduleListEl)

// Use that nice plugin
Vue.use(VueResource)
Vue.use(VueAnalytics, {
  id: 'UA-103935709-2',
  router
})

/* eslint-disable no-new */
// Create vue app
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
