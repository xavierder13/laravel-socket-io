import Axios from 'axios';
import router from '../../router';

const state = {
  user: {}
};

const getters = {};

const actions = {
  getUser({ commit, dispatch }) {
    Axios.get("/api/auth/init").then(
      (response) => {
        commit('setUser', response.data.user);
      },
      (error) => {
        // if unauthenticated (401)
        if (error.response.status == "401") {
          localStorage.removeItem("access_token");
          router.push('/login');
        }
      }
    );
  },
  login({}, data) {
    Axios.post("/api/auth/login", data).then(
      (response) => {
        if (response.data.access_token) {
          localStorage.setItem("access_token", response.data.access_token);
          router.push("/").catch((e) => {});
          // this.clear();
          this.email = null;
          this.password = null;
          this.overlay = false;
        } else {
          this.isInvalid = true;
          this.overlay = false;
        }
      },
      (error) => {
        console.log(error);
      }
    );
  }
};

const mutations = {
  setUser(state, data) {
    state.user = data;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
}