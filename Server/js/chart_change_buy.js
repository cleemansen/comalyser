var store = Ext.getCmp('store_team');

// example of custom renderer function
function change_renderer(value, metaData, record, rowIndex, colIndex, store) {
	if(value > 0){
		metaData.tdCls = 'positive_change';
		return "+" + value + "%";
	}else if(value < 0){
		metaData.tdCls = 'negative_change';
		return value + "%";
	}else if(value == 0){
		metaData.tdCls = 'no_change';
		return value + "%";
	}
	return value;
}

function change_renderer_absolute(value, metaData, record, rowIndex, colIndex, store) {
	var o = value;
	if(value > 0){
		metaData.tdCls = 'positive_change';
		return "+" + o;
	}else if(value < 0){
		metaData.tdCls = 'negative_change';
		return o;
	}else if(value == 0){
		metaData.tdCls = 'no_change';
		return o;
	}
	return value;
}

function change_absolute_column_renderer(sprite, record, attributes, index, store) {
	var v = record.data.change_absolute;
	if(v < 0) {
		Ext.apply(attributes, {fill: '#CC0000'});
	}else if (v > 0) {
		Ext.apply(attributes, {fill: "rgb(170, 191, 59)"});
	}
	return attributes;
}

function change_relative_column_renderer(sprite, record, attributes, index, store) {
	var v = record.data.change;
	if(v < 0) {
		Ext.apply(attributes, {fill: '#CC0000'});
	}else if (v > 0) {
		Ext.apply(attributes, {fill: "rgb(170, 191, 59)"});
	}
	return attributes;
}

Ext.onReady(function(){
	Ext.create('Ext.chart.Chart', {
		id: 'chartCmp',
		//xtype: 'chart',
		style: 'background:#fff',
		animate: true,
		shadow: true,
		store: Ext.getCmp('team_store'),
		axes: [{
			type: 'Numeric',
			position: 'left',
			fields: 'change',
			//majorTickSteps: 10,
			label: {
				renderer: Ext.util.Format.numberRenderer('0,0')
			},
			//title: 'Number of Hits',
			grid: true/*{
				odd: {
					opacity: 1,
					fill: '#ddd',
					stroke: '#bbb',
					'stroke-width': 0.5
				}
			}*/
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
		}],
		series: [{
			type: 'column',
			axis: 'left',
			highlight: true,
			renderer : change_relative_column_renderer,
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
			yField: 'change',
			listeners: {
				'itemmouseup': function(item) {
					var series = change_chart.series.get(0),
						index = Ext.Array.indexOf(series.items, item),
						selectionModel = grid.getSelectionModel();
						
						selectedStoreItem = item.storeItem;
						selectionModel.select(index);
				}
			}
		}]
	});
});