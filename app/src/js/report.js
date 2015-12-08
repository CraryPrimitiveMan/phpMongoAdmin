var React = require('react');
var ReactTabs = require('react-tabs');
var EventBus = require('./event-bus');
var rest = require('./rest');
var config = require('./config');
var Tab = ReactTabs.Tab;
var Tabs = ReactTabs.Tabs;
var TabList = ReactTabs.TabList;
var TabPanel = ReactTabs.TabPanel;
var JsonTree = require('react-json-tree');
var Menu = require('./menu')
var ContextMenuLayer = require('react-contextmenu').ContextMenuLayer
var config = require('./config')

var brace  = require('brace');
var AceEditor  = require('react-ace');
require('brace/mode/java')
require('brace/theme/github')

const SPACES = '\u00a0\u00a0\u00a0\u00a0';

var TabTitle = ContextMenuLayer("tab", function(props){
  return props;
})(React.createClass({
  render: function(){
    return(
      <span className="node" target={this.props.target}>{this.props.name}</span>
    )
  }
}));

var Report = React.createClass({

  getInitialState: function() {
    return {
      tabs:[],
      selectedIndex: 0
    };
  },

  componentDidMount: function() {
    EventBus.sub('collectionSelected', function(doc){
      tabs = this.state.tabs;
      cmd = 'db.' + doc.name + '.find();';
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

  handleChange: function(idx, value) {
    this.state.tabs[idx].cmd = value;
    this.setState({
      tabs: this.state.tabs
    });
  },

  handleSelect: function (index, last) {
    console.log('Selected tab: ' + index + ', Last tab: ' + last);
  },

  handleExecute: function(tab, cmd) {
    var query = tab.cmd ? tab.cmd : cmd;
    var path = config.domain + 'database/execute/' + tab.config.server + '/' + tab.config.db;
    rest.post(path, {code: query.replace(/\n|\s|\t/g,'')}, function(resp){
      this.state.tabs[tab.idx].results = resp[0].retval;
      this.setState({
        tabs: this.state.tabs
      });
    }.bind(this));
  },

  getItemString: function (type, data, itemType, itemString) {
    itemString = SPACES + itemString.replace('key', 'field') + SPACES + type
    data._id && (itemString = data._id + itemString)
    return itemString
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
                  <AceEditor
                    mode="javascript"
                    theme="github"
                    onChange={this.handleChange.bind(this, idx)}
                    name="command"
                    value={tab.cmd}
                    height="150px"
                    width="100%"
                    className="command" />
                  <JsonTree
                    data={tab.results}
                    getLabelStyle={(type, expanded) => ({paddingRight:'10px'})}
                    getItemString={this.getItemString}
                    keyName={'records'} />
                </TabPanel>
              );
          }, this)}
        </Tabs>
      </section>
    );
  }
});

module.exports = Report;
