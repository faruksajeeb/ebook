// api.js

import axios from "axios";

const apiClient = axios.create({
    baseURL: "/api", // Adjust the base URL as per your API configuration
});

export default {
    fetchUserPermissions() {
        return apiClient.get("/user/permissions").then((response) => {
            return response.data.permissions;
        });
    },
    fetchPermissions() {
        return apiClient.get("/get-pemissions").then(function (response) {
            return response.data;
        });
    },fetchRoles() {
        return apiClient.get("/get-roles").then(function (response) {
            return response.data;
        });
    },
};
