import React, { useState, useEffect, useMemo } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';
import { useNavigate, useParams } from 'react-router-dom';
import { FaPlus, FaMinus } from 'react-icons/fa';

const ShoppingListForm = ({ isEdit = false }) => {
    const [formData, setFormData] = useState({
        name: '',
        items: []
    });
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const [ingredientSuggestions, setIngredientSuggestions] = useState([]);
    const [activeSuggestionIndex, setActiveSuggestionIndex] = useState(null);
    const [activeInputIndex, setActiveInputIndex] = useState(null);

    const navigate = useNavigate();
    const { id } = useParams();

    const user = useMemo(() => {
        return Cookies.get('user') ? JSON.parse(Cookies.get('user')) : null;
    }, []);

    useEffect(() => {
        if (!user) {
            navigate('/login');
            return;
        }

        if (isEdit && id) {
            const fetchShoppingList = async () => {
                try {
                    const response = await axios.get(
                        `http://127.0.0.1:8000/api/v1/shopping-lists/${user.id}/${id}`
                    );

                    setFormData({
                        name: response.data.data.name,
                        items: response.data.data.items.map(item => ({
                            itemId: item.id,
                            id: item.ingredient.id,
                            name: item.ingredient.name,
                            purchased: item.purchased
                        }))
                    });
                } catch (err) {
                    setError('Failed to fetch shopping list.');
                }
            };

            fetchShoppingList();
        }

        // Event listener for Escape key
        const handleEscapeKey = (e) => {
            if (e.key === 'Escape') {
                setIngredientSuggestions([]); // Close the dropdown
            }
        };

        window.addEventListener('keydown', handleEscapeKey);

        return () => {
            window.removeEventListener('keydown', handleEscapeKey);
        };
    }, [isEdit, id, user, navigate]);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData(prevState => ({
            ...prevState,
            [name]: value
        }));
    };

    const handleItemChange = (index, e) => {
        const { name, value, type, checked } = e.target;
        const updatedItems = [...formData.items];

        updatedItems[index][name] = type === "checkbox" ? checked : value;
        setFormData(prevState => ({
            ...prevState,
            items: updatedItems
        }));
    };

    const addItem = () => {
        setFormData(prevState => ({
            ...prevState,
            items: [...prevState.items, { 
                itemId: null,
                id: null, 

                name: '',
                purchased: false
             }]
        }));
    };

    const removeItem = async (index) => {

        const updatedItems = [...formData.items];
        const itemToRemove = updatedItems[index];
        itemToRemove.id = itemToRemove.itemId;

        if (itemToRemove.id) {
            setLoading(true);
            try {
                await axios.delete(
                    `http://127.0.0.1:8000/api/v1/shopping-lists/${user.id}/${id}/items/${itemToRemove.id}`,
                    
                );
            } catch (err) {
                setError('Failed to remove item.');
                setLoading(false);
                return;
            }
            setLoading(false);
        }

        updatedItems.splice(index, 1);
        setFormData(prevState => ({
            ...prevState,
            items: updatedItems
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setLoading(true);

        //console.log(formDate);
        //debugger

        try {
            if (isEdit && id) {
                await axios.put(
                    `http://127.0.0.1:8000/api/v1/shopping-lists/${user.id}/${id}`,
                    formData
                );
            } else {
                await axios.post(
                    `http://127.0.0.1:8000/api/v1/shopping-lists/${user.id}`,
                    formData
                );
            }
            navigate(`/shopping-lists/${user.id}`);
        } catch (err) {
            setError('Failed to save shopping list.');
        } finally {
            setLoading(false);
        }
    };

    const fetchIngredients = async (query) => {
        if (query.length < 2) {
            setIngredientSuggestions([]);
            return;
        }

        try {
            const response = await axios.get(`http://127.0.0.1:8000/api/v1/ingredients`, {
                params: { search: query }
            });
            setIngredientSuggestions(response.data.data);
        } catch (err) {
            console.error('Error fetching ingredients:', err);
        }
    };

    const handleAddItem = async (ingredient, index) => {
        try {
            const response = await axios.post(
                `http://127.0.0.1:8000` +
                `/api/v1/shopping-lists/${user.id}/${id}/items`, {
                ingredient_id: ingredient.id,
                purchased: false, // Default value
            });

            const updatedItems = [...formData.items];
            updatedItems[index].itemId = response.data.id;
            updatedItems[index].id = ingredient.id;
            updatedItems[index].name = ingredient.name;
            updatedItems[index].purchased = false;

            setFormData({
                ...formData,
                items: updatedItems
            });
            setIngredientSuggestions([]);
            setActiveInputIndex(null);
        } catch (error) {
            console.error('Failed to add item:', error.response?.data || error.message);
        }
    };

    return (
        <div className="container mx-auto p-4">
            <h1 className="text-2xl font-bold mb-6">
                {isEdit ? 'Edit Shopping List' : 'Create New Shopping List'}
            </h1>

            {error && (
                <div className="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {error}
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
                <div>
                    <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">
                        Shopping List Name
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value={formData.name}
                        onChange={handleInputChange}
                        className="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required
                    />
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Items</label>
                    <div className="space-y-4">
                        {formData.items.map((item, index) => (
                            <div key={index} className="relative">
                                <div className="flex gap-2 items-center">
                                    <div className="relative flex-grow">
                                        <input
                                            type="text"
                                            name="name"
                                            value={item.name}
                                            onChange={(e) => {
                                                handleItemChange(index, e);
                                                fetchIngredients(e.target.value);
                                                setActiveInputIndex(index);
                                            }}
                                            onFocus={() => setActiveInputIndex(index)}
                                            placeholder="Item Name"
                                            className="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                            disabled={loading}
                                        />

                                        {activeInputIndex === index && ingredientSuggestions.length > 0 && (
                                            <ul className="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg z-10 max-h-48 overflow-y-auto">
                                                {ingredientSuggestions.map((ingrSuggestion) => (
                                                    <li
                                                        key={ingrSuggestion.id}
                                                        className="p-2 hover:bg-gray-100 cursor-pointer"
                                                        onClick={() => {
                                                            //Call Api to add the item
                                                            handleAddItem(ingrSuggestion, index);
                                                        }}
                                                    >
                                                        {ingrSuggestion.name}
                                                    </li>
                                                ))}
                                            </ul>
                                        )}
                                    </div>

                                    <div className="flex items-center gap-2">
                                        <label className="flex items-center gap-2">
                                            <input
                                                type="checkbox"
                                                name="purchased"
                                                checked={item.purchased}
                                                onChange={(e) => handleItemChange(index, e)}
                                                disabled={loading}
                                                className="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            />
                                            <span className="text-sm text-gray-600">Purchased</span>
                                        </label>

                                        <button
                                            type="button"
                                            onClick={() => removeItem(index)}
                                            className="p-2 text-red-600 hover:text-red-800 disabled:opacity-50"
                                            disabled={loading}
                                        >
                                            <FaMinus size={18} />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    <button
                        type="button"
                        onClick={addItem}
                        className="mt-2 flex items-center gap-1 text-blue-600 hover:text-blue-800"
                    >
                        <FaPlus size={18} className="mr-1" />
                        Add Item

                    </button>
                </div>

                <div>
                    <button
                        type="submit"
                        className="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                        disabled={loading}
                    >
                        {loading ? 'Saving...' : isEdit ? 'Update List' : 'Create List'}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default ShoppingListForm;