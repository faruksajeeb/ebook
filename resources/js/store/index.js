import { createStore } from "vuex";
import api from "../api"; // Import your API service
// Create a new store instance.
const store = createStore({
    state() {
        return {
            message: "welcome",
            option_groups: [],
            categories: [],
            permissions: [],
            roles: [],
            authors: [],
            publishers: [],
            user: {
                permissions: [],
            },
        };
    },
    getters: {
        getPemissions: (state) => {
            api.fetchPermissions().then((permissions) => {
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
            api.fetchRoles().then((roles) => {
                state.roles = roles;
            });
            return state.roles;
        },
        getAuthors: (state) => {
            api.fetchAuthors().then((authors) => {
                state.authors = authors;
            });
            return state.authors;
        },
        getPublishers: (state) => {
            api.fetchPublishers().then((publishers) => {
                state.publishers = publishers;
            });
            return state.publishers;
        },
    },
    mutations: {
        setUserPermissions(state, permissions) {
            state.user.permissions = permissions;
        },
        setRoles(state, roles) {
            state.user.roles = roles;
        },
        setOptionGroups(state, option_groups) {
            state.option_groups = option_groups;
        },
        setCategories(state, categories) {
            state.categories = categories;
        },
    },
    actions: {
        fetchUserPermissions({ commit }) {
            // Replace this with the actual API call to fetch user permissions
            // e.g., using axios or fetch
            api.fetchUserPermissions().then((permissions) => {
                commit("setUserPermissions", permissions);
            });
        },
        fetchRoles({ commit }) {
            // e.g., using axios or fetch
            api.fetchRoles().then((roles) => {
                commit("setRoles", roles);
            });
        },
        fetchOptionGroups({ commit }) {
            // e.g., using axios or fetch
            api.fetchOptionGroups().then((option_groups) => {
                commit("setOptionGroups", option_groups);
            });
        },
        fetchCategories({ commit }) {
            // e.g., using axios or fetch
            api.fetchCategories().then((categories) => {
                commit("setCategories", categories);
            });
        },
    },
});
export default store;
