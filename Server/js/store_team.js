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

Ext.onReady(function(){
	var uid = Ext.get('uid').dom.innerHTML;
	
	// for this demo configure local and remote urls for demo purposes
	var url = {
		local:  'home-kicker.json',  // static data file
		remote: 'ws/home-kicker.php?uid=' + uid
	};
	
	// configure whether filter query is encoded or not (initially)
	var encode = true;
	
	// configure whether filtering is performed locally or remotely (initially)
	var local = false;
	
	Ext.create('Ext.data.JsonStore', {
		// store configs
		id: 'store_team',
		autoDestroy: true,
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
});