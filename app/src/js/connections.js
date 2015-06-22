var React = require('react')
var TreeView = require('react-treeview')

var ConnenctionList = React.createClass({
  getInitialState: function() {
    return {connenctions: []};
  },
  componentDidMount: function() {
    this.setState({
      connenctions: [
        {
          name: 'Local',
          collapsed: true,
          dbs: [
            {name: 'test', collapsed: false, collections: [{name:'user'},{name:'books'}]},
            {name: 'aug', collapsed: false, collections: [{name:'user'},{name:'books'}]},
          ]
        },
        {
          name: 'Stage',
          collapsed: true,
          dbs: [
            {name: 'test', collapsed: false, collections: [{name:'user'},{name:'books'}]}
          ]
        }
      ]
    });
  },
  render: function() {
    return (
      <nav className="tree-wrap">
        {this.state.connenctions.map(function(con, i) {
          var name = con.name
          var label = <span className="node">{name}</span>;
          var collapsed = con.collapsed;
          return (
            <TreeView key={name + '|' + i} nodeLabel={label} defaultCollapsed={collapsed}>
              {con.dbs.map(function(db) {
                var dbName = db.name
                var label = <span className="node">{dbName}</span>;
                var collapsed = db.collapsed;
                return (
                  <TreeView nodeLabel={label} key={dbName} defaultCollapsed={collapsed}>
                    {db.collections.map(function(collection, k) {
                      var colName = collection.name;
                      return (
                        <div key={colName + '|' + k} className="item">{colName}</div>
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