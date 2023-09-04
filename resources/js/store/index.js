import { createStore } from "vuex";
import axios from "axios";
// Create a new store instance.
const store = createStore({
    state() {
        return {
            message: "welcome",
            permissions: [],
        };
    },
    getters: {
        getPemissions: (state) => {
            axios.get("/api/get-pemissions").then(function (response) {
                // console.log(state.permissions);
                state.permissions = response.data;
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
        // increment (state) {
        //   state.count++
        // }
    },
    actions: {},
});
export default store;
