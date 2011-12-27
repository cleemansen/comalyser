Ext.define('com5000.view.team.chart.Team_chart_change_buy' ,{
    extend: 'Ext.chart.Chart',
    alias : 'widget.teamChartChangeBuy',

	id: 'teamChartChangeBuy',
    store: 'Team_store',
	
	theme: 'Base',
    style: 'background:#fff',
    animate: true,
    shadow: true,
	border: false,
    
    initComponent: function() {
		
		this.change_absolute_column_renderer = function(sprite, record, attributes, index, store) {
			var v = record.data.change_absolute;
			if(v < 0) {
				Ext.apply(attributes, {fill: '#CC0000'});
			}else if (v > 0) {
				Ext.apply(attributes, {fill: "rgb(170, 191, 59)"});
			}
			return attributes;
		};
		
		this.change_relative_column_renderer = function(sprite, record, attributes, index, store) {
			var v = record.data.change;
			if(v < 0) {
				Ext.apply(attributes, {fill: '#CC0000'});
			}else if (v > 0) {
				Ext.apply(attributes, {fill: "rgb(170, 191, 59)"});
			}
			return attributes;
		};
        
		this.axes = [{
			type: 'Numeric',
			position: 'left',
			fields: 'change',
			//majorTickSteps: 10,
			label: {
				renderer: Ext.util.Format.numberRenderer('0,0')
			},
			grid: true
		}, {
			type: 'Category',
			position: 'bottom',
			fields: 'name',
			label: {
				renderer: function(v) {
					return Ext.String.ellipsis(v, 10, false);
				},
				font: '10px Arial',
				rotate: {
					degrees: 270
				}
			} 
		}];
        
		this.series = [{
			type: 'column',
			axis: 'left',
			highlight: true,
			renderer : this.change_relative_column_renderer,
			tips: {
				trackMouse: true,
				width: 180,
				//height: 28,
				renderer: function(storeItem, item) {
					this.setTitle(storeItem.get('name') + ' - ' + storeItem.get('club') + "<br>" +
								  "Absolut: " + storeItem.get('change_absolute') + '<br>' +
								  "Relativ: " + storeItem.get('change') + '%');
				}
			},
			label: {
				display: 'outside',
				'text-anchor': 'middle',
				field: 'change',
				renderer: Ext.util.Format.numberRenderer('0,0'),
				font: '11px Arial',
				rotate: {
					degrees: 0	
				},
				color: '#000'
			},
			xField: 'name',
			yField: 'change'
		}];

        this.callParent(arguments);
    }
});