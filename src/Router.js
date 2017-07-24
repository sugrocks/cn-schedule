// Vue and the router
import Vue from 'vue'
import Router from 'vue-router'
// Our base components
import Schedule from '@/components/Schedule'
import Grid from '@/components/Grid'

Vue.use(Router)

// Specify our routes and names
export default new Router({
  mode: 'history',
  linkActiveClass: 'selected',
  routes: [
    {
      path: '/',
      name: 'Index',
      component: Schedule
    },
    {
      path: '/grid',
      name: 'grid',
      component: Grid
    },
    {
      path: '/:date',
      name: 'Schedule',
      component: Schedule
    },
    {
      path: '/404',
      name: 'NotFound',
      redirect: function () {
        window.location.href = '/404.html'
      }
    },
    {
      path: '*',
      redirect: 'NotFound'
    }
  ]
})
