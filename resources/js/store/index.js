import { createStore } from "vuex";
import api from "../api"  // Import your API service
// Create a new store instance.
const store = createStore({
    state() {
        return {
            message: "welcome",
            permissions: [],
            user: {
                permissions: [],
              },
        };
    },
    getters: {
        getPemissions: (state) => {
            api.fetchPermissions().then( (permissions) => {
                // console.log(state.permissions);
                state.permissions = permissions;
            });
            // console.log(state.permissions);
            let formatedPermission = state.permissions.map((permission) => {
                const str = `${permission.name}`;
                const formatedName = str.charAt(0).toUpperCase() + str.slice(1);
                return {
                    id: permission.id,
                    permission_name: formatedName,
                };
            });
            return formatedPermission;
        },
    },
    mutations: {
        setUserPermissions(state, permissions) {
            state.user.permissions = permissions;
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
    },
});
export default store;
