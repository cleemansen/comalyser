Ext.define('com5000.view.team.chart.Money_chart' ,{
    extend: 'Ext.chart.Chart',
    alias : 'widget.moneyChart',

	id: 'moneyChart',
    store: 'Money_store',
	layout: 'fit',
	
	//theme: 'Base',
    style: 'background:#fff',
    animate: true,
    shadow: true,
	border: false,

	legend: {
                position: 'bottom'
            },
    
    initComponent: function() {
		
		this.axes = [{
                type: 'Numeric',
                //minimum: 0,
                position: 'right',
                fields: ['team_value'],
                title: "Mannschaftswert [in Mio.]",
                majorTickSteps: 4,
                grid: false /*{
                    odd: {
                        opacity: 1,
                        fill: 'transparent',
                        stroke: '#f00',
                        'stroke-width': 0.5
                    }
                }*/,
				label: {
                    renderer: function(v) {
						v = v / 1000000;
						return v;
					}
                }
				
            }, {
				type: 'Numeric',
				id: 'balance_axis',
				position: 'left',
				fields: ['balance'],
				title: 'Kontostand [in Mio.]',
				majorTickSteps: 4,
				label: {
                    renderer: function(v) {
						v = v / 1000000;
						return v;
					}
                }
			},{
                type: 'Category',
                position: 'bottom',
                fields: ['date'],
                //title: 'Day',
				label: {
                    renderer: Ext.util.Format.dateRenderer('d.m.Y')
                }
            }];
        this.series = [{
                type: 'line',
                highlight: {
                    size: 7,
                    radius: 7
                },
                axis: 'right',
                xField: 'date',
                yField: 'team_value',
                markerConfig: {
                    type: 'cross',
                    size: 4,
                    radius: 4,
                    'stroke-width': 0
                }
            },{
				type: 'line',
				axis: 'left',
				xField: 'date',
				yField: 'balance'
			}];
		
		
		

        this.callParent(arguments);
    }
});