<template>
  <button @click="getName()">button</button>
</template>

<script>

// client-side
const io = require("socket.io-client");
const socket = io("https://localhost:3000", {
  withCredentials: true,
  transportOptions: {
    polling: {
      extraHeaders: {
        "my-custom-header": "abcd"
      }
    }
  }
});
export default {
  data() {
    return {
      firstname: "xavierdEr",
    };
  },
  methods: {
    getName() {
      console.log(this.firstname);
      io.on("connection", (socket) => {
        socket.on("disconnect", () => {
          console.log("A user disconnected");
        });
      });
    },
  },
  mounted() {
    socket.on("newdata", fetchedData => {
        console.log(fetchedData);
      })
  },
};
</script>
