import Cookies from "js-cookie";
import { useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import { useEffect } from "react";

// Global logout function
export const useLogout = () => {
    const navigate = useNavigate();

    return () => {
        Cookies.remove("auth_token");
        Cookies.remove("user");
        toast.info("You have been logged out.");
        navigate("/login");  // Redirect to login page
    };
};

// Hook to check if user is authenticated
export const useAuthCheck = () => {
    const navigate = useNavigate();

    useEffect(() => {
        const authToken = Cookies.get("auth_token");
        const user = Cookies.get("user");

        if (!authToken || !user) {
            navigate("/login"); // Redirect if not authenticated
        }
    }, [navigate]);
};