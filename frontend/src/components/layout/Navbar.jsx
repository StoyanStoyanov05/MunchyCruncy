import React from 'react';
import { Link } from 'react-router-dom';

import logo from '../../assets/logoDiplomna.png';

function Navbar() {
    return (
        <nav className="fixed top-0 left-0 w-full text-white p-4 shadow-md z-50 navbarCustom">
            <div className="container mx-auto flex justify-between items-center">
                <div className="text-lg font-bold">
                    <Link to="/">
                        <img src={logo} alt="MyApp Logo" className="h-8" />
                    </Link>
                </div>
                <div className="space-x-4">
                    <Link to="/" className="hover:text-gray-300">Home</Link>
                    <Link to="/about" className="hover:text-gray-300">About</Link>

                    <Link to="/login" className="hover:text-gray-300">Login</Link> {/* Added Login link */}
                    <Link to="/register" className="hover:text-gray-300">Register</Link>
                </div>
            </div>
        </nav>
    );
}

export default Navbar;