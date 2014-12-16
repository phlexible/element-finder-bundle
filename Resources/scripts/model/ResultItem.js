Phlexible.elementfinder.model.ResultItem = Ext.data.Record.create([
    'id',
    'version',
    'language',
    'elementtypeId',
    {name: 'customDate', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'publishedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    'sortField',
    {name: 'isRestricted', type: 'boolean'},
    {name: 'isPreview', type: 'boolean'},
    {name: 'inNavigation', type: 'boolean'},
    'extras',
    'title',
    'icon'
]);