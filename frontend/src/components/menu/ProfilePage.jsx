import React, { useState, useEffect } from "react";
import axios from "axios";
import Cookies from "js-cookie";
import { useNavigate } from "react-router-dom";
import { toast, ToastContainer } from "react-toastify";

function ProfilePage() {
    const navigate = useNavigate();
    const user = JSON.parse(Cookies.get("user")) || null;  // Get user info from cookies

    console.log(user);

    const [formData, setFormData] = useState({
        name: user?.name || "",
        email: user?.email || "",
        password: "",
        confirmPassword: "",
    });
    const [error, setError] = useState("");

    useEffect(() => {
        if (!user) {
            navigate("/login");  // Redirect to login if no user is found
        }
    }, [user, navigate]);

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError("");

        // Check if passwords match
        if (formData.password !== formData.confirmPassword) {
            setError("Passwords do not match.");
            return;
        }

        try {
            const response = await axios.put(
                `http://127.0.0.1:8000/api/v1/users/${user.id}`,
                {
                    name: formData.name,
                    email: formData.email,
                    password: formData.password,  // You can decide whether to send the password if it's not empty
                }
            );

            if (response.status === 200) {
                // Update cookies with the new user data
                Cookies.set("user", JSON.stringify(response.data.user), { expires: 7 });
                // Assuming response.data contains the updated user info
                const updatedUser = response.data.data;  // Extract the user from the response

                // Update cookies with the new user data
                Cookies.set("user", JSON.stringify(updatedUser), { expires: 7 });

                toast.success("Profile updated successfully.");
            }
        } catch (error) {
            setError("An error occurred while updating your profile.");
        }
    };

    return (
        <div className="flex justify-center items-center min-h-screen bg-gray-100">
            <div className="bg-white shadow-md rounded-lg p-6 w-96">
                <h2 className="text-2xl font-bold text-center mb-4">Edit Profile</h2>
                {error && <p className="text-red-500 text-sm text-center">{error}</p>}

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label htmlFor="name" className="block">Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value={formData.name}
                            onChange={handleChange}
                            className="w-full p-2 border border-gray-300 rounded"
                            required
                        />
                    </div>
                    <div>
                        <label htmlFor="email" className="block">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value={formData.email}
                            onChange={handleChange}
                            className="w-full p-2 border border-gray-300 rounded"
                            required
                        />
                    </div>
                    <div>
                        <label htmlFor="password" className="block">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            value={formData.password}
                            onChange={handleChange}
                            className="w-full p-2 border border-gray-300 rounded"
                            placeholder="Leave blank to keep current password"
                        />
                    </div>
                    <div>
                        <label htmlFor="confirmPassword" className="block">Confirm Password</label>
                        <input
                            type="password"
                            id="confirmPassword"
                            name="confirmPassword"
                            value={formData.confirmPassword}
                            onChange={handleChange}
                            className="w-full p-2 border border-gray-300 rounded"
                            placeholder="Leave blank to keep current password"
                        />
                    </div>
                    <button
                        type="submit"
                        className="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600"
                    >
                        Update Profile
                    </button>
                </form>

                <ToastContainer />
            </div>
        </div>
    );
}

export default ProfilePage;