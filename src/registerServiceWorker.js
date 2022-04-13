/* globals Event */
/* eslint-disable no-console */
import { registerSW } from 'virtual:pwa-register'

window.updateSW = registerSW({
  onNeedRefresh () {
    console.log('An update is available!')
    const event = new Event('updated')
    window.dispatchEvent(event)
  },
  onOfflineReady () {
    console.log('The app is now cached.')
  },
  onRegisterError (error) {
    console.error('Error while registering service worker:', error)
  }
})
