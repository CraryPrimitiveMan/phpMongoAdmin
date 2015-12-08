var React = require('react')
var Header = require('./header')
var ConnectionList = require('./connections')
var Menu = require('./menu')
var Report = require('./report')
var Modal = require('react-modal')
var EventBus = require('./event-bus')
var config = require('./config')
var rest = require('./rest')

var brace  = require('brace');
var AceEditor  = require('react-ace');
require('brace/mode/java')
require('brace/theme/github')

var appElement = document.getElementById('container');

var App = React.createClass({
  getInitialState: function() {
    return { 
      modal: {
        opened: false,
        action: '',
        cmd: ''
      }
    };
  },

  executeCmd: function() {
    var server = this.state.modal.server;
    var db = this.state.modal.db;
    var cmd = this.state.modal.cmd;
    var path = config.domain + 'database/execute/' + server + '/' + db;
    rest.post(path, {code: cmd.replace(/\n|\s|\t/g,'')}, function(resp){
      console.log(resp);
      this.setState({modal:{opened: false}})
      //resp[0].retval;
    }.bind(this));
  },

  closeModal: function() {
    this.setState({modal:{opened:false}});
  },

  componentDidMount: function() {
    EventBus.sub('exeCmdInModal', function(data){
      this.setState({
        modal: {
          opened: true,
          action: data.action,
          cmd: data.cmd,
          server: data.server,
          db: data.db
        }
      });
    }.bind(this));
  },

  render: function() {
    return (
      <div className="wrapper">
        <Header />
        <section className="content">
          <ConnectionList/>
          <Menu id="connection" items={config.menuAction.connection}/>
          <Menu id="database" items={config.menuAction.database}/>
          <Menu id="collection" items={config.menuAction.collection}/>
          <Modal
            isOpen={this.state.modal.opened}
            onRequestClose={this.closeModal}
            className="modal"
          >
            <h2>{this.state.modal.action}</h2>
            <i className="iconfont" onClick={this.closeModal}>&#xe65d;</i>
            <AceEditor
              mode="javascript"
              theme="github"
              name="command"
              value={this.state.modal.cmd}
              height="150px"
              width="100%"
              className="command" />
            <button onClick={this.executeCmd}>Execute</button>
          </Modal>
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
