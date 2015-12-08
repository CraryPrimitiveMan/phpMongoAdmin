var request = require('superagent');

rest = {
  get: function(path, cb) {
    request.get(path).end(function(err, res){
      err && console.error(err);
      cb(res.body);
    });
  }
};

['put', 'post', 'del'].forEach(function(method){
  rest[method] = (function(m){
    return function(path, data, cb) {
      request[m](path).send(data).end(function(err, res){
        err && console.error(err);
        cb(res.body);
      });
    }
  })(method);
})

module.exports = rest
