// api.js

import axios from "axios";
const token = localStorage.getItem('token');

axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

const apiClient = axios.create({
    baseURL: "/api", // Adjust the base URL as per your API configuration
});

export default {
    fetchUserPermissions() {
        const userId = User.id();
        return apiClient.get(`/user/${userId}/permissions`).then((response) => {
            return response.data.permissions;
        });
    },
    fetchPermissions() {
        return apiClient.get("/get-permissions").then(function (response) {
            return response.data;
        });
    },
    fetchRoles() {
        return apiClient.get("/get-roles").then(function (response) {
            return response.data;
        });
    },
    fetchOptionGroups() {
        return apiClient.get("/get-option-groups").then(function (response) {
            return response.data;
        });
    },
    fetchCategories() {
        return apiClient.get("/get-categories").then(function (response) {
            return response.data;
        });
    },
};
