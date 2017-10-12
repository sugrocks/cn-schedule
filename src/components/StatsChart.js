import { Doughnut, mixins } from 'vue-chartjs'

export default Doughnut.extend({
  name: 'stats-chart',
  mixins: [mixins.reactiveProp],
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
})
