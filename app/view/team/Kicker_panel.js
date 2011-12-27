Ext.define('com5000.view.team.Kicker_panel' ,{
    extend: 'Ext.panel.Panel',
    alias : 'widget.kickerPanel',
	
	border: false,

	layout: 'fit',

    initComponent: function() {
		
		this.items = {
			xtype: 'kickerChart'	
		};
		
        this.callParent(arguments);
    }
});