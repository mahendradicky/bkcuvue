import { getLocalUser } from "../../helpers/auth";
import Axios from "axios";

const user = getLocalUser();

export const auth = {
  namespaced: true,

  state: {
    currentUser: user,
    isLoggedIn: !!user,
    isLoading: false,
    isTokenExpired: false,
    authError: null,
    notification: {},
    unreadNotification:'',
    markNotifStat:'',
    tokenExp: null,
    redirect: '/',
    isFromLogin: false,
  },

  getters: {
    isLoading: state => state.isLoading,
    isLoggedIn: state => state.isLoggedIn,
    isTokenExpired: state => state.isTokenExpired,
    currentUser: state => state.currentUser,
    authError: state => state.authError,
    notification: state => state.notification,
    unreadNotification: state => state.unreadNotification,
    markNotifStat: state => state.markNotifStat,
    tokenExp: state => state.tokenExp,
    redirect: state => state.redirect,
    isFromLogin: state => state.isFromLogin,
  },

  actions: {
    login({commit}){
      commit('setIsLoading', true);
      commit('setAuthError', null);
    },
    loginSuccess({ commit, state }, payload){
      commit('setAuthError', null);
      commit('setIsLoggedIn', true);
      commit('setIsLoading', false);
      commit('setCurrentUser', Object.assign({}, payload.user, {token: payload.access_token}));
      localStorage.setItem("user", JSON.stringify(state.currentUser));
      commit('setIsTokenExpired', false);
      commit('isFromLogin',true);
    },
    loginFailed({ commit, state }, payload){
      commit('setIsLoading', false);
      commit('setAuthError', payload.error);
    },
    logoutTokenExpired({ commit, state }){
      commit('setIsTokenExpired', true);
    },
    logout({ commit, state }){
      localStorage.removeItem("user");
      commit('setIsLoggedIn', false);
      commit('setCurrentUser', null);
    },
  },

  mutations: {
    setIsLoading ( state, data ){
      state.isLoading = data;
    },
    setAuthError ( state, data ){
      state.authError = data;
    },
    setIsLoggedIn ( state, data ){
      state.isLoggedIn = data;
    },
    setIsTokenExpired ( state, data ){
      state.isTokenExpired = data;
    },
    setCurrentUser ( state, data ){
      state.currentUser = data;
    },
    setNotification ( state, data ){
      state.notification = data;
    },
    setUnreadNotification ( state, data ){
      state.unreadNotification = data;
    },
    setMarkNotifStat( state, status ){
      state.markNotifStat = status;
    },
    setTokenExp( state, data ){
      state.tokenExp = data;
    },
    setRedirect( state, data ){
      state.redirect = data;
    },
    isFromLogin( state, data ){
      state.isFromLogin = data;
    },
  } 
}