import Swiper from 'swiper/bundle';
import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';

import '@fortawesome/fontawesome-free/css/all.min.css';

Chart.register(ChartDataLabels);

/*
 * Inline scripts in Blade partials still use the global names, so expose the
 * bundled libraries on window. Those scripts must run after the module
 * executes (i.e. inside a DOMContentLoaded listener), because ES modules are
 * deferred.
 */
window.Swiper = Swiper;
window.Chart = Chart;
window.ChartDataLabels = ChartDataLabels;
