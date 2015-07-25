var React = require('react')
var TreeView = require('react-treeview')
var $ = require('jquery')
var EventBus = require('./event-bus')

var ConnenctionList = React.createClass({

  getInitialState: function() {
    return {connenctions: []};
  },

  componentDidMount: function() {
    var self = this
    $.get('/api/index.php?r=server/index', function(servers){
      var connenctions = []
      for (idx in servers) {
        connenctions.push({
          name: servers[idx].name,
          collapsed: true,
          dbs: []
        })
      }
      self.setState({
        connenctions: connenctions
      });
    })
  },

  handleSeverClick: function(idx) {
    var selectedConnection = this.state.connenctions[idx]
    if (!selectedConnection.dbs.length) {
      $.get('/api/index.php?r=database/index/' + selectedConnection.name, function(dbs){
        selectedConnection.dbs = dbs.map(function(db){
          return {
            name: db,
            collapsed: true,
            collections: []
          }
        })
        this.setState({
          connenctions: this.state.connenctions
        })
      }.bind(this))
    }
  },

  handleDBClick: function(conIdx, dbIdx) {
    selectedConnection = this.state.connenctions[conIdx]
    selectedDb = selectedConnection.dbs[dbIdx]
    if (!selectedDb.collections.length) {
      $.get('/api/index.php?r=collection/index/' + [selectedConnection.name, selectedDb.name].join('/'), function(collections){
        selectedDb.collections = collections.map(function(collection){
          return {
            name: collection,
            collapsed: true
          }
        })
        this.setState({
          connenctions: this.state.connenctions
        })
      }.bind(this))
    }
  },

  handleCollectionClick: function(colName) {
    EventBus.pub('collectionSelected', colName)
  },

  render: function() {
    return (
      <nav className="tree-wrap">
        {this.state.connenctions.map(function(con, i) {
          var name = con.name
          var label = <span className="node">{name}</span>;
          var collapsed = con.collapsed;
          var self = this;
          return (
            <TreeView key={name + '|' + i} nodeLabel={label} defaultCollapsed={collapsed} onClick={self.handleSeverClick.bind(self, i)}>
              {con.dbs.map(function(db, j) {
                var dbName = db.name
                var label = <span className="node">{dbName}</span>;
                var collapsed = db.collapsed;
                return (
                  <TreeView nodeLabel={label} key={dbName} defaultCollapsed={collapsed} onClick={self.handleDBClick.bind(self, i, j)}>
                    {db.collections.map(function(collection, k) {
                      var colName = collection.name;
                      return (
                        <div key={colName + '|' + k} onDoubleClick={self.handleCollectionClick.bind(self, colName)} className="item">{colName}</div>
                      );
                    })}
                  </TreeView>
                );
              })}
            </TreeView>
          );
        }, this)}
      </nav>
    );
  }
});

module.exports = ConnenctionList;