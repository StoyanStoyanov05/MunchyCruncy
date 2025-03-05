import React, { useEffect, useState } from "react";
import { Route, Navigate } from "react-router-dom";
import Cookies from "js-cookie";
import { useNavigate } from "react-router-dom";
import { useAuthCheck } from './../utils/authUtils';  // Import useAuthCheck hook
import { toast } from "react-toastify";

// PrivateRoute component to protect routes that need authentication
const PrivateRoute = ({ element: Component, ...rest }) => {
    const [isAuthenticated, setIsAuthenticated] = useState(false);
    const navigate = useNavigate();
    const { checkAuth } = useAuthCheck();  // Use the hook for checking authentication

    useEffect(() => {
        // Check if the user is authenticated when the component mounts
        checkAuth();
    }, [checkAuth]);

    // Conditionally render the protected route or redirect to login
    if (!isAuthenticated) {
        // If not authenticated, you can display a loading state or directly redirect
        return <Navigate to="/login" />;
    }

    return (
        <Route
            {...rest}
            element={<Component />}
        />
    );
};

export default PrivateRoute;