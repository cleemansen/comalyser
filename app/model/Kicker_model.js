Ext.define('com5000.model.Kicker_model', {
    extend: 'Ext.data.Model',
    fields: [
	{
        name: 'date',
        type: 'date',
		dateFormat: 'Y-m-d'
    }, {
        name: 'price',
        type: 'int'
    }, {
        name: 'points',
        type: 'int'
    }]
});