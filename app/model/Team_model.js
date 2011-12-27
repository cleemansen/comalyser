Ext.define('com5000.model.Team_model', {
    extend: 'Ext.data.Model',
    fields: [
	{
        name: 'update',
        type: 'date',
		dateFormat: 'Y-m-d'
    }, {
        name: 'name'
    }, {
        name: 'club'
    }, {
        name: 'pos'
    }, {
        name: 'points',
        type: 'int'
    }, {
        name: 'price',
        type: 'int'
    }, {
        name: 'purchase_price',
        type: 'int'
    }, {
        name: 'change',
        type: 'int'
    }, {
        name: 'purchase_date',
        type: 'date',
		dateFormat: 'Y-m-d'
    }, {
        name: 'change_absolute',
        type: 'int'
    }, {
        name: 'change_yesterday',
        type: 'int'
    }, {
        name: 'change_absolute_yesterday',
        type: 'int'
    }, {
        name: 'status'
    }]
});