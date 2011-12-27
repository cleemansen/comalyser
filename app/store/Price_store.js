Ext.define('com5000.store.Price_store', {
    extend: 'Ext.data.Store',
    model: 'com5000.model.Price_model',
    fields: ['date'],
    //autoLoad: true,

    proxy: {
        type: 'jsonp',
        url: 'http://futzi.net/comunio/5000/ws/home_values.php',
        reader: {
            type: 'json',
            root: 'data',
            idProperty: 'date',
            totalProperty: 'total'
        }
    }
});