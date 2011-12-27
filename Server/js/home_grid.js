Ext.Loader.setConfig({enabled: true});
//Ext.Loader.setPath('Ext.ux', '../ux');
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
	'Ext.chart.*',
	'Ext.grid.Panel'
]);
//the kicker grid model
Ext.define('Kicker', {
    extend: 'Ext.data.Model',
    fields: [
	{
        name: 'update',
        type: 'date',
		dateFormat: 'Y-m-d'
    }, {
        name: 'name'
    }, {
        name: 'club'
    }, {
        name: 'pos'
    }, {
        name: 'points',
        type: 'int'
    }, {
        name: 'price',
        type: 'int'
    }, {
        name: 'purchase_price',
        type: 'int'
    }, {
        name: 'change',
        type: 'int'
    }, {
        name: 'purchase_date',
        type: 'date',
		dateFormat: 'Y-m-d'
    }, {
        name: 'change_absolute',
        type: 'int'
    }, {
        name: 'change_yesterday',
        type: 'int'
    }, {
        name: 'change_absolute_yesterday',
        type: 'int'
    }]
});

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
	//Ext.util.Format.numberRenderer('0.000/i');
	/*
	var o = '';
	value = value.toString();
	for(var i = value.length; i > 0; i = i-3){
		var ss;
		if(i > 3)
			ss = value.substring(i-4, i-1);
		else
			ss = value.substring(0, i);
		o = ss + '.' + o;
	}
	o = o.substring(0, o.length-2);
	//value = value.substring(0, value.length-4) + '.' + ss;
	value = ss;
	*/
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
	var uid = Ext.get('uid').dom.innerHTML,
		selectedStoreItem = false,
        //performs the highlight of an item in the bar series
        selectItem = function(storeItem) {
            var name = storeItem.get('name'),
                series = change_chart.series.get(0),
				series2 = change_chart_yesterday.series.get(0),
                i, items, l;
            
            series.highlight = true;series2.highlight = true;
            series.unHighlightItem();series2.unHighlightItem();
            series.cleanHighlights();series2.cleanHighlights();
            for (i = 0, items = series.items, l = items.length; i < l; i++) {
                if (name == items[i].storeItem.get('name')) {
                    selectedStoreItem = items[i].storeItem;
                    series.highlightItem(items[i]);
                    break;
                }
            }
            series.highlight = false;
			
			for (i = 0, items = series2.items, l = items.length; i < l; i++) {
                if (name == items[i].storeItem.get('name')) {
                    selectedStoreItem = items[i].storeItem;
                    series2.highlightItem(items[i]);
                    break;
                }
            }
            series2.highlight = false;
        },
		updateRecord = function(rec) {
			selectItem(rec);
		};
	
    Ext.QuickTips.init();
	/*
	 * THE KICKER GRID
	 */
    // for this demo configure local and remote urls for demo purposes
    var url = {
        local:  'home-kicker.json',  // static data file
        remote: 'ws/home-kicker.php?uid=' + uid
    };

    // configure whether filter query is encoded or not (initially)
    var encode = true;
    
    // configure whether filtering is performed locally or remotely (initially)
    var local = false;

    var store = Ext.create('Ext.data.JsonStore', {
        // store configs
        autoDestroy: true,
		autoLoad: true,
		storeId: 'team_store',
        model: 'Kicker',
        proxy: {
            type: 'ajax',
            url: (local ? url.local : url.remote),
            reader: {
                type: 'json',
                root: 'data',
                idProperty: 'name',
                totalProperty: 'total'
            },
			listeners: {
				load: function(ref, records, successful){
					if(successful){
						//waiting for the store - min max values for axes.
						var chart = Ext.getCmp ('chartCmp');
						var min = store.min('change', false);
						var max = store.max('change', false);
						min = min/10;
						max = max/10;
						min = Math.round(min-0.5);
						max = Math.round(max+0.5);
						min = min * 10;
						max = max * 10;
						chart.axes.items[0].minimum = min;
						chart.axes.items[0].maximum = max;
						min = Math.abs(min);
						max = Math.abs(max);
						var d = min + max;
						chart.axes.items[0].majorTickSteps = (d / 10);
						chart.axes.items[0].minorTickSteps = 0;
						chart.redraw();
					}else{
						alert('Image load error.');
					}
				}
			}
        }
        /*remoteSort: false,
        sorters: [{
            property: 'company',
            direction: 'ASC'
        }],
        pageSize: 50*/
    });
	
	// use a factory method to reduce code while demonstrating
    var createColumns = function (finish, start) {

        var columns = [{
            dataIndex: 'update',
            text: 'Update',
            // instead of specifying filter config just specify filterable=true
            // to use store's field's type property (if type property not
            // explicitly specified in store config it will be 'auto' which
            // GridFilters will assume to be 'StringFilter'
            filterable: true,
			renderer: Ext.util.Format.dateRenderer('d.m.Y'),
            width: 66
            //,filter: {type: 'numeric'}
        }, {
            dataIndex: 'name',
            text: 'Name',
            flex: 1,
			id: 'kicker_name',
            filter: {
                type: 'string'
                // specify disabled to disable the filter menu
                //, disabled: true
            }
        }, {
            dataIndex: 'club',
            text: 'Verein',
			flex: 1
            //width: 70
        }, {
            dataIndex: 'pos',
            text: 'Position',
			align: 'center',
			width: 70,
            filter: {
                type: 'list',
                options: ['0', '1', '2', '3']
                //,phpMode: true
            }
        }, {
            dataIndex: 'points',
            text: 'Punkte',
			align: 'right',
			width: 50,
			id: 'kicker_points'
            // this column's filter is defined in the filters feature config
        }, {
            dataIndex: 'price',
            text: 'Marktwert',
			align: 'right',
			renderer: Ext.util.Format.numberRenderer('0.000/i'),
			width: 70
			//xtype: 'numbercolumn'
            // this column's filter is defined in the filters feature config
        }, {
            dataIndex: 'change_absolute',
            text: '< &#177; >',
            filter: true,
			align: 'right',
			renderer: change_renderer_absolute,
			id: 'kicker_change_absolute',
			width: 70
        }, {
            dataIndex: 'change',
            text: '< % >',
            filter: true,
			align: 'right',
			renderer: change_renderer,
			id: 'kicker_change',
			width: 50
        }, {
            dataIndex: 'purchase_price',
            text: 'Kaufpreis',
			align: 'right',
            filter: true,
			renderer: Ext.util.Format.numberRenderer('0.000/i'),
			width: 70
        }, {
            dataIndex: 'purchase_date',
            text: 'Kaufdatum',
            filter: true,
            renderer: Ext.util.Format.dateRenderer('d.m.Y'),
			width: 66
        }];

        return columns.slice(start || 0, finish);
    };
	
	// the grid panel
	var grid = Ext.create('Ext.grid.Panel', {
        border: false,
        store: store,
        columns: createColumns(10),
		loadMask: true,
		id: 'home_grid',
		layout: 'fit',
		flex: 2,
		height: '100%',
		
		listeners: {
            selectionchange: function(model, records) {
                var json, name, i, l, items, series, fields;
				
                if (records[0]) {
                    rec = records[0];
                    //form = form || this.up('form').getForm();
                    //fields = form.getFields();
                    // prevent change events from firing
                    /*fields.each(function(field){
                        field.suspendEvents();
                    });
                    form.loadRecord(rec);*/
                    updateRecord(rec);
                    /*fields.each(function(field){
                        field.resumeEvents();
                    });*/
                }
                
            }
        }
		
        //features: [filters],
        /*dockedItems: [Ext.create('Ext.toolbar.Paging', {
            dock: 'bottom',
            store: store
        })]*/
    });
	
	/*
	 * WINS AND LOOSE CHART
	 */
	var change_chart = Ext.create('Ext.chart.Chart', {
		id: 'chartCmp',
		style: 'background:#fff',
		animate: true,
		shadow: true,
		store: store,
		axes: [{
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
	
	// WINS LOOSES YESTERDAY
	var change_chart_yesterday = Ext.create('Ext.chart.Chart', {
		id: 'chart_change_yesterday',
		style: 'background:#fff',
		animate: true,
		shadow: true,
		store: store,
		axes: [{
			type: 'Numeric',
			position: 'left',
			fields: 'change_yesterday',
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
								  "Absolut: " + storeItem.get('change_absolute_yesterday') + '<br>' +
								  "Relativ: " + storeItem.get('change_yesterday') + '%');
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
			yField: 'change_yesterday',
			listeners: {
				'itemmouseup': function(item) {
					var series = change_chart_yesterday.series.get(0),
						index = Ext.Array.indexOf(series.items, item),
						selectionModel = grid.getSelectionModel();
						
						selectedStoreItem = item.storeItem;
						selectionModel.select(index);
				}
			}
		}]
	});
	
	
	
	var change_chart_panel = Ext.create('Ext.Panel', {
        //flex: 1,
		height: 324,
		width: '100%',
		border: false,
        hidden: false,
		id: "change_panel",
        //title: 'Marktwertentwicklung deiner Spieler seit ihrem jeweiligen Einkauf:',
        //renderTo: 'first_chart',
        layout: 'fit',
		tbar: [
		{
			text: 'Spielerentwicklung:',
			disabled: true,
			handler: function() {
				alert("Dieses Diagramm zeigt dir bla bla bla... TODO");
			}
		},{
			enableToggle: true,
			allowDepress: false,
            text: 'Relativ',
			pressed: true,
			toggleGroup: 'bt_change_buy',
			toggleHandler: function(btn, pressed) {
                //var chart = Ext.getCmp('chartCmp');
                if(pressed) {
					var chart = Ext.getCmp ('chartCmp');
					chart.axes.items[0].fields = 'change';
					
					var min = store.min('change', false);
					var max = store.max('change', false);
					min = min/10;
					max = max/10;
					min = Math.round(min-0.5);
					max = Math.round(max+0.5);
					min = min * 10;
					max = max * 10;
					chart.axes.items[0].minimum = min;
					chart.axes.items[0].maximum = max;
					min = Math.abs(min);
					max = Math.abs(max);
					var d = min + max;
					chart.axes.items[0].majorTickSteps = (d / 10);
					chart.axes.items[0].minorTickSteps = 0;
					
					chart.series.items[0].renderer = change_relative_column_renderer;
					chart.series.items[0].yField = 'change';
					chart.series.items[0].label.field = 'change';
					chart.series.items[0].label.rotate.degrees = 0;
					
					chart.redraw();
				}
            }
        },{
            text: 'Absolut',
			enableToggle: true,
			allowDepress: true,
			toggleGroup: 'bt_change_buy',
            toggleHandler: function(btn, pressed) {
				if(pressed) {
					var chart = Ext.getCmp ('chartCmp');
					chart.axes.items[0].fields = 'change_absolute';
					var min = store.min('change_absolute', false);
					var max = store.max('change_absolute', false);
					chart.axes.items[0].minimum = min;
					chart.axes.items[0].maximum = max;
					chart.series.items[0].renderer = change_absolute_column_renderer;
					chart.series.items[0].yField = 'change_absolute';
					chart.series.items[0].label.field = 'change_absolute';
					chart.series.items[0].label.rotate = {degrees: 45};
					
					chart.redraw();
				}
            }
        }],
        items: [Ext.getCmp('chartCmp')]
	});
	
	
	
	// The chart for wins and loses since yesterday
	var change_chart_yesterday_panel = Ext.create('Ext.Panel', {
        height: 324,
		width: '100%',
		border: false,
        hidden: false,
		id: "change_yesterday_panel",
        layout: 'fit',
		tbar: [
		{
			text: 'Spielerentwicklung:',
			handler: function() {
				alert("Dieses Diagramm zeigt dir bla bla bla... TODO");
			}
		},{
            enableToggle: true,
			allowDepress: false,
            text: 'Relativ',
			pressed: true,
			toggleGroup: 'bt_change_yesterday',
			toggleHandler: function(btn, pressed) {
                //var chart = Ext.getCmp('chartCmp');
                if(pressed) {
					var chart = Ext.getCmp ('chart_change_yesterday');
					chart.axes.items[0].fields = 'change_yesterday';
					
					var min = store.min('change_yesterday', false);
					var max = store.max('change_yesterday', false);
					min = min/10;
					max = max/10;
					min = Math.round(min-0.5);
					max = Math.round(max+0.5);
					min = min * 10;
					max = max * 10;
					chart.axes.items[0].minimum = min;
					chart.axes.items[0].maximum = max;
					min = Math.abs(min);
					max = Math.abs(max);
					var d = min + max;
					chart.axes.items[0].majorTickSteps = (d / 10);
					chart.axes.items[0].minorTickSteps = 0;
					
					chart.series.items[0].renderer = change_relative_column_renderer;
					chart.series.items[0].yField = 'change_yesterday';
					chart.series.items[0].label.field = 'change_yesterday';
					chart.series.items[0].label.rotate.degrees = 0;
					
					chart.redraw();
				}
            }
        },{
            enableToggle: true,
			allowDepress: false,
            text: 'Absolute',
			pressed: false,
			toggleGroup: 'bt_change_yesterday',
			toggleHandler: function(btn, pressed) {
                //var chart = Ext.getCmp('chartCmp');
                if(pressed) {
					var chart = Ext.getCmp ('chart_change_yesterday');
					chart.axes.items[0].fields = 'change_absolute_yesterday';
					var min = store.min('change_absolute_yesterday', false);
					var max = store.max('change_absolute_yesterday', false);
					chart.axes.items[0].minimum = min;
					chart.axes.items[0].maximum = max;
					chart.series.items[0].renderer = change_absolute_column_renderer;
					chart.series.items[0].yField = 'change_absolute_yesterday';
					chart.series.items[0].label.field = 'change_absolute_yesterday';
					chart.series.items[0].label.rotate = {degrees: 45};
					
					chart.redraw();
				}
            }
        }],
        items: [change_chart_yesterday/*Ext.getCmp('chart_change_yesterday')*/]
	});
	
	//disable highlighting by default.
    change_chart.series.get(0).highlight = false;
    
    //add listener to (re)select bar item after sorting or refreshing the dataset.
    change_chart.addListener('beforerefresh', (function() {
        var timer = false;
        return function() {
            clearTimeout(timer);
            if (selectedStoreItem) {
                timer = setTimeout(function() {
                    selectItem(selectedStoreItem);
                }, 900);
            }
        };
    })());
	
	
	/*
	var extern = getChart_changeYesterday();
	
	var extern_test = Ext.create('Ext.Panel', {
        height: 324,
		width: 500,
		border: true,
        hidden: false,
		id: "extern_panel",
        layout: 'fit',
		tbar: [
		{
			text: 'Spielerentwicklung:',
			handler: function() {
				alert("Dieses Diagramm zeigt dir bla bla bla... TODO");
			}
		}],
		items: [extern]
	});*/
	//var test = Ext.getCmp('chartCmp');
	//disable highlighting by default.
    /*change_chart_yesterday.series.get(0).highlight = false;
    
    //add listener to (re)select bar item after sorting or refreshing the dataset.
    change_chart_yesterday.addListener('beforerefresh', (function() {
        var timer = false;
        return function() {
            clearTimeout(timer);
            if (selectedStoreItem) {
                timer = setTimeout(function() {
                    selectItem(selectedStoreItem);
                }, 900);
            }
        };
    })());*/
	
	var tabs2 = Ext.createWidget('tabpanel', {
        //renderTo: document.body,
        activeTab: 0,
        //width: 600,
        //height: 800,
        plain: true,
		//margin: '0 50 0 5',
		border: false,
        defaults :{
            autoScroll: true,
            bodyPadding: 0
        },
        items: [{
				title: 'seit Einkauf',
				border: false,
				handler: function() {alert("yeeeaahh");},
                items: [change_chart_panel]
            },{
				title: 'seit gestern',
				border: false,
				//html: 'bla'
                items: [Ext.getCmp('change_yesterday_panel')]
            },{
				margin: '10 3 0 3',
                title: 'seit einer Woche',
                items: [/*extern_test*/]
				//html: 'wersdf'
            },{
				margin: '0 3 0 3',
                title: 'seit einem Monat',
                html: 'coming soon...'
            }]
	});
	
	
	var displayPanel = new Ext.Panel({
		width    : '100%',
		height   : 800,
		//layout: 'fit',
		renderTo : 'home',
		bodyPadding: 5,
		
		layout:{type: 'vbox', align: 'stretch'},
		items: [
			{
				height: 350,
				width: '100%',
				//flex: 1,
                layout: 'fit',
                margin: '0 0 3 0',
				border: false,
                items: [tabs2]
			},
			{
				layout: {type: 'hbox', align: 'stretch'},
				flex: 3,
				border: false,
				margin: '0 0 0 0',
				items: [
					{
						border: true,
						flex: 1.5,
						margin: '0 10 0 0',
						items: [grid]
					}, {
						border: true,
						flex: 1,
						items: [Ext.getCmp('overview_chart')]
					}
				]
			}
		]
	});
	
    //store.load();
	//extern.chart_changeYesterday.redraw();
	
});
