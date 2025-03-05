import React, { useState, useEffect, useMemo, useRef } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';
import { useNavigate, useParams } from 'react-router-dom';
import { FaPlus, FaMinus, FaUpload } from 'react-icons/fa';
import { toast, ToastContainer } from 'react-toastify';
import { useAuthCheck } from '../../utils/authUtils';

const RecipeForm = ({ isEdit = false }) => {

    if (isEdit) {
        useAuthCheck();
    }

    const [formData, setFormData] = useState({
        title: '',
        description: '',
        instructions: '',
        image_url: '',
        ingredients: []  // Store the list of ingredients added to the recipe
    });
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const [ingredientSuggestions, setIngredientSuggestions] = useState([]);
    const [activeInputIndex, setActiveInputIndex] = useState(null);
    const [searchInput, setSearchInput] = useState('');

    const navigate = useNavigate();
    const { id } = useParams(); // Used for editing an existing recipe

    const user = useMemo(() => {
        return Cookies.get('user') ? JSON.parse(Cookies.get('user')) : null;
    }, []);

    const authToken = Cookies.get("auth_token");

    useEffect(() => {
        if (!user) {
            navigate('/login'); // Redirect to login if user is not logged in
            return;
        }

        if (isEdit && id) {
            const fetchRecipe = async () => {
                try {
                    const response = await axios.get(
                        `http://127.0.0.1:8000/api/v1/recipes/${id}`
                    );

                    setFormData({
                        title: response.data.data.title,
                        description: response.data.data.description,
                        instructions: response.data.data.instructions,
                        image_url: "http://127.0.0.1:8000/images/" + response.data.data.imageUrl,
                        ingredients: response.data.data.ingredients || []  // Assuming ingredients are returned in response
                    });
                } catch (err) {
                    setError('Failed to fetch recipe.');
                }
            };

            fetchRecipe();
        }
    }, [isEdit, id, user, navigate]);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData(prevState => ({
            ...prevState,
            [name]: value
        }));
    };

    const handleAddIngredient = (ingredient) => {
        setFormData(prevState => {
            console.log("Previous Ingredients:", prevState.ingredients);

            // Ensure ingredients array is initialized correctly
            const updatedIngredients = prevState.ingredients ? [...prevState.ingredients] : [];

            // Check if ingredient already exists to prevent duplicates
            if (updatedIngredients.some(i => i.id === ingredient.id)) {
                toast.error("Duplicate ingredient, not adding.");
                return prevState;  // Don't add duplicate
            }

            updatedIngredients.push(ingredient);
            console.log("Updated Ingredients:", updatedIngredients);

            return {
                ...prevState,
                ingredients: updatedIngredients
            };
        });

        setIngredientSuggestions([]); // Clear suggestions after selection
    };

    const handleRemoveIngredient = async (ingredId) => {
        const ingredientId = ingredId;
        const recipeId = id;

        setLoading(true);

        try {
            const response = await axios.delete(
                `http://127.0.0.1:8000/api/v1/recipes/${recipeId}/ingredients/${ingredientId}`
                ,{
                    headers: { 'Authorization': `Bearer ${authToken}` }
                });

            // Remove the ingredient from the state after successful deletion
            setFormData((prevData) => ({
                ...prevData,
                ingredients: prevData
                    .ingredients
                    .filter(ing => ing.id !== ingredientId)
            }));

            toast.success("Ingredient removed successfully!");
        } catch (error) {
            toast.error(error.response?.data?.message || "Failed to remove ingredient");
        } finally {
            setLoading(false);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setLoading(true);

        try {
            const recipeData = {
                ...formData,
                user_id: user.id,
                ingredients: formData.ingredients.map(ingredient => ingredient.id) // Convert objects to IDs
            };

            if (recipeData.image_url.includes("127.0.0.1")) {
                delete recipeData.image_url;
            }

            if (isEdit && id) {
                await axios.put(`http://127.0.0.1:8000/api/v1/recipes/${id}`,
                     recipeData
                     ,{ headers: { 'Authorization': `Bearer ${authToken}` } });
            } else {
                await axios.post('http://127.0.0.1:8000/api/v1/recipes',
                     recipeData,
                     { headers: { 'Authorization': `Bearer ${authToken}` } }
                    );
            }

            navigate(`/my-recipes`);
        } catch (err) {
            setError('Failed to save recipe.');
        } finally {
            setLoading(false);
        }
    };

    const fetchIngredients = async (query) => {
        if (query.length < 2) {
            setIngredientSuggestions([]);  // Clear suggestions when query is too short
            return;
        }

        try {
            const response = await axios.get(
                'http://127.0.0.1:8000/api/v1/ingredients', {
                params: { search: query }
            });
            setIngredientSuggestions(response.data.data);
        } catch (err) {
            console.error('Error fetching ingredients:', err);
        }
    };

    const [dragging, setDragging] = useState(false);
    const fileInputRef = useRef(null); // Reference to the hidden file input

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            convertToBase64(file);
        }
    };

    const handleDrop = (e) => {
        e.preventDefault();
        setDragging(false);
        const file = e.dataTransfer.files[0];
        if (file) {
            convertToBase64(file);
        }
    };

    const convertToBase64 = (file) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            setFormData({ ...formData, image_url: reader.result });
        };
    };

    return (
        <div className="container mx-auto p-4">
            <ToastContainer></ToastContainer>

            <h1 className="text-2xl font-bold mb-6">
                {isEdit ? "Edit Recipe" : "Create New Recipe"}
            </h1>

            {error && (
                <div className="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {error}
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
                {/* Recipe Title */}
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Recipe Title</label>
                    <input
                        type="text"
                        name="title"
                        value={formData.title}
                        onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                        className="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        required
                    />
                </div>

                {/* Description */}
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea
                        name="description"
                        value={formData.description}
                        onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                        className="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        rows="4"
                        required
                    />
                </div>

                {/* Instructions */}
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Instructions</label>
                    <textarea
                        name="instructions"
                        value={formData.instructions}
                        onChange={(e) => setFormData({ ...formData, instructions: e.target.value })}
                        className="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        rows="4"
                        required
                    />
                </div>

                {/* Drag & Drop Image Upload */}
                <div
                    className={`border-2 border-dashed p-6 rounded-md flex flex-col items-center justify-center cursor-pointer ${dragging ? "border-blue-500 bg-blue-50" : "border-gray-300"
                        }`}
                    onDragOver={(e) => {
                        e.preventDefault();
                        setDragging(true);
                    }}
                    onDragLeave={() => setDragging(false)}
                    onDrop={handleDrop}
                    onClick={() => fileInputRef.current.click()} // Click opens file dialog
                >
                    <FaUpload size={32} className="text-gray-500" />
                    <p className="mt-2 text-gray-600">Drag & Drop to Upload Image</p>
                    <input
                        type="file"
                        accept="image/*"
                        className="hidden"
                        ref={fileInputRef}
                        onChange={handleImageChange} />
                </div>

                {/* Image Preview */}
                {formData.image_url && (

                    <div className="mt-4 flex justify-center">
                        <img src={formData.image_url}
                            alt="Preview"
                            className="max-w-xs rounded-md shadow" />
                    </div>
                )}

                {/* Ingredient Search and Add */}
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Ingredients</label>
                    <div className="space-y-2">
                        {formData.ingredients.map((ingredient, index) => (
                            <div key={index} className="flex items-center gap-2">
                                <span>{ingredient.name}</span>
                                <button
                                    type="button"
                                    onClick={() => handleRemoveIngredient(ingredient.id)}
                                    className="text-red-600 hover:text-red-800"
                                >
                                    <FaMinus size={18} />
                                </button>
                            </div>
                        ))}
                        <div className="relative">
                            <input
                                type="text"
                                placeholder="Search or add an ingredient"
                                className="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                onChange={(e) => {
                                    fetchIngredients(e.target.value);
                                    setActiveInputIndex(0);  // Activate the input field when typing
                                }}
                            />
                            {ingredientSuggestions.length > 0 && (
                                <ul className="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg z-10 max-h-48 overflow-y-auto">
                                    {ingredientSuggestions.map((ingredient) => (
                                        <li
                                            key={ingredient.id}
                                            className="p-2 hover:bg-gray-100 cursor-pointer"
                                            onClick={() => handleAddIngredient(ingredient)}
                                        >
                                            {ingredient.name}
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </div>
                    </div>
                </div>

                {/* Submit Button */}
                <div>
                    <button
                        type="submit"
                        className="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                        disabled={loading}
                    >
                        {loading ? "Saving..." : isEdit ? "Update Recipe" : "Create Recipe"}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default RecipeForm;