import React, { useState, useEffect, useCallback, useMemo } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie'; // For managing cookies
import { useNavigate } from 'react-router-dom';
import { useAuthCheck } from '../../../utils/authUtils';

const ShoppingListPage = () => {
    useAuthCheck();
    const [shoppingLists, setShoppingLists] = useState([]);
    const [error, setError] = useState('');
    const navigate = useNavigate();

    const user = useMemo(() => {
        return Cookies.get('user') ? JSON.parse(Cookies.get('user')) : null;
    }, []);

    const authToken = Cookies.get("auth_token");
    const fetchShoppingLists = useCallback(async () => {
        try {
            const response = await axios.get(
                `http://127.0.0.1:8000/api/v1/shopping-lists/${user.id}`,
                {
                    headers: {
                    'Authorization': `Bearer ${authToken}`
                    }
                    }
            );
            setShoppingLists(response.data.data);
        } catch (err) {
            setError('Failed to fetch shopping lists.');
        }
    }, [user]); // Only change when user changes

    useEffect(() => {
        if (!user) {
            navigate('/login');
            return;
        }

        fetchShoppingLists();
    }, [fetchShoppingLists, navigate]); // Ensures the function is stable

    const handleEditList = (listId) => {
        navigate(`/shopping-lists/${user.id}/edit/${listId}`); // Navigate to edit page for selected list
    };

    const handleAddList = () => {
        navigate(`/shopping-lists/${user.id}/create`); // Navigate to create new shopping list page
    };

    return (
        <div className="container mx-auto p-4">
            <h1 className="text-2xl font-bold">Your Shopping Lists</h1>
            {error && <p className="text-red-500">{error}</p>} {/* Display error if there's any */}

            <div className="mt-4">
                {/* Button to add new shopping list */}
                <button
                    onClick={handleAddList}
                    className="bg-blue-500 text-white py-2 px-4 rounded mb-4"
                >
                    Add New List
                </button>

                {shoppingLists.length === 0 ? (
                    <p>No shopping lists available.</p>
                ) : (
                    <ul className="space-y-3">
                        {shoppingLists.map((list) => (
                            <li key={list.id} className="border-b pb-2 flex justify-between items-center">
                                <h2 className="text-xl font-semibold">{list.name}</h2>

                                {/* Edit button for each list */}
                                <button
                                    onClick={() => handleEditList(list.id)}
                                    className="text-blue-500 mt-2"
                                >
                                    Edit List
                                </button>
                            </li>
                        ))}
                    </ul>

                )}
            </div>
        </div>
    );
};

export default ShoppingListPage;