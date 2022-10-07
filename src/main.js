/* globals CN_COMMITHASH */
// Vue stuff
import { createApp } from 'vue'
import App from './App'
import router from './router'
import { createPinia } from 'pinia'

// Modules
import * as Sentry from '@sentry/vue'
import { Integrations } from '@sentry/tracing'
import './registerServiceWorker'

// Polyfill
import { ResizeObserver as ResizeObserverPolyfill } from '@juggle/resize-observer'

// Tests for old browsers or scripts (no console.* || no window.* || ie 7-10 || Opera Mini)
if (typeof console === 'undefined' ||
    typeof window === 'undefined' ||
    (document.all && window.XMLHttpRequest) ||
    Object.prototype.toString.call(window.operamini) === '[object OperaMini]') {
  let errorMsgUnsupported = '<h1>CN Schedule</h1>'
  errorMsgUnsupported += '<p><b>You\'re currently using an unsupported browser</b>.<br/>'
  errorMsgUnsupported += 'Please upgrade to the latest version of Chrome, Opera, Brave, Firefox, Microsoft Edge or Safari to continue.</p>'

  document.getElementById('jserror').innerHTML = errorMsgUnsupported
  throw new Error('Unsupported browser')
}

// Apply polyfill
window.ResizeObserver = window.ResizeObserver || ResizeObserverPolyfill

// Create our app
const cnApp = createApp(App)

// Plug the modules
cnApp.use(router)
cnApp.use(createPinia())

// Don't include tracking in dev
if (import.meta.env.PROD) {
  Sentry.init({
    cnApp,
    dsn: 'https://c64d65a5b58a4852b77891b64cac04cc@sentry.io/213540',
    environment: import.meta.env.MODE || import.meta.env.NODE_ENV,
    integrations: [
      new Integrations.BrowserTracing({
        routingInstrumentation: Sentry.vueRouterInstrumentation(router),
        tracingOrigins: [/schedule\./, /^\//],
        startTransactionOnLocationChange: false
      })
    ],
    logErrors: true,
    release: CN_COMMITHASH,
    tracesSampleRate: 0.4
  })

  const shy = document.createElement('script')
  shy.setAttribute('src', 'https://shy.ctoon.network/ingress/85c3200b-ebd6-49d7-b87d-6eb29fecc21b/script.js')
  document.body.appendChild(shy)
}

// LET'S GOOOOOOO
cnApp.mount('#app')
