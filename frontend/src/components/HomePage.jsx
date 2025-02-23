import { useState, useRef, useCallback } from "react";
import { useInfiniteQuery } from "@tanstack/react-query";
import axios from "axios";

const fetchRecipes = async ({ pageParam = 1 }) => {
    const response = await axios.get(
        `http://localhost:8000/api/v1/recipes?page=${pageParam}`);
    return response.data;
};

function HomePage() {
    const {
        data,
        fetchNextPage,
        hasNextPage,
        isFetchingNextPage
    } = useInfiniteQuery({
        queryKey: ["recipes"],
        queryFn: fetchRecipes,
        getNextPageParam: (lastPage) => {
            const nextPage = lastPage.meta?.current_page + 1;
            return lastPage.meta?.last_page >= nextPage ? nextPage : undefined;
        }
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

    return (
        <div className="p-6">
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
                                <img src={recipe.image_url} alt={recipe.title} className="w-full h-48 object-cover" />
                                <div className="p-4">
                                    <h2 className="text-xl font-semibold text-center">{recipe.title}</h2>
                                </div>
                            </div>
                        );
                    })
                )}
            </div>
            {isFetchingNextPage && <p className="text-center mt-4">Loading more...</p>}
        </div>
    );
}

export default HomePage;