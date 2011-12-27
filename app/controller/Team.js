Ext.define('com5000.controller.Team', {
    extend: 'Ext.app.Controller',
	
	views: [
        'team.Team_grid',
		'team.chart.Team_chart_change_buy',
		'team.Team_panel_change_buy',
		'team.chart.Team_chart_change_yesterday',
		'team.Team_panel_change_yesterday',
		'team.chart.Money_chart',
		'team.Money_panel',
		'team.chart.Team_chart_price',
		'team.Team_panel_price',
		'team.chart.Kicker_chart',
		'team.Kicker_panel',
		'team.CheckColumn'
    ],
	
	stores: ['Team_store', 'Money_store', 'Kicker_store'],
    
	models: ['Team_model', 'Money_model', 'Kicker_model'],

    init: function() {
		
		this.control({
            'teamGrid': {
                itemdblclick: this.onGridClick,
				itemclick: this.onGridClick,
				itemcontextmenu: this.onGridContextMenu
            },
			'viewport': {
                render: this.onPanelRendered
            }
        });
		
		//alert('blbldg ' + Ext.get('uid').getAttribute('uid'));
    },
	
	onGridClick : function(grid, record) {
		console.log('on grid click @' + record.data.name);
		var kicker_store = Ext.getStore('Kicker_store');
		
		kicker_store.load({params: {uid: Ext.get('uid').dom.innerHTML, name: record.data.name}});
		
		Ext.getCmp('kickerChart').redraw();
	},

    onPanelRendered: function() {
        alert("waaaaaa");
    },
	
	onGridContextMenu: function(grid, index, event) {
		//event.stopEvent();
		console.log('context');
		mnuContext.showAt(event.xy);
		event.preventDefault();
	}
});
