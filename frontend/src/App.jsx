import {
     BrowserRouter as Router,
     Routes,
     Route 
} from "react-router-dom";

import './App.css';

import HomePage from "./components/menu/HomePage";
import AboutPage from "./components/menu/AboutPage";
import RegisterPage from "./components/menu/RegisterPage";
import Navbar from "./components/layout/Navbar";
import LoginPage from "./components/menu/LoginPage";
import ProfilePage from "./components/menu/ProfilePage";

import ShoppingListPage from  "./components/menu/user-menu/ShopingListPage";

import ShoppingListForm from "./components/forms/ShoppingListForm";
import MyRecipesPage  from "./components/menu/user-menu/MyRecipesPage";
import RecipeForm from "./components/forms/RecipeForm";
import RecipePage from "./components/RecipePage";

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

                    {/* Logged in pages */} 
                    <Route path="/profile" element={<ProfilePage />} />
                    <Route path="/shopping-list" element={<ShoppingListPage />} />
                    <Route path="/my-recipes" element={<MyRecipesPage />} />

                    <Route path="/shopping-lists/:userId" element={<ShoppingListPage />} /> 
                    <Route path="/shopping-lists/:userId/edit/:id" element={<ShoppingListForm isEdit={true} />} />
                    <Route path="/shopping-lists/:userId/create" element={<ShoppingListForm />} />
                

                    <Route path="/recipes/edit/:id" element={<RecipeForm isEdit={true} />} />
                    <Route path="/recipes/create" element={<RecipeForm />} /> 

                    <Route path="/recipe/:id" element={<RecipePage />} />
                </Routes>
            </div>
        </Router>
    );
}

export default App;
