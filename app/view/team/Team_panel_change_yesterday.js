Ext.define('com5000.view.team.Team_panel_change_yesterday' ,{
    extend: 'Ext.panel.Panel',
    alias : 'widget.teamPanelChangeYesterday',

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
			toggleGroup: 'bt_change_yesterday',
			toggleHandler: function(btn, pressed) {
                //var chart = Ext.getCmp('chartCmp');
                if(pressed) {
					var chart = Ext.getCmp ('teamChartChangeYesterday');
					chart.axes.items[0].fields = 'change_yesterday';
					
					var store = chart.store;
					
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
					
					chart.series.items[0].renderer = chart.change_relative_column_renderer;
					chart.series.items[0].yField = 'change_yesterday';
					chart.series.items[0].label.field = 'change_yesterday';
					chart.series.items[0].label.rotate.degrees = 0;
					
					chart.redraw();
				}
            }
        },{
            text: 'Absolut',
			enableToggle: true,
			allowDepress: true,
			toggleGroup: 'bt_change_yesterday',
            toggleHandler: function(btn, pressed) {
				if(pressed) {
					var chart = Ext.getCmp ('teamChartChangeYesterday');
					
					var store = chart.store;
					chart.axes.items[0].fields = 'change_absolute_yesterday';
					var min = store.min('change_absolute_yesterday', false);
					var max = store.max('change_absolute_yesterday', false);
					chart.axes.items[0].minimum = min;
					chart.axes.items[0].maximum = max;
					chart.series.items[0].renderer = chart.change_absolute_column_renderer;
					chart.series.items[0].yField = 'change_absolute_yesterday';
					chart.series.items[0].label.field = 'change_absolute_yesterday';
					chart.series.items[0].label.rotate = {degrees: 45};
					
					chart.redraw();
				}
            }
        }],

    initComponent: function() {
		
		this.items = {
			xtype: 'teamChartChangeYesterday'	
		};
		
        this.callParent(arguments);
    }
});