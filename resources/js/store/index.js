import { createStore } from "vuex";
import api from "../api"  // Import your API service
// Create a new store instance.
const store = createStore({
    state() {
        return {
            message: "welcome",
            permissions: [],
            roles: [],
            user: {
                permissions: [
                   
                ],               
              },
        };
    },
    getters: {
        getPemissions: (state) => {
            api.fetchPermissions().then( (permissions) => {
                // console.log(state.permissions);
                state.permissions = permissions;
            });
            return state.permissions;
            // console.log(state.permissions);
            // let formatedPermission = state.permissions.map((permission) => {
            //     const str = `${permission.name}`;
            //     const formatedName = str.charAt(0).toUpperCase() + str.slice(1);
            //     return {
            //         id: permission.id,
            //         name: formatedName,
            //     };
            // });
            // return formatedPermission;
        },
        getRoles: (state) => {
            api.fetchRoles().then( (roles) => {               
               state.roles = roles;               
            });
            // let formatedPermission = state.roles.map((role) => {
            //     const str = `${role.name}`;
            //     const formatedName = str.charAt(0).toUpperCase() + str.slice(1);
            //     return {
            //         id: role.id,
            //         name: formatedName,
            //     };
            // });
            // return formatedPermission;
            // console.log(state.roles);
             return state.roles;
        },
    },
    mutations: {
        setUserPermissions(state, permissions) {
            state.user.permissions = permissions;
          },
          setRoles(state, roles) {
            state.user.roles = roles;
          },
    },
    actions: {
        fetchUserPermissions({ commit }) {
            // Replace this with the actual API call to fetch user permissions
            // e.g., using axios or fetch
            api.fetchUserPermissions().then((permissions) => {
              commit('setUserPermissions', permissions);
            });
        },
        fetchRoles({ commit }) {
            // e.g., using axios or fetch
            api.fetchRoles().then((roles) => {
              commit('setRoles', roles);
            });
        },
    },
});
export default store;
