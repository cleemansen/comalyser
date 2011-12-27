Ext.Loader.setConfig({enabled: true});
//Ext.Loader.setPath('Ext.ux', '../ux');
Ext.require([
    'Ext.data.*',
	'Ext.chart.*'
]);
//the kicker grid model
Ext.define('Overview', {
    extend: 'Ext.data.Model',
    fields: [
	{
        name: 'date',
        type: 'date',
		dateFormat: 'Y-m-d'
    }, {
        name: 'team_value',
        type: 'int'
    }, {
        name: 'balance',
        type: 'int'
    }]
});

Ext.onReady(function(){
	var uid = Ext.get('uid').dom.innerHTML;
	
    Ext.QuickTips.init();
	/*
	 * THE KICKER GRID
	 */
    // for this demo configure local and remote urls for demo purposes
    var url = {
        remote: 'ws/home_overview.php?uid=' + uid
    };

    // configure whether filter query is encoded or not (initially)
    var encode = true;
    
    var store_overview = Ext.create('Ext.data.JsonStore', {
        // store configs
        autoDestroy: true,
        model: 'Overview',
        proxy: {
            type: 'ajax',
            url: url.remote,
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
	
	var overview_chart = Ext.create('Ext.Panel', {
		id: 'overview_chart',
		flex: 1,
		width: '100%',
		height: 400,
        hidden: false,
		border: false,
        title: 'Kontostand / Mannschaftswert',
        //renderTo: 'first_chart',
        layout: 'fit',
		items: {
            xtype: 'chart',
			theme: 'Category1',
            style: 'background:#fff',
            animate: true,
            store: store_overview,
            shadow: true,
            //theme: 'Category1',
            legend: {
                position: 'bottom'
            },
            axes: [{
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
            }],
            series: [{
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
			}]
        }		
	});
	
	store_overview.load();
	
	
});