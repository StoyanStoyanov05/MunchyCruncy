import React from "react";
import { FaFacebook, FaInstagram, FaTwitter } from "react-icons/fa";

const AboutPage = () => {
  return (
    <div className="w-full flex flex-col items-center">
      <div className="w-full max-w-7xl px-4 mx-auto">
        
        <section
          className="w-full min-h-screen flex flex-col items-center justify-center text-center text-white px-6 bg-cover bg-center relative"
          style={{
            backgroundImage: "url('/images/image_hero_about_page.jpg')",
            backgroundSize: "cover",
            backgroundPosition: "center",
            backgroundRepeat: "no-repeat"
          }}
        >
          <div className="relative z-10 max-w-3xl px-8 py-6 bg-black/30 backdrop-blur-md rounded-xl transition-transform duration-300 hover:translate-y-2">
            <h1 className="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white">
              About <span className="text-yellow-300">MunchyCrunchy</span>
            </h1>
            <p className="mt-4 text-lg sm:text-xl md:text-2xl text-gray-200">
              Your ultimate cooking assistant! Discover recipes based on your ingredients and explore new flavors effortlessly.
            </p>
            <a 
              href="#socials" 
              className="mt-6 bg-yellow-400 text-black px-6 py-3 rounded-xl font-semibold text-lg shadow-lg hover:bg-yellow-500 transition-all inline-block hover:translate-y-3"
            >
              Connect with Us
            </a>
          </div>
        </section>

        <footer className="w-full bg-yellow-400 py-6 px-6 text-center">
          <div className="max-w-7xl mx-auto">
            <h2 className="text-3xl font-semibold mb-4 text-[#5054D4]">Follow Us</h2>
            <div className="flex justify-center space-x-8">
              <a href="https://facebook.com" className="text-[#3b5998] text-4xl hover:translate-y-2 transition-transform duration-300">
                <FaFacebook />
              </a>
              <a href="https://instagram.com" className="text-[#C13584] text-4xl hover:translate-y-2 transition-transform duration-300">
                <FaInstagram />
              </a>
              <a href="https://twitter.com" className="text-[#00acee] text-4xl hover:translate-y-2 transition-transform duration-300">
                <FaTwitter />
              </a>
            </div>
          </div>
        </footer>

      </div>
    </div>
  );
};

export default AboutPage;