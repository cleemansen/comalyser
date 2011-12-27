Ext.define('com5000.view.team.Team_panel_price' ,{
    extend: 'Ext.panel.Panel',
    alias : 'widget.teamPanelPrice',
	
	border: false,

	layout: 'fit',

    initComponent: function() {
		
		this.items = {
			xtype: 'teamChartPrice'	
		};
		
        this.callParent(arguments);
    }
});