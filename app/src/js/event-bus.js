var topics = {};
var EventBus = {
  sub: function(topic, callback) {
    if (!topics[topic]) {
      topics[topic] = []
    }
    topics[topic].push(callback)
  },
  pub: function(topic, data) {
    if (topics[topic]) {
      topics[topic].forEach(function(callback){
        callback(data)
      })
    }
  },
  remove: function() {
    if (topics[topic]) {
      topics[topic] = []
    }
  }
}

module.exports = EventBus