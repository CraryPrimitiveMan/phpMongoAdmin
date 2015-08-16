var React = require('react')
var ReactContextMenu = require('react-contextmenu')
var ContextMenu = ReactContextMenu.ContextMenu;
var MenuItem = ReactContextMenu.MenuItem;

//The context-menu to be triggered
var Menu = React.createClass({
    render: function() {
        var self = this;
        return (
          <ContextMenu identifier={this.props.id} currentItem={this.currentItem}>
            {this.props.items.map(function(item, idx) {
              if (!item.label) {
                return (<MenuItem key={idx} divider />);
              } else {
                return (
                  <MenuItem key={idx} data={{target:item.target}} onSelect={self.handleSelect}>
                    {item.label}
                  </MenuItem>
                );
              }
            })}
          </ContextMenu>
        );
    },
    handleSelect: function(data, item) {
        console.log(data, item);
    }
});

module.exports = Menu
