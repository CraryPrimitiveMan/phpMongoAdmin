module.exports = {
  domain: '/api/index.php?r=',
  menuAction: {
    connection: [
      {
        label: 'Refresh',
        target: 'refresh'
      },  
      {
        label: 'Create Database',
        target: 'create'
      },
      {
        label: 'Disconnect',
        target: 'disconnect'
      }
    ],
    collection: [
      {
        label: 'Refresh',
        target: 'refresh'
      },  
      {
        label: 'Create Collection',
        target: 'create'
      }
    ],
    document: [
      {
        label: 'View Document',
        target: 'view'
      },
      {
        label: 'Insert Document',
        target: 'insert'
      },  
      {
        label: 'Update Document',
        target: 'update'
      },
      {
        label: 'Remove Document',
        target: 'remove'
      }
    ],
  }
}
