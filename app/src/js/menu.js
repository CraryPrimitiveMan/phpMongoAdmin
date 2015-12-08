var React = require('react')
var rest = require('./rest')
var config = require('./config')
var EventBus = require('./event-bus');
var ReactContextMenu = require('react-contextmenu')
var ContextMenu = ReactContextMenu.ContextMenu;
var MenuItem = ReactContextMenu.MenuItem;

//The context-menu to be triggered
var Menu = React.createClass({
    render: function() {
        var self = this;
        return (
          <ContextMenu identifier={this.props.id} currentItem={this.currentItem}>
            {this.props.items.map(function(item, idx) {
              if (!item.label) {
                return (<MenuItem key={idx} divider />);
              } else {
                return (
                  <MenuItem key={idx} data={{action: item.action}} onSelect={self.handleSelect}>
                    {item.label}
                  </MenuItem>
                );
              }
            })}
          </ContextMenu>
        );
    },
    handleSelect: function(data) {
      this[data.target][data.action](data);
    },
    connection: {
      refresh: function(data) {

      },
      create: function(data) {
        //Create database
      },
      disconnect: function(data) {

      }
    },
    database: {
      refresh: function(data) {
        //refresh collection
      },
      create: function(data) {
        console.log(data);
        //Create collection
        var server = data.extras.server;
        var db = data.extras.db;
        var collection = window.prompt('Collection name');
        var path = `${config.domain}collection/create/${server}/${db}/${collection}`
        rest.post(path, {}, function(){
          data.extras.collection = collection;
          EventBus.pub('collectionCreated', data.extras);
        });
      }
    },
    collection: {
      view: function(data) {
        //View document
        EventBus.pub('collectionSelected', {
          name: data.name,
          extras: data.extras
        });
      },
      insert: function(data) {
        //Insert document
        var cmd = `db.${data.name}.insert(\n\t{\n\t\t"key":"value"\n\t}\n);`;
        EventBus.pub('exeCmdInModal', {
          action: 'Insert document',
          cmd: cmd,
          server: data.extras.server,
          db: data.extras.db
        });
      },
      update: function(data) {
        //Update document
        var cmd = `db.${data.name}.update(\n\t{\n\t\t"key":"value"\n\t},\n\t{\n\t}\n);`;
        EventBus.pub('exeCmdInModal', {
          action: 'Update document',
          cmd: cmd,
          server: data.extras.server,
          db: data.extras.db
        });
      },
      remove: function(data) {
        //Remove document
        var cmd = `db.${data.name}.remove(\n\t{\n\t\t"key":"value"\n\t}\n);`;
        EventBus.pub('exeCmdInModal', {
          action: 'Remove document',
          cmd: cmd,
          server: data.extras.server,
          db: data.extras.db
        });
      },
      drop: function(data) {
        //Drop collection d
        var server = data.extras.server;
        var db = data.extras.db;
        var collection = data.name;
        var path = `${config.domain}collection/delete/${server}/${db}/${collection}`
        rest.del(path, {}, function(){
          EventBus.pub('collectionDropped', data.extras)
        });
      }
    }
});

module.exports = Menu
