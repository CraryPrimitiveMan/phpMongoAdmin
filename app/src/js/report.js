var React = require('react');
var ReactTabs = require('react-tabs');
var EventBus = require('./event-bus');
var ObejctInspector = require('react-object-inspector');
var $ = require('jquery');
var config = require('./config');
var Tab = ReactTabs.Tab;
var Tabs = ReactTabs.Tabs;
var TabList = ReactTabs.TabList;
var TabPanel = ReactTabs.TabPanel;
var JsonTable = require('react-json-table');
var Menu = require('./menu')
var ContextMenuLayer = require('react-contextmenu').ContextMenuLayer
var config = require('./config')

var TabTitle = ContextMenuLayer("tab", function(props){
  return props;
})(React.createClass({
  render: function(){
    return(
      <span className="node" target={this.props.target}>{this.props.name}</span>
    )
  }
}));

var Table = React.createClass({
  getInitialState: function(){
    // We will store the selected cell and row, also the sorted column
    return {row: false, cell: false, sort: false};
  },  
  render: function(){
    var self = this,
        // clone the rows
        items = this.props.rows.slice()
    ;
    // Sort the table
    if(this.state.sort){
      items.sort(function(a, b){
         return a[self.state.sort] - b[self.state.sort];
      });
    }
        
    return <JsonTable 
      rows={items} 
      settings={this.getSettings()} 
      onClickCell={this.onClickCell}
      onClickHeader={this.onClickHeader}
      onClickRow={this.onClickRow} />;
  },
  
  getSettings: function(){
      var self = this;
      // We will add some classes to the selected rows and cells
      return {
        keyField: 'name',
        cellClass: function(current, key, item){
          if(self.state.cell == key && self.state.row == item.name)
            return current + ' cellSelected';
          return current;
        },
        headerClass: function(current, key){
            if(self.state.sort == key)
              return current + ' headerSelected';
            return current;
        },
        rowClass: function(current, item){
          if(self.state.row == item.name)
            return current + ' rowSelected';
          return current;
        }
      };
  },
  
  onClickCell: function(e, column, item){
    console.log(arguments)
    this.setState({cell: column});
  },
  
  onClickHeader: function(e, column){
    console.log(column)
    this.setState({sort: column});
  },
  
  onClickRow: function(e, item){
    console.log(arguments)
    this.setState({row: item.name});
  }  
});

var Report = React.createClass({

  getInitialState: function() {
    return {
      tabs:[],
      selectedIndex: 0
    };
  },

  componentDidMount: function() {
    EventBus.sub('docSelected', function(doc){
      tabs = this.state.tabs;
      cmd = 'db.' + doc.name + '.find()';
      tabs.push({
        title: doc.name,
        config: doc.extras,
        cmd: cmd,
        results: '',
        idx: tabs.length
      });
      this.setState({
        tabs: tabs,
        selectedIndex: tabs.length - 1
      });

      state = this.state;
      this.handleExecute(state.tabs[state.selectedIndex], cmd);
    }.bind(this));

    EventBus.sub('exeCmd', function(){
      state = this.state;
      this.handleExecute(state.tabs[state.selectedIndex]);
    }.bind(this));
  },

  handleDelete: function(idx) {
    this.state.tabs.splice(idx, 1)
    this.setState({
      tabs: this.state.tabs
    });
  },

  handleChange: function(value, idx) {
    this.state.tabs[idx].cmd = value;
    this.setState({
      tabs: this.state.tabs
    });
  },

  handleSelect: function (index, last) {
    console.log('Selected tab: ' + index + ', Last tab: ' + last);
  },

  handleExecute: function(tab, cmd) {
    var textarea = React.findDOMNode(this.refs.query);
    var query = textarea ? textarea.value : cmd;
    $.ajax({
        dataType: 'json',
        type: 'post',
        url: config.domain + 'database/execute/' + tab.config.server + '/' + tab.config.db,
        data: JSON.stringify({code: query}),
        success: function(resp){
          this.state.tabs[tab.idx].results = resp[0].retval;
          this.setState({
            tabs: this.state.tabs
          });
        }.bind(this)
    });
  },

  render: function () {
    var self = this;
    return (
      <section className="report">
        <Tabs onSelect={this.handleSelected} selectedIndex={this.state.selectedIndex}>
          <TabList>
            {this.state.tabs.map(function(tab, idx){
              var extras = {
                idx: idx
              };
              return (
                <Tab key={idx}>
                  <i className="iconfont" onClick={this.handleDelete.bind(this, idx)}>&#xe65d;</i>
                  <TabTitle name={tab.title} extras={extras}/>
                  <Menu id="tab" items={config.menuAction.tab}/>
                </Tab>
              );
            }, this)}
          </TabList>
          {this.state.tabs.map(function(tab, idx){
              return (
                <TabPanel key={idx}>
                  <textarea className="cmd-input" ref="query" value={tab.cmd} onChange={this.handleChange.bind(this, tab.cmd, idx)}></textarea>
                  <Table rows={ tab.results }  />
                </TabPanel>
              );
          }, this)}
        </Tabs>
      </section>
    );
  }
});

module.exports = Report;
