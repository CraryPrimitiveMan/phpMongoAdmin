var React = require('react');
var Modal = require('react-modal');
var EventBus = require('./event-bus');
var config = require('./config')
var rest = require('./rest');

Modal.setAppElement(document.getElementById('container'));
Modal.injectCSS();

var Header = React.createClass({
  getInitialState: function() {
    return { modalIsOpen: false };
  },

  componentDidMount: function() {
    EventBus.sub('connectionsLoaded', function(connections){
      this.connections = connections;
    }.bind(this));
  },

  execute: function() {
    EventBus.pub('exeCmd');
  },

  openModal: function() {
    this.setState({modalIsOpen: true});
  },

  closeModal: function() {
    this.setState({modalIsOpen: false});
  },

  handleSubmit: function(e) {
    e.preventDefault();
    var config = [];
    this.connections.forEach(function(connection){
      config.push({
        name: connection.name,
        dsn: 'localhost:27017'//TODO: pass it to the backend to change mongo connection
      })
    });
    var data = {
      config: [{
        name: this.refs.name.getDOMNode().value.trim(),
        dsn: this.refs.dsn.getDOMNode().value.trim() 
      }]
    };
    this.refs.name.getDOMNode().value = '';
    this.refs.dsn.getDOMNode().value = '';
    /*rest.put(config.domain + 'server/update', data, function(){
      console.log(arguments)
    })*/
    this.setState({modalIsOpen: false});
  },

  render: function() {
    return (
      <header className="header">
        <span className="logo">PHPMongo Admin</span>
        <ul className="btn-group">
          <li onClick={this.execute} className="btn">Execute</li>
          <li onClick={this.openModal} className="btn">New Connecion</li>
        </ul>
        <Modal
          isOpen={this.state.modalIsOpen}
          onRequestClose={this.closeModal}
          className="modal"
        >
          <h2>New Connecion</h2>
          <i className="iconfont" onClick={this.closeModal}>&#xe65d;</i>
          <form onSubmit={this.handleSubmit}>
            <label>Name:</label>
            <input type="text" placeholder="Please give your connection a name" ref="name"/>
            <br/>
            <label>DSN:</label>
            <input type="text" placeholder="Please provide correct DSN for the connection" ref="dsn"/>
            <br/>
            <input type="submit" value="Confirm"/>
          </form>
        </Modal>
      </header>
    );
  }
});

module.exports = Header;
