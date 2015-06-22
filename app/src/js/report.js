var React = require('react');
var ReactTabs = require('react-tabs');
var Tab = ReactTabs.Tab;
var Tabs = ReactTabs.Tabs;
var TabList = ReactTabs.TabList;
var TabPanel = ReactTabs.TabPanel;

var Report = React.createClass({

  getInitialState: function() {
    return {tabs:['Foo','Bar','Baz']};
  },

  handleSelect: function (index, last) {
    console.log('Selected tab: ' + index + ', Last tab: ' + last);
  },

  render: function () {
    return (
      <section className="report">
        <Tabs onSelect={this.handleSelected} selectedIndex={2}>
          <TabList>
            {this.state.tabs.map(function(tab, idx){
              return (
                <Tab key={idx}>{tab}</Tab>
              );
            }, this)}
          </TabList>
          <TabPanel>
            <h2>Hello from Foo</h2>
          </TabPanel>
          <TabPanel>
            <h2>Hello from Bar</h2>
          </TabPanel>
          <TabPanel>
            <h2>Hello from Baz</h2>
          </TabPanel>
        </Tabs>
      </section>
    );
  }
});

module.exports = Report;