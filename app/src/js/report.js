var React = require('react');
var ReactTabs = require('react-tabs')
var EventBus = require('./event-bus')
var Tab = ReactTabs.Tab;
var Tabs = ReactTabs.Tabs;
var TabList = ReactTabs.TabList;
var TabPanel = ReactTabs.TabPanel;

var Report = React.createClass({

  getInitialState: function() {
    return {
      tabs:[],
      selectedIndex: 0
    };
  },

  componentDidMount: function() {
    EventBus.sub('docSelected', function(docName){
      tabs = this.state.tabs
      tabs.push({
        title: docName
      })
      this.setState({
        tabs: tabs,
        selectedIndex: tabs.length - 1
      })
    }.bind(this))
  },

  handleSelect: function (index, last) {
    console.log('Selected tab: ' + index + ', Last tab: ' + last);
  },

  render: function () {
    return (
      <section className="report">
        <Tabs onSelect={this.handleSelected} selectedIndex={this.state.selectedIndex}>
          <TabList>
            {this.state.tabs.map(function(tab, idx){
              return (
                <Tab key={idx}>{tab.title}</Tab>
              );
            }, this)}
          </TabList>
          {this.state.tabs.map(function(tab, idx){
              return (
                <TabPanel key={idx}>
                  
                </TabPanel>
              );
          }, this)}
        </Tabs>
      </section>
    );
  }
});

module.exports = Report;