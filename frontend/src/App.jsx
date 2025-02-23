import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import HomePage from "./components/HomePage";
import AboutPage from "./components/AboutPage";
import RegisterPage from "./components/RegisterPage";
import Navbar from "./components/layout/Navbar";
import LoginPage from "./components/Login";

import './App.css';

function App() {
  return (
    <Router>
        <Navbar />
        <div className="pt-16"> { }
            <Routes>
                <Route path="/" element={<HomePage />} />
                <Route path="/about" element={<AboutPage />} />
                <Route path="/register" element={<RegisterPage />} />
                <Route path="/login" element={<LoginPage />} />

            </Routes>
        </div>
    </Router>
);
}

export default App;
