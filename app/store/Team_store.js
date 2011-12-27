Ext.define('com5000.store.Team_store', {
    extend: 'Ext.data.Store',
    model: 'com5000.model.Team_model',
    //autoLoad: true,
    //baseParams: {uid: 25},
    proxy: {
        type: 'jsonp',
        url: 'http://futzi.net/comunio/5000/ws/home-kicker.php',//+Ext.get('uid').dom.innerHTML,
        
        reader: {
            type: 'json',
            root: 'data',
            idProperty: 'name',
            totalProperty: 'total'
        }
    }
});