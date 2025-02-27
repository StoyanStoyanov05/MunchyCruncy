import { useState, useRef, useCallback } from "react";
import { useInfiniteQuery, useQuery} from "@tanstack/react-query";
import axios from "axios";
import { Link } from "react-router-dom";

const fetchRecipes = async ({ pageParam = 1, ingredients = [] }) => {
    const url =
        ingredients.length > 0
            ? `http://localhost:8000/api/v1/recipes/search?${ingredients
                .map((ing) => `ingredients[]=${ing}`)
                .join("&")}&page=${pageParam}`
            : `http://localhost:8000/api/v1/recipes?page=${pageParam}`;

    const response = await axios.get(url);
    return response.data;
}; 

    const fetchIngredients = async () => {
    const response = await axios.get(
        "http://localhost:8000/api/v1/ingredients?all=true");
    return response.data;
};

function HomePage() {
    const [selectedIngredients, setSelectedIngredients] = useState([]);
    const [searchTerm, setSearchTerm] = useState("");

    // Fetch ingredients
    const { data: ingredientsData } = useQuery({
        queryKey: ["ingredients"],
        queryFn: fetchIngredients,
    });

    // Fetch recipes

    const {
        data,
        fetchNextPage,
        hasNextPage,
        isFetchingNextPage,
        refetch,
    } = useInfiniteQuery({
        queryKey: ["recipes", selectedIngredients],
        queryFn: ({ pageParam }) => fetchRecipes({ pageParam, ingredients: selectedIngredients }),
        getNextPageParam: (lastPage) => {
            const nextPage = lastPage.meta?.current_page + 1;
            return lastPage.meta?.last_page >= nextPage ? nextPage : undefined;
        },
    });

    const observer = useRef();
    const lastRecipeRef = useCallback(
        (node) => {
            if (isFetchingNextPage) return;
            if (observer.current) observer.current.disconnect();
            observer.current = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && hasNextPage) {
                    fetchNextPage();
                }
            });
            if (node) observer.current.observe(node);
        },
        [isFetchingNextPage, hasNextPage, fetchNextPage]
    );

    // Handle ingredient selection
    const toggleIngredient = (ingredient) => {
        setSelectedIngredients((prev) =>
            prev.includes(ingredient)
                ? prev.filter((i) => i !== ingredient)
                : [...prev, ingredient]
        );
    };

    // Refetch recipes when ingredients change
    const handleIngredientChange = () => {
        refetch();
    };

    return (
        <div className="flex">
            {/* Sidebar Panel */}
            <div className="w-64 p-4 bg-gray-100 border-r h-screen overflow-y-auto">
                <h2 className="text-xl font-bold mb-4">Filter by Ingredients</h2>
                <input
                    type="text"
                    placeholder="Search ingredients..."
                    className="w-full p-2 mb-3 border rounded"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                />
                <div className="space-y-2">
                    {ingredientsData?.data
                        .filter((ing) =>
                            ing.name.toLowerCase().includes(searchTerm.toLowerCase())
                        )
                        .map((ingredient) => (
                            <div key={ingredient.id} className="flex items-center">
                                <input
                                    id={ingredient.id}
                                    type="checkbox"
                                    className="mr-2"
                                    checked={selectedIngredients.includes(ingredient.name)}
                                    onChange={() => {
                                        toggleIngredient(ingredient.name);
                                        handleIngredientChange();
                                    }}
                                />
                                <label htmlFor={ingredient.id}>
                                    {ingredient.name}
                                </label>
                            </div>
                        ))}
                </div>
            </div>
            {/* Main Content */}
            <div className="flex-1 p-6">
            <h1 className="text-3xl font-bold mb-6 text-center">Recipes</h1>
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                {data?.pages.map((page, pageIndex) =>
                    page.data.map((recipe, index) => {
                        const isLast =
                            pageIndex === data.pages.length - 1 &&
                            index === page.data.length - 1;
                        return (
                            <div
                                key={recipe.id}
                                ref={isLast ? lastRecipeRef : null}
                                className="border rounded-lg shadow-lg overflow-hidden"
                            >
                            <Link to={`/recipe/${recipe.id}`}>
                                    <img
                                        src={
                                            `http://127.0.0.1:8000/images/${recipe.imageUrl}`}
                                        alt={recipe.title}
                                        className="w-full h-48 object-cover"
                                    />
                                <div className="p-4">
                                    <h2 className="text-xl font-semibold text-center">
                                    {recipe.title}
                                    </h2>     

                                </div>                  
                            </Link>
                        </div>
                        );
                    })
                )}
            </div>
            {isFetchingNextPage && <p className="text-center mt-4">Loading more...</p>}
        </div>
    </div>
    );
    
}

export default HomePage;