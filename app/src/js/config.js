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
    tab: [
      {
        label: 'New Tab',
        target: 'new'
      },
      {
        label: 'Re-execute Query',
        target: 'execute'
      },  
      {
        label: 'Duplicate Query in New Tab',
        target: 'duplicate'
      },
      {
        label: 'Close Current Tab',
        target: 'closecurrent'
      },
      {
        label: 'Close Other Tabs',
        target: 'closeothers'
      },
      {
        label: 'Close Tabs to The Right',
        target: 'closeright'
      }
    ]
  }
}
