import Cookies from "js-cookie";
import { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { toast } from "react-toastify";

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

export const useAuthCheck = () => {
    const navigate = useNavigate();

    useEffect(() => {
        const authToken = Cookies.get("auth_token");
        const userCookie = Cookies.get("user");

        // If no auth token or user, redirect to login page
        if (!authToken || !userCookie) {
            navigate("/login");
            return;
        }

        const user = JSON.parse(userCookie);
        const userId = user.id; // Assuming 'user' cookie stores the user info with an id property

        // Fetch request to check user authentication by user_id
        fetch(`http://127.0.0.1:8000/api/v1/users/${userId}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${authToken}`, // Add the token for authentication
                'Content-Type': 'application/json',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    // If the response status is not ok (e.g., 401 or 403), show toast and redirect to login
                    if (response.status === 401) {
                        toast.error("Unauthorized access. Please log in again."); // Show toast for unauthorized access
                    }
                    navigate("/login");
                }
            })
            .catch((error) => {
                // If there is an error (e.g., network issues), show a generic error toast
                console.error(error);
                toast.error("An error occurred. Please try again.");
                navigate("/login");
            });
    }, [navigate]);
};