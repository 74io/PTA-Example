/**
 * Grid theme for Highcharts JS
 * @author Torstein Hønsi
 */

Highcharts.theme = {

	title: {
		style: {
			color: '#000',
			font: 'bold 16px "Helvetica Neue",Helvetica,Arial,sans-serif'
		}
	},
	subtitle: {
		style: {
			color: '#666666',
			font: 'bold 12px "Helvetica Neue",Helvetica,Arial,sans-serif'
		}
	},
	xAxis: {
		labels: {
			style: {
				color: '#666',
				font: '12px "Helvetica Neue",Helvetica,Arial,sans-serif'
			}
		},
		title: {
			style: {
				color: '#333',
				fontWeight: 'bold',
				fontSize: '12px',
				fontFamily: 'Helvetica Neue,Helvetica,Arial,sans-serif'

			}
		}
	},
	yAxis: {
		labels: {
			style: {
				color: '#666',
				font: '11px "Helvetica Neue",Helvetica,Arial,sans-serif'
			}
		},
		title: {
			style: {
				color: '#333',
				fontWeight: 'bold',
				fontSize: '12px',
				fontFamily: 'Helvetica Neue,Helvetica,Arial,sans-serif'
			}
		}
	},
	legend: {
		itemStyle: {
			font: '9pt Trebuchet MS, Verdana, sans-serif',
			color: 'black'

		},
		itemHoverStyle: {
			color: '#039'
		},
		itemHiddenStyle: {
			color: 'gray'
		}
	},
	labels: {
		style: {
			color: '#99b'
		}
	}
};

// Apply the theme
var highchartsOptions = Highcharts.setOptions(Highcharts.theme);
