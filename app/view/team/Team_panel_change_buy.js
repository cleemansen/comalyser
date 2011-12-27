Ext.define('com5000.view.team.Team_panel_change_buy' ,{
    extend: 'Ext.panel.Panel',
    alias : 'widget.teamPanelChangeBuy',

    //title : 'Team Change',
	
	store: 'Team_store',
	border: false,

	layout: 'fit',
	lbar: [
		{
			enableToggle: true,
			allowDepress: false,
            text: 'Relativ',
			pressed: true,
			toggleGroup: 'bt_change_buy',
			toggleHandler: function(btn, pressed) {
                //var chart = Ext.getCmp('chartCmp');
                if(pressed) {
					var chart = Ext.getCmp ('teamChartChangeBuy');
					chart.axes.items[0].fields = 'change';
					
					var store = chart.store;
					
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
					
					chart.series.items[0].renderer = chart.change_relative_column_renderer;
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
					var chart = Ext.getCmp ('teamChartChangeBuy');
					
					var store = chart.store;
					chart.axes.items[0].fields = 'change_absolute';
					var min = store.min('change_absolute', false);
					var max = store.max('change_absolute', false);
					chart.axes.items[0].minimum = min;
					chart.axes.items[0].maximum = max;
					chart.series.items[0].renderer = chart.change_absolute_column_renderer;
					chart.series.items[0].yField = 'change_absolute';
					chart.series.items[0].label.field = 'change_absolute';
					chart.series.items[0].label.rotate = {degrees: 45};
					
					chart.redraw();
				}
            }
        }],

    initComponent: function() {
		
		this.items = {
			xtype: 'teamChartChangeBuy'	
		};
		
        this.callParent(arguments);
    }
});