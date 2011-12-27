Ext.require('Ext.chart.*');
Ext.require(['Ext.Window', 'Ext.layout.container.Fit', 'Ext.fx.target.Sprite']);

Ext.define('Change', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'name'
    }, {
        name: 'change',
        type: 'int'
    }]
});

var store = null;

Ext.onReady(function () {
	Ext.QuickTips.init();
	
	var uid = Ext.get('uid').dom.innerHTML;

	// for this demo configure local and remote urls for demo purposes
	var url = {
		local:  'home-kicker.json',  // static data file
		remote: 'ws/home_change.php?uid=' + uid
	};
	
	// configure whether filter query is encoded or not (initially)
	var encode = true;
	
	// configure whether filtering is performed locally or remotely (initially)
	var local = false;
	
	store = Ext.create('Ext.data.JsonStore', {
		// store configs
		autoDestroy: true,
		model: 'Change',
		proxy: {
			type: 'ajax',
			url: (local ? url.local : url.remote),
			reader: {
				type: 'json',
				root: 'data',
				idProperty: 'name',
				totalProperty: 'total'
			}
		},
		fields: ['name', 'change'],
		//autoLoad: true,
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
	}).load();
	
	//alert(store.min('change', false) + " - " + store.max('change', false));
	//alert(store.min('change', false) + " - " + store.max('change', false));

	
	//store.load();

	var panel = Ext.create('Ext.Panel', {
        width: 800,
        height: 350,
        hidden: false,
        title: 'Marktwertentwicklung deiner Spieler seit seinem Einkauf:',
        renderTo: 'first_chart',
        layout: 'fit',
		tbar: [{
            text: '1 Tag',
            handler: function() {
                alert("todo");
            }
        },{
            text: '1 Woche',
            handler: function() {
                alert("todo");
            }
        },{
            text: '1 Monat',
            handler: function() {
                alert("todo");
            }
        },{
            text: 'seit Einkauf',
            handler: function() {
                alert("siehe aktuelles Chart..");
            }
        }],
        items: {
            id: 'chartCmp',
            xtype: 'chart',
            style: 'background:#fff',
            animate: true,
            shadow: true,
            store: store,
            axes: [{
                type: 'Numeric',
                position: 'left',
                fields: 'change',
				majorTickSteps: 10,
                label: {
                    renderer: Ext.util.Format.numberRenderer('0,0')
                },
                //title: 'Number of Hits',
                grid: true,
                minimum: store.min('change', false),
				maximum: store.max('change', false)
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
				renderer : function(sprite, record, attributes, index, store) {
					var v = record.data.change;
					if(v < 0) {
						Ext.apply(attributes, {fill: '#CC0000'});
					}else if (v > 0) {
						Ext.apply(attributes, {fill: "rgb(170, 191, 59)"});
					}
					return attributes;
				},
                tips: {
                  trackMouse: true,
                  width: 140,
                  height: 28,
                  renderer: function(storeItem, item) {
                    this.setTitle(storeItem.get('name') + ': ' + storeItem.get('change') + '%');
                  }
                },
                label: {
					display: 'outside',
					'text-anchor': 'middle',
                    field: 'change',
                    renderer: Ext.util.Format.numberRenderer('0'),
					font: '9px Arial',
                    orientation: 'vertical',
                    color: '#333'
                },
                xField: 'name',
                yField: 'change'
            }]
        }
    });
});	

