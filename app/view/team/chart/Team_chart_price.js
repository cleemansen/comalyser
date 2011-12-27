Ext.define('com5000.view.team.chart.Team_chart_price' ,{
    extend: 'Ext.chart.Chart',
    alias : 'widget.teamChartPrice',
	id: 'teamChartPrice',

    store: 'arrayStore',
	
	theme: 'Base',
    style: 'background:#fff',
    animate: true,
    shadow: true,
	border: false,
	legend: {
                position: 'right',
				itemSpacing: 0,
				padding: 3,
				labelFont: '8px Helvetica'
            },
    
    initComponent: function() {
        
		this.axes = [{
                type: 'Numeric',
                grid: true,
                position: 'left',
				decimals: 0,
                fields: ['data1', 'data2', 'data3', 'data4', 'data5', 'data6', 'data7'],
                title: 'Number of Hits',
                grid: {
                    odd: {
                        opacity: 1,
                        fill: '#ddd',
                        stroke: '#bbb',
                        'stroke-width': 1
                    }
                },
                minimum: 0,
                adjustMinimumByMajorUnit: 0
            }, {
                type: 'Time',
				dateFormat: 'd/m',
                position: 'bottom',
                fields: ['date'],
                title: 'Month of the Year',
                grid: true,
                label: {
                    rotate: {
                        degrees: 0
                    }
                }
            }];
        this.series = [{
                type: 'area',
                highlight: true,
                axis: 'left',
                xField: 'date',
                yField: ['data1', 'data2', 'data3', 'data4', 'data5', 'data6', 'data7'],
                style: {
                    lineWidth: 1,
                    stroke: '#666',
                    opacity: 0.86
                },
				tips: {
                  trackMouse: true,
                  width: 170,
                  height: 35,
                  renderer: function(storeItem, item) {
                      this.setTitle(item.storeField + ' - '
                              + Ext.Date.format(new Date(storeItem.get('date')), 'd.m.Y')
                              + ' - ' + storeItem.get(item.storeField));
                  }
                }
            }]

        this.callParent(arguments);
    }
});