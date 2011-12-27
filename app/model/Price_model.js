Ext.define('com5000.model.Price_model', {
    extend: 'Ext.data.Model',
    fields: [
	{
        name: 'date',
        type: 'date',
		dateFormat: 'Y-m-d'
    }, {
        name: 'team_value',
        type: 'int'
    }, {
        name: 'balance',
        type: 'int'
    }]
});