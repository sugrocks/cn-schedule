/* eslint-disable no-useless-escape */
import path from 'path'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import envCompatible from 'vite-plugin-env-compatible'
import { EsLinter, linterPlugin } from 'vite-plugin-linter'
import { VitePWA } from 'vite-plugin-pwa'
import { visualizer } from 'rollup-plugin-visualizer'
import viteSentry from 'vite-plugin-sentry'
import { GitRevisionPlugin } from 'git-revision-webpack-plugin'

const gitRevisionPlugin = new GitRevisionPlugin()

let netlifyConf = []
if (process.env.NETLIFY && process.env.PULL_REQUEST !== 'true') {
  netlifyConf = [
    viteSentry({
      authToken: process.env.SENTRY_AUTH_TOKEN,
      org: process.env.SENTRY_ORG,
      project: process.env.SENTRY_PROJECT,
      release: gitRevisionPlugin.commithash(),
      deploy: {
        env: process.env.CONTEXT
      },
      setCommits: {
        auto: true
      },
      sourceMaps: {
        include: [
          './dist',
          './dist/assets'
        ],
        ignore: [
          'node_modules'
        ],
        urlPrefix: '~/assets'
      }
    })
  ]
}

export default defineConfig((configEnv) => ({
  define: {
    CN_COMMITHASH: JSON.stringify(gitRevisionPlugin.commithash())
  },
  resolve: {
    alias: [
      {
        find: /^~/,
        replacement: ''
      },
      {
        find: '@',
        replacement: path.resolve(__dirname, 'src')
      }
    ],
    extensions: [
      '.mjs',
      '.js',
      '.ts',
      '.jsx',
      '.tsx',
      '.json',
      '.vue'
    ]
  },
  plugins: [
    linterPlugin({
      include: [
        './src/**/*.js',
        './src/**/*.vue'
      ],
      linters: [
        new EsLinter({
          configEnv,
          serveOptions: { clearCacheOnStart: true }
        })
      ]
    }),
    vue(),
    envCompatible(),
    VitePWA({
      filename: 'service-worker.js',
      workbox: {
        globPatterns: [
          '**\/*.{js,css,html,webmanifest}',
          'img\/icons/*'
        ]
      },
      manifest: {
        name: 'CN Schedule',
        short_name: 'Schedule',
        description: 'Browse the US TV Schedule for Cartoon Network and Adult Swim',
        icons: [
          {
            src: '/img/icons/android-chrome-192x192.png',
            sizes: '192x192',
            type: 'image/png'
          },
          {
            src: '/img/icons/android-chrome-256x256.png',
            sizes: '256x256',
            type: 'image/png'
          },
          {
            src: '/img/icons/android-chrome-384x384.png',
            sizes: '384x384',
            type: 'image/png'
          },
          {
            src: '/img/icons/android-chrome-512x512.png',
            sizes: '512x512',
            type: 'image/png'
          }
        ],
        theme_color: '#000000',
        background_color: '#EC018C',
        start_url: '/',
        display: 'standalone',
        dir: 'ltr',
        lang: 'en-US',
        categories: [
          'entertainment',
          'utilities'
        ],
        shortcuts: [
          {
            name: 'Grid',
            description: 'Get a quick view of the schedule for the next days',
            url: '/grid'
          }
        ]
      }
    }),
    visualizer(),
    ...netlifyConf
  ],
  build: {
    sourcemap: true,
    cssCodeSplit: false // hopefully prevents asking for preloading CSS which has issues on Safari and some Chromium? browser
  }
}))
