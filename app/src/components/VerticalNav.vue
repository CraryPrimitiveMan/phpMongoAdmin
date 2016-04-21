<template>
  <div class="nav">
    <h1>{{ msg }}</h1>
  </div>
</template>

<script>
export default {
  data () {
    return {
      msg: 'Hello World!'
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.nav {
  width: 200px;
  background: #eee;
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  height: 100%;
  transition-duration: .3s;
}
h1 {
  color: #42b983;
}
</style>
