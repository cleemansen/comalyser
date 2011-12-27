Ext.require([
	'Ext.chart.*',
	'Ext.grid.*',
	'Ext.toolbar.*',
    'Ext.button.*',
    'Ext.container.ButtonGroup',
	'Ext.data.*'
]);

Ext.application({
    name: 'com5000',

    appFolder: 'app',
	
	controllers: [
        'Team'
    ],

    launch: function() {
		var colors = ['rgb(255, 0, 0)',
                  'rgb(0, 255, 255)',
                  'rgb(234, 102, 17)',
                  'rgb(154, 176, 213)',
                  'rgb(186, 10, 25)',
                  'rgb(40, 40, 40)'];

		Ext.chart.theme.Browser = Ext.extend(Ext.chart.theme.Base, {
			constructor: function(config) {
				Ext.chart.theme.Base.prototype.constructor.call(this, Ext.apply({
					colors: colors
				}, config));
			}
		});
		
		Ext.onReady(function() {
			var money_store = Ext.data.StoreManager.lookup('Money_store');
			var team_store = Ext.getStore('Team_store');
			//var price_store = Ext.getStore('Price_store');
			
			money_store.load({params: {uid: Ext.get('uid').dom.innerHTML}});
			team_store.load({params: {uid: Ext.get('uid').dom.innerHTML}});
			
			//http://stackoverflow.com/questions/2430899/extjs-dynamic-grid
			/*Ext.data.JsonP.request({
               url: 'http://futzi.net/comunio/5000/ws/home_values.php?uid=26',
               success: function(response, request) {                               
                    //alert('Success');                                                   
                    showGrid(response,request);
               },
               failure: function(results, request) {
                    alert('Error');
               }//,
               //params: {uid: Ext.get('uid').dom.innerHTML}
			});*/
			
			// NOT IN USE!
			function showGrid(response, request) {                  
				//var jsonData = Ext.JSON.decode(response.responseText);                 
				/*var grid = Ext.getCmp('contentGrid'+request.params.owner);
		
				if(grid) {
					grid.destroy();                                 
				} */
		
				var store = new Ext.data.ArrayStore({
					id  : 'arrayStore',                 
					fields : response.fields,
					autoDestroy : true
				});
				
				store.sort('date', 'ASC');
				
				price_chart = Ext.getCmp('teamChartPrice');
				price_chart.store = store;
				price_chart.axes.items[0].fields = response.chart_fields;
				price_chart.series.items[0].yField = response.chart_fields;
				
				price_chart.redraw();
				
				/*
				grid = new Ext.grid.GridPanel({
					defaults: {sortable:true},
					id:'contentGrid'+request.params.owner,
					store: store,        
					columns: jsonData.columns,
					//width:540,
					//height:200,
					loadMask: true
				}); */        
		
		
				store.loadData(response.data);
				/*
				if(Ext.getCmp('tab-'+request.params.owner)) {
					Ext.getCmp('tab-'+request.params.owner).show();
				} else {                
					grid.render('grid-div');
					Ext.getCmp('card-tabs-panel').add({
					id:'tab-'+request.params.owner,
					title: request.params.text,
					iconCls:'silk-tab',
					html:Ext.getDom('grid-div').innerHTML,
					closable:true
					}).show();          
				}*/
			}
			
			/*
			price_store.on('load',function(store,records,opts){
				alert('load');
                console.log(store.getRange());
            });*/
			
			//price_store.load({params: {uid: Ext.get('uid').dom.innerHTML}});
		});
		
		Ext.create('Ext.panel.Panel', {
            layout: 'vbox',
			border: true,
			renderTo: 'home',//document.body,
			height: 800,
			width: '100%',
			minHeight: 700,
			//title: 'Comalyser 5000',
			items: [{
				layout: 'accordion',
				width: '100%',
				height: 350,
				resizable: true,
				items: [
				{
					title: 'Spielerentwicklung seit dessen Einkauf',
					xtype: 'teamPanelChangeBuy'
				},{
					title: 'Spielerentwicklung seit gestern',
					xtype: 'teamPanelChangeYesterday'
				}/*,{
					title: 'Wertentwicklung Teamübersicht',
					xtype: 'teamPanelPrice'
				}*/,{
					title: 'Wertentwicklung Einzelspieler',
					xtype: 'kickerPanel'
				}]
            },{
				width: '100%',
				//minHeight: 500,
				flex: 1,
				layout: {type: 'hbox', align: 'stretch'},
				border: false,
				margin: '8 0 0 0',
				items: [{
					layout: 'fit',
					flex: 0.6,
					xtype: 'teamGrid',
					margin: '0 10 0 0',
					border: true,
					resizable: false
				},{
					flex: 0.4,
					layout: 'fit',
					border: true,
					xtype: 'moneyPanel',
					resizable: false
				}]
			}]
        });
    }
});