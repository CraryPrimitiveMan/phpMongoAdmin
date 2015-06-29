var React = require('react')
var Header = require('./header')
var ConnectionList = require('./connections')
var Report = require('./report')

var appElement = document.getElementById('container');

var App = React.createClass({
  render: function() {
    return (
      <div className="wrapper">
        <Header />
        <section className="content">
          <ConnectionList/>
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