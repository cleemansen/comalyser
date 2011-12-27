var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    });

Ext.define('com5000.view.team.Team_grid' ,{
    extend: 'Ext.grid.Panel',
    alias : 'widget.teamGrid',
	id: 'teamGrid',

    title : 'Team Grid',
	
	store: 'Team_store',
	
	autoScroll: true,
	
	layout: 'fit',
	
	border: false,
	
	columnLines: true,
	
	iconCls: 'icon-grid',
	
	frame: false,
	
	selModel: {
		selType: 'cellmodel'
	},
	
	plugins: [
        Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1
        })
    ],
	
	//selModel: girdSel,
	
	// inline buttons
	/*dockedItems: [{
		xtype: 'toolbar',
		items: [{
			text:'Add Something',
			tooltip:'Add a new row',
			iconCls:'add'
		}, '-', {
			text:'Options',
			tooltip:'Set options',
			iconCls:'option'
		},'-',{
			itemId: 'removeButton',
			text:'Remove Something',
			tooltip:'Remove the selected item',
			iconCls:'remove',
			disabled: true
		}]
	}],*/

    initComponent: function() {
		
		// example of custom renderer function
		this.cell_renderer4change_relative = function(value, metaData, record, rowIndex, colIndex, store) {
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
		};
	
		this.cell_renderer4change_absolute = function(value, metaData, record, rowIndex, colIndex, store) {
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
		};
		
		this.columns = [{
				dataIndex: 'update',
				text: 'Update',
				renderer: Ext.util.Format.dateRenderer('d.m.Y'),
				width: 66
			},
            {
                xtype: 'actioncolumn',
                width: 20,
                items: [{
                    getClass: function(v, meta, rec) {          // Or return a class from a function
                        if (rec.get('status') == 'fit') {
                            this.items[0].tooltip = 'keine Verletzungsmeldungen';
                            return 'fit';
                        } else {
                            this.items[0].tooltip = rec.get('status');
                            return 'not_fit';
                        }
                    },
                    handler: function(grid, rowIndex, colIndex) {
                        var rec = store.getAt(rowIndex);
                        alert((rec.get('change') < 0 ? "Hold " : "Buy ") + rec.get('company'));
                    }
                }]
            }, {
				dataIndex: 'name',
				header: 'Name',
				flex: 1,
				id: 'kicker_name'
			},{
				header: 'Notiz',
				xtype: 'checkcolumn',
				editor: 'checkbox',
				hidden: true,
				width: 35
			}, {
				dataIndex: 'club',
				text: 'Verein',
				flex: 1
			}, {
				dataIndex: 'pos',
				text: 'Position',
				align: 'center',
				width: 70
			}, {
				dataIndex: 'points',
				text: 'Punkte',
				align: 'right',
				width: 50,
				id: 'kicker_points'
			}, {
				dataIndex: 'price',
				text: 'Marktwert',
				align: 'right',
				renderer: Ext.util.Format.numberRenderer('0.000/i'),
				width: 70
			}, {
				dataIndex: 'change_absolute',
				text: '< &#177; >',
				filter: true,
				align: 'right',
				renderer: this.cell_renderer4change_absolute,
				id: 'kicker_change_absolute',
				width: 70
			}, {
				dataIndex: 'change',
				text: '< % >',
				filter: true,
				align: 'right',
				renderer: this.cell_renderer4change_relative,
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
			},{
                xtype: 'actioncolumn',
                width: 20,
                items: [{
                    icon   : 'delete.gif',  // Use a URL in the icon config
                    tooltip: 'Spieler verkauft',
                    handler: function(grid, rowIndex, colIndex) {
                        var rec = Ext.getStore('Team_store').getAt(rowIndex);
                        // Show a dialog using config options:
						Ext.Msg.show({
							title:'Spieler verkaufen?',
							msg: 'Bist du sicher, dass du '+rec.get('name')+' verkauft hast und ihn aus deinem Comalyse5000 Profil entfernen möchtest?',
							buttons: Ext.Msg.YESNO,
							fn: function(btn) {
								if(btn == 'yes') {
									//window.open('http://www.futzi.net/comunio/5000/ws/sell_kicker.php?uid='+Ext.get('uid').dom.innerHTML+'&kicker='+rec.get('name'), '_self');
									Ext.data.JsonP.request({
										url: 'http://www.futzi.net/comunio/5000/ws/sell_kicker.php',
										params: {
											uid: Ext.get('uid').dom.innerHTML,
											kicker: rec.get('name')
										},
										success: function(response){
											var team_store = Ext.getStore('Team_store');
											team_store.load({params: {uid: Ext.get('uid').dom.innerHTML}});
										}
									});
									
									//window.open('http://www.futzi.net/comunio/5000/index.php?site=home');
								}
							},
							//animateTarget: 'elId',
							icon: Ext.Msg.WARNING
						});
                    }
                }]
            }];

        this.callParent(arguments);
    }
});