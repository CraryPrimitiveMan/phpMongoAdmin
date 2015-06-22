var React = require('react');

var Header = React.createClass({
  render: function() {
    return (
      <header className="header">
        <span className="logo">PHPMongo Admin</span>
        <ul className="btn-group">
          <li>New Connecion</li>
        </ul>
      </header>
    );
  }
});

module.exports = Header;