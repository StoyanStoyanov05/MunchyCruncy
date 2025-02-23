import { useState } from "react";
import axios from "axios";

export default function RegisterPage() {
    const [form, setForm] = useState({
        name: "", email: "", password: "", password_confirmation: ""
    });
    const [error, setError] = useState("");
    const [success, setSuccess] = useState("");

    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError("");
        setSuccess("");

        if (!form.name || !form.email || !form.password || !form.password_confirmation) {
            setError("All fields are required.");
            return;
        }

        if (form.password !== form.password_confirmation) {
            setError("Passwords do not match.");
            return;
        }

        try {
            const response = await axios.post("http://localhost:8000/api/v1/users", form);
            setSuccess("Registered successfully!");
            setForm({ name: "", email: "", password: "", password_confirmation: "" }); // Reset form
        } catch (error) {
            if (error.response && error.response.data.errors) {
                const errorMessages = Object.values(error.response.data.errors).flat().join(" ");
                setError(errorMessages);
            } else {
                setError("Registration failed. Please try again.");
            }
        }
    };

    return (
        <div className="flex justify-center items-center min-h-screen bg-gray-100">
            <div className="bg-white shadow-md rounded-lg p-6 w-96">
                <h2 className="text-2xl font-bold text-center mb-4">Register</h2>
                {error && <p className="text-red-500 text-sm text-center">{error}</p>}
                {success && <p className="text-green-500 text-sm text-center">{success}</p>}
                <form onSubmit={handleSubmit} className="space-y-3">
                    <input
                        type="text"
                        name="name"
                        placeholder="Name"
                        value={form.name}
                        onChange={handleChange}
                        className="w-full p-2 border border-gray-300 rounded"
                    />
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
                    <input
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirm Password"
                        value={form.password_confirmation}
                        onChange={handleChange}
                        className="w-full p-2 border border-gray-300 rounded"
                    />
                    <button type="submit"
                        className="w-full bg-blue-500 py-2 rounded hover:bg-blue-600">
                        Register
                    </button>
                </form>
            </div>
        </div>
    );
}