import { useState } from "react";
import axios from "axios";
import "../../index.css";
import { useNavigate } from "react-router-dom";
import Cookies from "js-cookie"; // Use js-cookie to manage cookies
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css"; // Import Toastify CSS

export default function LoginPage() {
    const [form, setForm] = useState({
        email: "willa29@example.com",
        password: "password"
    });
    const [error, setError] = useState("");
    const navigate = useNavigate();  // For redirecting after login

    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleLoginSubmit = async (e) => {
        e.preventDefault();
        setError("");

        if (!form.email || !form.password) {
            setError("All fields are required.");
            return;
        }

        try {
            // Make the login API call
            const response = await axios.post(
                "http://127.0.0.1:8000/api/v1/users/login", {
                email: form.email,
                password: form.password,
            });

            // If login is successful, store the token in cookies
            if (response.status === 200) {
                const { token, user } = response.data;
                Cookies.set(
                    "auth_token", token, {
                    expires: 7
                }); // Set token cookie for 7 days

                // Optionally, you can store the user info as well, but it's not mandatory
                Cookies.set("user", JSON.stringify(user), {
                    expires: 7
                });

                toast.success("Login successful!"); // Show success notification
                navigate("/"); // Redirect to the home page after successful login

            }
        } catch (error) {
            setError("Invalid credentials or server error.");
        }
    };

    const handleRegisterSubmit = () => {
        navigate("/register");  // Redirect to login page after successful registration
    };

    return (
        <div className="flex justify-center items-center min-h-screen bg-white-100">
            <div className="bg-white shadow-md rounded-lg p-6 w-96">
                <h2 className="text-2xl font-bold text-center mb-4">Login</h2>
                {error && <p className="text-red-500 text-sm text-center">{error}</p>}

                <form onSubmit={handleLoginSubmit} className="space-y-3">
                    <input
                        type="email"
                        name="email"
                        placeholder="Email"
                        value={form.email}
                        onChange={handleChange}
                        className="w-full p-2 border border-gray-300 rounded"
                    />
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        value={form.password}
                        onChange={handleChange}
                        className="w-full p-2 border border-gray-300 rounded"
                    />
                    <button type="submit" className="w-full bg-blue-500 text-black py-2 rounded hover:bg-blue-600">
                        Login
                    </button>
                </form>

                <div className="text-center mt-4">
                    <p>Don't have an account?</p>
                    <button
                        onClick={handleRegisterSubmit}
                        className="text-blue-500 hover:underline"
                    >
                        Register here
                    </button>
                </div>
            </div>

            <ToastContainer /> {/* Toast notifications container */}

        </div>
    );
}