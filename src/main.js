/* globals VERSION COMMITHASH BRANCH */
// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
// Polyfills
import 'promise-polyfill/src/polyfill'
import 'whatwg-fetch'
// Our app
import App from './App'
// Some plugins and components
import Raven from 'raven-js'
import RavenVue from 'raven-js/plugins/vue'
import VueAnalytics from 'vue-analytics'
import Tabs from 'vue-tabs-component/src/index' // Broke?
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import 'flatpickr/dist/themes/dark.css'
// The router
import router from './Router'

// Tests for old browsers (no console.* for weird browsers || ie 7-10 || Server-side Opera Mini)
if (typeof console === 'undefined' ||
    (document.all && window.XMLHttpRequest) ||
    Object.prototype.toString.call(window.operamini) === '[object OperaMini]') {
  var errorMsgUnsupported = '<h1>CN Schedule</h1>'
  errorMsgUnsupported += '<b>You\'re currently using an unsupported browser</b>.<br>'
  errorMsgUnsupported += 'Please upgrade to the latest version of Chrome, Opera, Brave, Firefox, Microsoft Edge or Safari to continue.<br>'
  errorMsgUnsupported += '<a href="https://whatbrowser.org/">More details on having a good and updated browser here.</a>'

  document.getElementById('jserror').innerHTML = errorMsgUnsupported
  throw new Error('Unsupported browser')
}

// Stop posting tips
Vue.config.productionTip = false

// Enable plugins
if (process.env.NODE_ENV !== 'development') {
  Raven
    .config('https://c64d65a5b58a4852b77891b64cac04cc@sentry.io/213540', {
      release: VERSION,
      tags: {
        git_commit: COMMITHASH,
        git_branch: BRANCH
      },
      environment: process.env
    })
    .addPlugin(RavenVue, Vue)
    .install()
  Vue.use(VueAnalytics, {
    id: 'UA-103935709-2',
    router
  })
} else {
  console.log('%cRaven and Analytics disabled in dev', 'color:orange;')
}

Vue.use(Tabs)
Vue.use(flatPickr)

// Say hi to whoever opens the console
console.log(
  '%cCN Schedule',
  'font-size:70px;color:#fff;text-shadow:0 1px 0 #ccc,0 2px 0 #c9c9c9,0 3px 0 #bbb,0 4px 0 #b9b9b9,0 5px 0 #aaa,0 6px 1px rgba(0,0,0,.1),0 0 5px rgba(0,0,0,.1),0 1px 3px rgba(0,0,0,.3),0 3px 5px rgba(0,0,0,.2),0 5px 10px rgba(0,0,0,.25),0 10px 10px rgba(0,0,0,.2),0 20px 20px rgba(0,0,0,.15);'
)
console.log(
  '%cby CTOON Webmedia Group' +
  '\nGit: https://gitlab.com/ctoon/cn-schedule',
  'font-style:italic;'
)
console.log(
  '%cCommit: ' + COMMITHASH +
  '\nVersion: ' + VERSION +
  '\nBranch: ' + BRANCH +
  '\nEnv: ' + process.env.NODE_ENV,
  'color:green;'
)

/* eslint-disable no-new */
// Create vue app
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
