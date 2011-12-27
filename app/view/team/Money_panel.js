Ext.define('com5000.view.team.Money_panel' ,{
    extend: 'Ext.panel.Panel',
    alias : 'widget.moneyPanel',

    title : 'Kontostand / Mannschaftswert',
	
	store: 'Money_store',
	border: false,

	layout: 'fit',

    initComponent: function() {
		
		this.items = {
			xtype: 'moneyChart'	
		};
		
        this.callParent(arguments);
    }
});