<template>
  <div id="app">
    <vertical-nav></vertical-nav>
    <div>content
    </div>
  </div>
</template>

<script>
import VerticalNav from './components/VerticalNav'

export default {
  components: {
    VerticalNav
  }
}
</script>

<style>
html, body, #app {
  margin: 0;
  padding: 0;
  height: 100%;
}

#app {
  font-family: Source Sans Pro, Helvetica, sans-serif;
  padding-left: 200px;
}

.logo {
  width: 100px;
  height: 100px
}
</style>
