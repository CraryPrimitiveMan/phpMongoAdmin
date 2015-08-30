var React = require('react')
var TreeView = require('react-treeview')
var $ = require('jquery')
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

var CollectionTitle = ContextMenuLayer("collection", function(props){
  return props;
})(LabelTitle);

var DocumentTitle = ContextMenuLayer("document", function(props) {
  return props;
})(React.createClass({
  handledocClick: function(docName, extras) {
    EventBus.pub('docSelected', {
      name: docName,
      extras: extras
    })
  },
  render: function() {
    return(
      <div key={this.props.key} onDoubleClick={this.handledocClick.bind(this, this.props.name, this.props.extras)} className="item ">{this.props.name}</div>
    )
  }
}));

//Connenction List
var ConnectionList = React.createClass({

  getInitialState: function() {
    return {connenctions: []};
  },

  componentDidMount: function() {
    var self = this
    $.get(config.domain + 'server/index', function(servers){
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
      $.get(config.domain + 'database/index/' + selectedConnection.name, function(dbs){
        selectedConnection.dbs = dbs.map(function(db){
          return {
            name: db,
            collapsed: true,
            docs: []
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
    if (!selectedDb.docs.length) {
      $.get(config.domain + 'collection/index/' + [selectedConnection.name, selectedDb.name].join('/'), function(docs){
        selectedDb.docs = docs.map(function(doc){
          return {
            name: doc,
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
          var label = <ConnectionTitle target="con" name={name}/>;
          var collapsed = con.collapsed;
          var self = this;
          return (
            <TreeView key={name + '|' + i} nodeLabel={label} defaultCollapsed={collapsed} onClick={self.handleSeverClick.bind(self, i)}>
              {con.dbs.map(function(db, j) {
                var dbName = db.name;
                var label = <CollectionTitle target="db" name={dbName}/>;
                var collapsed = db.collapsed;
                return (
                  <TreeView nodeLabel={label} key={dbName} defaultCollapsed={collapsed} onClick={self.handleDBClick.bind(self, i, j)}>
                    {db.docs.map(function(doc, k) {
                      var docName = doc.name;
                      var extras = {
                        server: name,
                        db: dbName
                      };
                      return (
                        <DocumentTitle key={docName + '|' + k} name={docName} extras={extras}/>
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

module.exports = ConnectionList;
