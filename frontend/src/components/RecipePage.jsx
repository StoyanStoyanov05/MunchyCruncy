import { useEffect, useState, useMemo } from "react";
import { useParams } from "react-router-dom";
import axios from "axios";
import Cookies from "js-cookie";
import { FaStar } from "react-icons/fa";

const RecipePage = () => {
    const { id } = useParams(); // Get recipe ID from URL
    const [recipe, setRecipe] = useState(null);
    const [loading, setLoading] = useState(true);
    const [userRating, setUserRating] = useState(null);
    const [hoverRating, setHoverRating] = useState(null);

    // Retrieve the current user from cookies
    const user = useMemo(() => {
        return Cookies.get("user") ? JSON.parse(Cookies.get("user")) : null;
    }, []);

    useEffect(() => {
        const fetchRecipe = async () => {
            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/v1/recipes/${id}`);
                setRecipe(response.data.data);

                // Find the user's existing rating
                if (user) {
                    const userRatingData = response.data.data.ratings.find(
                        (rating) => rating.user_id === user.id
                    );
                    if (userRatingData) {
                        setUserRating(userRatingData.rating);
                    }
                }
            } catch (error) {
                console.error("Error fetching recipe:", error);
            } finally {
                setLoading(false);
            }
        };
        fetchRecipe();
    }, [id, user]);

    // Handle Star Click (Update or Create Rating)
    const handleRating = async (selectedRating) => {
        if (!user) {
            alert("You must be logged in to rate.");
            return;
        }

        try {
            // Send the rating update request to the correct endpoint
            await axios.post(
                `http://127.0.0.1:8000/api/v1/recipes/${id}/ratings/update-or-create`,
                {
                    user_id: user.id,
                    rating: selectedRating,
                }
            );

            // Update rating state
            setUserRating(selectedRating);

            // Refetch the recipe to update the average rating
            const updatedRecipe = await axios.get(`http://127.0.0.1:8000/api/v1/recipes/${id}`);
            setRecipe(updatedRecipe.data.data);
        } catch (error) {
            console.error("Error submitting rating:", error);
        }
    };

    if (loading) {
        return <p className="text-center text-gray-500">Loading recipe...</p>;
    }

    if (!recipe) {
        return <p className="text-center text-red-500">Recipe not found.</p>;
    }

    return (
        <div className="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg">
            {/* Recipe Header */}
            <div className="flex flex-col md:flex-row items-center gap-6">
                {/* Left: Recipe Image */}
                <img
                    src={
                        "http://127.0.0.1:8000/images/" + recipe.imageUrl ||
                        "/placeholder.jpg"}
                    alt={recipe.title}
                    className="w-64 h-64 object-cover rounded-lg shadow-lg"
                />

                {/* Right: Recipe Details */}
                <div className="flex-1">
                    <h1 className="text-3xl font-bold text-gray-800">{recipe.title}</h1>
                    <p className="text-gray-600 mt-2">{recipe.description}</p>

                    {/* Ratings Section */}
                    <div className="flex items-center mt-4">
                        {[1, 2, 3, 4, 5].map((star) => (
                            <FaStar
                                key={star}
                                onClick={() => handleRating(star)}
                                onMouseEnter={() => setHoverRating(star)}
                                onMouseLeave={() => setHoverRating(null)}
                                className={`w-8 h-8 cursor-pointer transition-colors duration-200 
                ${((hoverRating || userRating || Math.round(recipe.averageRating)) >= star)
                                        ? "text-yellow-400"
                                        : "text-gray-300"} 
                border-2 border-gray-300 rounded-full p-1`} // Adds circular border and padding
                            />
                        ))}
                        <span className="ml-2 text-gray-700">
                            {recipe.averageRating ? recipe.averageRating.toFixed(1) : "No Ratings"}
                        </span>
                    </div>
                </div>
            </div>

            {/* Instructions & Ingredients */}
            <div className="mt-6">
                {/* Ingredients */}
                <div className="mb-4">
                    <h2 className="text-xl font-semibold text-gray-800">Ingredients:</h2>
                    <ul className="list-disc list-inside text-gray-700 mt-2">
                        {recipe.ingredients.map((ingredient) => (
                            <li key={ingredient.id}>{ingredient.name}</li>
                        ))}
                    </ul>
                </div>

                {/* Instructions */}
                <div>
                    <h2 className="text-xl font-semibold text-gray-800">Instructions:</h2>
                    <p className="text-gray-700 mt-2 whitespace-pre-line">{recipe.instructions}</p>
                </div>
            </div>
        </div>
    );
};

export default RecipePage;
