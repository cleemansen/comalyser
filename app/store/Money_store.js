Ext.define('com5000.store.Money_store', {
    extend: 'Ext.data.Store',
    model: 'com5000.model.Money_model',
    //autoLoad: true,

    proxy: {
        type: 'jsonp',
        url: 'http://futzi.net/comunio/5000/ws/home_overview.php',
        reader: {
            type: 'json',
            root: 'data',
            idProperty: 'date',
            totalProperty: 'total'
        }
    }
});