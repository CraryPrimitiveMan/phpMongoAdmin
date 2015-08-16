var React = require('react')
var Header = require('./header')
var ConnectionList = require('./connections')
var Menu = require('./menu')
var Report = require('./report')
var config = require('./config')

var appElement = document.getElementById('container');

var App = React.createClass({
  render: function() {
    return (
      <div className="wrapper">
        <Header />
        <section className="content">
          <ConnectionList/>
          <Menu id="connection" items={config.menuAction.connection}/>
          <Menu id="collection" items={config.menuAction.collection}/>
          <Menu id="document" items={config.menuAction.document}/>
          <Report/>
        </section>        
      </div>
    );
  }
});

React.render(
  <App/>,
  appElement
);
