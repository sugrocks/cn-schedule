/* global Shynet */
// Vue stuff
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: _ => import('@/components/Home')
    },
    {
      path: '/grid',
      name: 'Grid',
      component: _ => import('@/components/Grid')
    },
    {
      path: '/settings',
      name: 'SettingsAbout',
      component: _ => import('@/components/SettingsAbout')
    },
    {
      path: '/view/:date',
      name: 'Schedule',
      component: _ => import('@/components/Schedule')
    },
    {
      path: '/20([0-9]{2}-[0-9]{2}-[0-9]{2})', // Redirects from old urls
      redirect: from => {
        return {
          name: 'Schedule',
          params: {
            date: '20' + from.params['0']
          }
        }
      }
    },
    {
      path: '/404',
      name: 'NotFound',
      component: _ => import('@/components/NotFound')
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: from => {
        return {
          name: 'NotFound',
          params: {
            lastPath: from.path // Pass the path the user tried
          }
        }
      }
    }
  ],
  // After route, scroll to the content if in mobile width (but not on first load)
  scrollBehavior (to, from, savedPosition) {
    if (to.path !== from.path) {
      // Only act if path changed
      if (savedPosition) {
        // Delay by 150ms due to transition getting in the way
        // Will not work if we have to load stuff but oh well
        return new Promise((resolve, reject) => {
          setTimeout(_ => {
            resolve(savedPosition)
          }, 150)
        })
      } else if (from.name && window.screen.width < 675) {
        // Smooth scroll to content on mobile
        return {
          el: '.content',
          behavior: 'smooth'
        }
      } else {
        return {
          top: 0
        }
      }
    }
  }
})

router.afterEach((to, from, failure) => {
  if (!failure && to.path !== from.path && 'Shynet' in window) {
    // Notify Shynet about a new page load
    Shynet.newPageLoad()
  }
})

// Export the router
export default router
