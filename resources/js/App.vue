<template>
  <button @click="getData()">Click</button>
</template>

<script>
export default {
  data() {
    return {};
  },
  methods: {
    getData() {
      // console.log("clicked!");
      this.$socket.emit("sendData", "Message Sent!");
    },
  },
  mounted() {
    this.$options.sockets.sendData = (data) => {
      console.log(data);
    };
  },
};
</script>
