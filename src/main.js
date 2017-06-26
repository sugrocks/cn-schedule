// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import About from '@/components/About'
import Stats from '@/components/Stats'
import DayListEl from '@/components/DayListEl'
import ScheduleListEl from '@/components/ScheduleListEl'
import router from './Router'

Vue.config.productionTip = false

Vue.component('app-about', About)
Vue.component('schedule-stats', Stats)
Vue.component('day-el', DayListEl)
Vue.component('schedule-el', ScheduleListEl)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
