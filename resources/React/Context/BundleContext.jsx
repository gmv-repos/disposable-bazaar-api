import React, { createContext, useState, useContext, useEffect } from 'react';
import { v4 as uuidv4 } from 'uuid'; // Importing uuid for unique ID generation

// Create a context for the cart
const BundleContext = createContext();

// Create a provider component
export const BundleProvider = ({ children }) => {
    const [bundleItems, setBundleItems] = useState(() => {
        // Load cart items from localStorage if available, otherwise use an empty array
        const savedBundle = localStorage.getItem('bundleItems');
        return savedBundle ? JSON.parse(savedBundle) : [];
    });

    // Save cart items to localStorage whenever they change
    useEffect(() => {
        localStorage.setItem('bundleItems', JSON.stringify(bundleItems));
    }, [bundleItems]);

    // Function to add item to the cart
    const addToBundle = (product_id,  product_name, product_quantity, total_pieces, price_per_piece, product_img, product_total) => {
        const newItem = {
            id: uuidv4(), // Generate a unique ID for the cart item
            product_id,
            product_name,
            product_quantity,
            total_pieces,
            price_per_piece,
            product_img,
            product_total,
        };
        setBundleItems((prevItems) => [...prevItems, newItem]);
    };

    // Function to update the quantity of an item in the cart
    const updateQuantity = (itemId, quantity) => {
        setCartItems((prevItems) =>
            prevItems.map(item => {
                if (item.id === itemId) { // Check by unique ID
                    const newQuantity = Math.max(1, quantity); // Ensure quantity is at least 1

                    // Ensure item.pack_size and item.product_sub_total are valid numbers
                    const packSize = Number(item.pack_size) || 1; // Default to 1 if not a valid number
                    const productSubTotal = item.lid_Price ? (Number(item.lid_Price) + Number(item.price_per_piece) + Number(item.option_Price)) : Number(item.price_per_piece) || 0; // Default to 0 if not a valid number

                    // Update total_pieces
                    const newTotalPieces = newQuantity * packSize;

                    // Recalculate product_total
                    const newProductTotal = newTotalPieces * productSubTotal;

                    return {
                        ...item,
                        product_quantity: newQuantity,
                        total_pieces: newTotalPieces,
                        product_total: newProductTotal.toFixed(2) // Ensure price is in 2 decimal places
                    };
                }
                return item;
            })
        );
    };

    // Function to remove an item from the cart
    const removeFromCart = (itemId) => {
        setCartItems((prevItems) => prevItems.filter(item => item.id !== itemId)); // Remove by unique ID
    };


    return (
        <BundleContext.Provider value={{ bundleItems, addToBundle, updateQuantity, removeFromCart}}>
            {children}
        </BundleContext.Provider>
    );
};

// Custom hook to use the Cart context
export const useBundle = () => useContext(BundleContext);

