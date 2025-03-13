import React, { useState, useEffect, useCallback, useMemo } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie'; // For managing cookies
import { useNavigate } from 'react-router-dom';
import { useAuthCheck } from '../../../utils/authUtils';

const MyRecipesPage = () => {
    useAuthCheck();

    const [recipes, setRecipes] = useState([]);
    const [error, setError] = useState('');
    const navigate = useNavigate();

    // Retrieve the current user and auth token from cookies
    const user = useMemo(() => {
        return Cookies.get('user') ? JSON.parse(Cookies.get('user')) : null;
    }, []);

    const authToken = Cookies.get("auth_token");

    const fetchRecipes = useCallback(async () => {
        if (!user || !authToken) {
            setError('Unauthorized access. Please log in.');
            navigate('/login');
            return;
        }

        try {
            const response = await axios.get(
                `http://127.0.0.1:8000/api/v1/recipes/user/${user.id}`,
                {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                }
            );
            setRecipes(response.data.data);
        } catch (err) {
            setError('No recipes found for this user');
        }
    }, [user, authToken, navigate]);

    useEffect(() => {
        if (!user || !authToken) {
            navigate('/login'); // Redirect to login if no user or token
            return;
        }

        fetchRecipes();
    }, [fetchRecipes, navigate, authToken]); // Re-fetch recipes whenever the user changes

    const handleEditRecipe = (recipeId) => {
        navigate(`/recipes/edit/${recipeId}`); // Navigate to the edit page for the selected recipe
    };

    const handleAddRecipe = () => {
        navigate(`/recipes/create`); // Navigate to the create new recipe page
    };

    // Handle deleting a recipe
    const handleDeleteRecipe = async (recipeId) => {
        if (!authToken) {
            setError('Unauthorized access. Please log in.');
            navigate('/login');
            return;
        }

        try {
            const response = await axios.delete(
                `http://127.0.0.1:8000/api/v1/recipes/${recipeId}`,
                {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                }
            );

            if (response.status === 200) {
                // Remove the deleted recipe from the local state
                setRecipes((prevRecipes) =>
                    prevRecipes.filter((recipe) => recipe.id !== recipeId)
                );
            }
        } catch (err) {
            setError('Failed to delete recipe.');
        }
    };

    return (
        <div className="container mx-auto p-4">
            <h1 className="text-2xl font-bold">Your Recipes</h1>
            {error && <p className="text-red-500">{error}</p>} {/* Display error if there's any */}

            <div className="mt-4">
                {/* Button to add new recipe */}
                <button
                    onClick={handleAddRecipe}
                    className="bg-blue-500 text-white py-2 px-4 rounded mb-4"
                >
                    Add New Recipe
                </button>

                {recipes.length === 0 ? (
                    <p>No recipes available.</p>
                ) : (
                    <ul className="space-y-3">
                        {recipes.map((recipe) => (
                            <li key={recipe.id} className="border-b pb-2 flex justify-between items-center">
                                <h2 className="text-xl font-semibold">{recipe.title}</h2>

                                {/* Edit button for each recipe */}
                                <button
                                    onClick={() => handleEditRecipe(recipe.id)}
                                    className="text-blue-500 mt-2"
                                >
                                    Edit Recipe
                                </button>

                                {/* Delete button for each recipe */}
                                <button
                                    onClick={() => handleDeleteRecipe(recipe.id)}
                                    className="text-red-500 mt-2 ml-2"
                                >
                                    Delete Recipe
                                </button>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </div>
    );
};

export default MyRecipesPage;