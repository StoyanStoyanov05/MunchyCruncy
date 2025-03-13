import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import Cookies from 'js-cookie'; // Import js-cookie to access stored cookies
import logo from '../../assets/logoDiplomna.png';
import { useLogout } from '../../utils/authUtils';

function Navbar() {
    const navigate = useNavigate();

    // Check if the token exists in cookies to identify if the user is logged in
    const token = Cookies.get('auth_token');
    let user = null;
    try {
        const userCookie = Cookies.get('user');
        if (userCookie) {
            user = JSON.parse(userCookie);  // Try parsing the cookie
        }
    } catch (e) {
        console.error("Error parsing user cookie:", e);
        user = null;  // If parsing fails, set user to null
    }

    const logout = useLogout(); 


    return (
        <nav className="fixed top-0 left-0 w-full text-white p-4 shadow-md z-50 navbarCustom">
            <div className="container mx-auto flex justify-between items-center">
                <div className="text-lg font-bold">
                    <Link to="/">
                        <img src={logo} alt="MyApp Logo" className="h-12" />
                    </Link>
                </div>
                <div className="space-x-4">
                    <Link to="/" className="hover:text-gray-300">Home</Link>
                    <Link to="/about" className="hover:text-gray-300">About</Link>

                    {token ? (
                        // If the user is logged in, show the user-specific menu
                        <>

                            <Link
                                to="/my-recipes"
                                className="hover:text-gray-300">
                                My Recipes
                            </Link>

                            <Link to="/shopping-list" className="hover:text-gray-300">
                                Shopping List
                            </Link>

                            <Link to="/profile" className="hover:text-gray-300">Profile</Link>
                            {user?.role === 1 && (
                                <Link to="/admin" className="hover:text-gray-300">Admin Dashboard</Link> // Example for admin users
                            )}
                            <button
                                onClick={logout}
                                className="hover:text-black-300 text-sm"
                            >
                                Logout
                            </button>
                        </>
                    ) : (
                        // If not logged in, show login and register options
                        <>
                            <Link to="/login" className="hover:text-gray-300">Login</Link>
                            <Link to="/register" className="hover:text-gray-300">Register</Link>
                        </>
                    )}
                </div>
            </div>
        </nav>
    );
}

export default Navbar;