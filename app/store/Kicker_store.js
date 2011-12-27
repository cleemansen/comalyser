Ext.define('com5000.store.Kicker_store', {
    extend: 'Ext.data.Store',
    model: 'com5000.model.Kicker_model',
    //autoLoad: true,

    proxy: {
        type: 'jsonp',
        url: 'http://futzi.net/comunio/5000/ws/home_single_kicker.php',
        reader: {
            type: 'json',
            root: 'data',
            idProperty: 'date',
            totalProperty: 'total'
        }
    }
});