import { Doughnut, mixins } from 'vue-chartjs'

export default {
  name: 'stats-chart',
  mixins: [Doughnut, mixins.reactiveProp],
  props: ['chartData'],
  mounted () {
    this.renderChart(this.chartData, {
      layout: {
        padding: 15
      },
      legend: {
        display: false
      }
    })
  }
}
