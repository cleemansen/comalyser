Ext.define('com5000.view.team.chart.Kicker_chart' ,{
    extend: 'Ext.chart.Chart',
    alias : 'widget.kickerChart',
	id: 'kickerChart',

    store: 'Kicker_store',
	
	theme: 'Category1',
    style: 'background:#fff',
    animate: true,
    shadow: true,
	border: false,
	/*legend: {
                position: 'right',
				itemSpacing: 0,
				padding: 3,
				labelFont: '8px Helvetica'
            },*/
    
    initComponent: function() {
		
		this.points_renderer = function(sprite, record, attributes, index, store) {
			var v = record.data.points;
			if(v < 0) {
				Ext.apply(attributes, {fill: '#CC0000'});
			}else if (v > 0) {
				Ext.apply(attributes, {fill: "rgb(170, 191, 59)"});
			}
			return attributes;
		};
        
		this.axes = [{
                type: 'Numeric',
                grid: true,
                position: 'right',
				decimals: 0,
                fields: ['price'],
                title: 'Marktwert',
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
				type: 'Numeric',
                //grid: true,
                position: 'left',
				decimals: 0,
                fields: ['points'],
                title: 'Punkte',
				minimum: -20,
				maximum: 20
                /*grid: {
                    odd: {
                        opacity: 1,
                        fill: '#ddd',
                        stroke: '#bbb',
                        'stroke-width': 1
                    }
                }*/	
			},{
                type: 'Time',
				dateFormat: 'd.m',
                position: 'bottom',
                fields: ['date'],
                grid: true,
                label: {
                    rotate: {
                        degrees: 0
                    }
                }
            }];
        this.series = [{
				type: 'column',
                axis: 'left',
                xField: 'date',
                yField: 'points',
				fillOpacity: 1,
                label: {
					display: 'insideEnd',
					'text-anchor': 'middle',
					field: 'points',
					renderer: Ext.util.Format.numberRenderer('0'),
					orientation: 'horizontal',
					color: '#333'
				},
				renderer : this.points_renderer
			}, {
                type: 'line',
                highlight: true,
                axis: 'right',
                xField: 'date',
                yField: 'price',
                style: {
                    lineWidth: 2
                    //stroke: '#11cc00',
                    //opacity: 0.5
                },
				tips: {
                  trackMouse: true,
                  width: 170,
                  height: 35,
                  renderer: function(storeItem, item) {
                      this.setTitle(Ext.Date.format(new Date(storeItem.get('date')), 'd.m.Y')
                              + ' - ' + storeItem.data.price);
                  }
                }
            }]

        this.callParent(arguments);
    }
});