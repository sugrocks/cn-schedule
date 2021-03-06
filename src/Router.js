// Vue stuff
import Vue from 'vue'
import Router from 'vue-router'
import Meta from 'vue-meta'
import SmoothScroll from 'smoothscroll'
// Our components
import Home from '@/components/Home'
import Grid from '@/components/Grid'
import SettingsAbout from '@/components/SettingsAbout'
import Schedule from '@/components/Schedule'
import NotFound from '@/components/NotFound'
// Apply our vue components
Vue.use(Router)
Vue.use(Meta)

// Specify our routes and names
const router = new Router({
  mode: 'history',
  linkActiveClass: 'selected',
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home
    },
    {
      path: '/grid',
      name: 'Grid',
      component: Grid
    },
    {
      path: '/settings',
      name: 'SettingsAbout',
      component: SettingsAbout
    },
    {
      path: '/view/:date',
      name: 'Schedule',
      component: Schedule
    },
    {
      path: '/201([0-9]-[0-9]{2}-[0-9]{2})', // Redirects from old urls
      redirect: from => {
        return {
          name: 'Schedule',
          params: {
            date: '201' + from.params['0']
          }
        }
      }
    },
    {
      path: '/404',
      name: 'NotFound',
      component: NotFound
    },
    {
      path: '*',
      redirect: from => {
        return {
          name: 'NotFound',
          params: {
            lastPath: from.path // Pass the path the user tried
          }
        }
      }
    }
  ]
})

// After route, scroll to the content if in mobile width (but not on first load)
router.afterEach((to, from) => {
  if (from.name && window.screen.width <= 630) {
    SmoothScroll(document.querySelector('.content'))
  }
})

// Export the router
export default router
