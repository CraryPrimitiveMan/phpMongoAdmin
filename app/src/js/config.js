module.exports = {
  domain: '/api/index.php?r=',
  menuAction: {
    connection: [
      {
        label: 'Refresh',
        action: 'refresh'
      },  
      {
        label: 'Create Database',
        action: 'create'
      },
      {
        label: 'Disconnect',
        action: 'disconnect'
      }
    ],
    database: [
      {
        label: 'Refresh',
        action: 'refresh'
      },  
      {
        label: 'Create Collection',
        action: 'create'
      }
    ],
    collection: [
      {
        label: 'View Document',
        action: 'view'
      },
      {
        label: 'Insert Document',
        action: 'insert'
      },  
      {
        label: 'Update Document',
        action: 'update'
      },
      {
        label: 'Remove Document',
        action: 'remove'
      },
      {
        action: ''
      },
      {
        label: 'Drop Collection',
        action: 'drop'
      }
    ],
    tab: [
      {
        label: 'New Tab',
        action: 'new'
      },
      {
        label: 'Re-execute Query',
        action: 'execute'
      },  
      {
        label: 'Duplicate Query in New Tab',
        action: 'duplicate'
      },
      {
        label: 'Close Current Tab',
        action: 'closecurrent'
      },
      {
        label: 'Close Other Tabs',
        action: 'closeothers'
      },
      {
        label: 'Close Tabs to The Right',
        action: 'closeright'
      }
    ]
  }
}
