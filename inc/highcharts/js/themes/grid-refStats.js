/**
 * Grid-light theme for Highcharts JS
 * @author Torstein Honsi
 * UPDATED for WSU RefStats
 */

// Load the fonts
Highcharts.createElement('link', {
	href: '//fonts.googleapis.com/css?family=Dosis:400,600',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

var chart_colors = [
	"rgba(6,158,135,.3)", 
	"rgba(6,158,135,.6)", 
	"rgba(6,158,135,1)", 
	"rgba(4,111,95,1)", 
	"rgba(238,196,69,.35)", 
	"rgba(238,196,69,.6)", 
	"rgba(238,196,69,1)",
	"rgba(204,169,59,1)",
	"rgba(142, 144, 144, .2)",
	"rgba(142, 144, 144, .4)",
	"rgba(142, 144, 144, .6)",
	"rgba(142, 144, 144, .9)",
	"rgba(110, 110, 110, 1)"
]

Highcharts.theme = {
	colors: chart_colors,
	chart: {
		backgroundColor: null,
		style: {
			fontFamily: "Dosis, sans-serif"
		}
	},
	title: {
		style: {
			fontSize: '18px',
			fontWeight: 'bold',
			textTransform: 'uppercase'
		}
	},
	tooltip: {
		borderWidth: 0,
		backgroundColor: 'rgba(219,219,216,0.8)',
		shadow: false
	},
	legend: {
		itemStyle: {
			fontWeight: 'bold',
			fontSize: '15px'
		}
	},
	xAxis: {
		gridLineWidth: 1,
		labels: {
			style: {
				fontSize: '14px'
			}
		}
	},
	yAxis: {
		minorTickInterval: 'auto',
		title: {
			style: {
				textTransform: 'uppercase'
			}
		},
		labels: {
			style: {
				fontSize: '14px'
			}
		}
	},
	plotOptions: {
		candlestick: {
			lineColor: '#404048'
		}
	},


	// General
	background2: '#F0F0EA'
	
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);