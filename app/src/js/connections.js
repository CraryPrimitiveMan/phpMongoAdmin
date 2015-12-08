var React = require('react')
var TreeView = require('react-treeview')
var rest = require('./rest');
var EventBus = require('./event-bus')
var config = require('./config')
var ContextMenuLayer = require('react-contextmenu').ContextMenuLayer

//Component on which context-menu must be triggred
var LabelTitle = React.createClass({
  render: function(){
    return(
      <span className="node" target={this.props.target}>{this.props.name}</span>
    )
  }
});

var ConnectionTitle = ContextMenuLayer("connection", function(props){
  return props;
})(LabelTitle);

var DatabaseTitle = ContextMenuLayer("database", function(props){
  return props;
})(LabelTitle);

var CollectionTitle = ContextMenuLayer("collection", function(props) {
  return props;
})(React.createClass({
  handleCollectionClick: function(collection, extras) {
    EventBus.pub('collectionSelected', {
      name: collection,
      extras: extras
    })
  },
  render: function() {
    return(
      <div key={this.props.key} onDoubleClick={this.handleCollectionClick.bind(this, this.props.name, this.props.extras)} className="item ">{this.props.name}</div>
    )
  }
}));

//Connenction List
var ConnectionList = React.createClass({

  getInitialState: function() {
    return {connenctions: []};
  },

  componentDidMount: function() {
    rest.get(config.domain + 'server/index', function(servers){
      var connenctions = []
      for (idx in servers) {
        connenctions.push({
          name: servers[idx].name,
          collapsed: true,
          dbs: []
        })
      }
      this.setState({
        connenctions: connenctions
      });
      EventBus.pub('connectionsLoaded', connenctions);
    }.bind(this));

    EventBus.sub('collectionCreated', function(data){
      var selectedConnection = this.state.connenctions[data.serverIdx];
      var selectedDb = selectedConnection.dbs[data.dbIdx];
      selectedDb.collections.unshift({
        name: data.collection,
        collapsed: false
      });
      this.setState({
        connenctions: this.state.connenctions
      })
    }.bind(this));

    EventBus.sub('collectionDropped', function(data){
      var selectedConnection = this.state.connenctions[data.serverIdx];
      var selectedDb = selectedConnection.dbs[data.dbIdx];
      selectedDb.collections.splice(data.collectionIdx, 1);
      this.setState({
        connenctions: this.state.connenctions
      })
    }.bind(this));
  },

  handleSeverClick: function(idx) {
    var selectedConnection = this.state.connenctions[idx]
    if (!selectedConnection.dbs.length) {
      var path = config.domain + 'database/index/' + selectedConnection.name;
      rest.get(path, function(dbs){
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

  handleDatabaseClick: function(conIdx, dbIdx) {
    var selectedConnection = this.state.connenctions[conIdx]
    var selectedDb = selectedConnection.dbs[dbIdx]
    if (!selectedDb.collections.length) {
      var path = config.domain + 'collection/index/' + [selectedConnection.name, selectedDb.name].join('/');
      rest.get(path, function(collections){
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

  render: function() {
    return (
      <nav className="tree-wrap">
        {this.state.connenctions.map(function(con, i) {
          var name = con.name;
          var label = <ConnectionTitle target="connection" name={name}/>;
          var collapsed = con.collapsed;
          var self = this;
          return (
            <TreeView key={name + '|' + i} nodeLabel={label} defaultCollapsed={collapsed} onClick={this.handleSeverClick.bind(this, i)}>
              {con.dbs.map(function(db, j) {
                var dbName = db.name;
                var extras = {
                  server: name,
                  db: dbName,
                  serverIdx: i,
                  dbIdx: j
                };
                var label = <DatabaseTitle target="database" name={dbName} extras={extras}/>;
                var collapsed = db.collapsed;
                return (
                  <TreeView nodeLabel={label} key={dbName} defaultCollapsed={collapsed} onClick={this.handleDatabaseClick.bind(this, i, j)}>
                    {db.collections.map(function(doc, k) {
                      var docName = doc.name;
                      var extras = {
                        server: name,
                        db: dbName,
                        serverIdx: i,
                        dbIdx: j,
                        collectionIdx: k
                      };
                      return (
                        <CollectionTitle key={docName + '|' + k} target="collection" name={docName} extras={extras}/>
                      );
                    })}
                  </TreeView>
                );
              }.bind(this))}
            </TreeView>
          );
        }, this)}
      </nav>
    );
  }
});

module.exports = ConnectionList;
