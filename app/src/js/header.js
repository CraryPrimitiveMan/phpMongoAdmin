var React = require('react');
var Modal = require('react-modal');

Modal.setAppElement(document.getElementById('container'));
Modal.injectCSS();

var Header = React.createClass({
  getInitialState: function() {
    return { modalIsOpen: false };
  },

  openModal: function() {
    this.setState({modalIsOpen: true});
  },

  closeModal: function() {
    this.setState({modalIsOpen: false});
  },

  render: function() {
    return (
      <header className="header">
        <span className="logo">PHPMongo Admin</span>
        <ul className="btn-group">
          <li onClick={this.openModal} className="btn">New Connecion</li>
        </ul>
        <Modal
          isOpen={this.state.modalIsOpen}
          onRequestClose={this.closeModal}
        >
          <h2>New Connecion</h2>
          <button onClick={this.closeModal}>close</button>
        </Modal>
      </header>
    );
  }
});

module.exports = Header;