import React, { createContext, useContext, useState, useEffect } from 'react';
import axios from '../Utils/axios';


// Create the context
const WishlistContext = createContext();

// WishlistProvider managing wishlist count, including fetching default value from API
export const WishlistProvider = ({ children }) => {
    const [wishlistCount, setWishlistCount] = useState(0);

    // Fetch initial wishlist count from API when component mounts
    useEffect(() => {
        const fetchWishlistCount = async () => {
            try {
                const response = await axios.protected.get('/user/wishlist/count'); // Replace with your actual API endpoint
                setWishlistCount(response.data.count); // Assuming response contains count field
            } catch (error) {
                console.error('Error fetching wishlist count:', error);
            }
        };

        fetchWishlistCount();
    }, []);

    // Function to increase wishlist count
    const addToWishlist = () => {
        setWishlistCount((prevCount) => prevCount + 1);
    };

    // Function to decrease wishlist count
    const removeFromWishlist = () => {
        setWishlistCount((prevCount) => (prevCount > 0 ? prevCount - 1 : 0));
    };

    return (
        <WishlistContext.Provider value={{ wishlistCount, addToWishlist, removeFromWishlist }}>
            {children}
        </WishlistContext.Provider>
    );
};

// Custom hook to use the wishlist context
export const useWishlist = () => {
    return useContext(WishlistContext);
};
