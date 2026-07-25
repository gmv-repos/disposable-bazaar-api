import React, { createContext, useState, useContext, useEffect } from 'react';
import { toast } from 'react-toastify';
import { v4 as uuidv4 } from 'uuid'; // Importing uuid for unique ID generation

// Create a context for the cart
const CartContext = createContext();

// Create a provider component
export const CartProvider = ({ children }) => {
    const [cartItems, setCartItems] = useState(() => {
        // Load cart items from localStorage if available, otherwise use an empty array
        const savedCart = localStorage.getItem('cartItems');
        return savedCart ? JSON.parse(savedCart) : [];
    });

    // Save cart items to localStorage whenever they change
    useEffect(() => {
        localStorage.setItem('cartItems', JSON.stringify(cartItems));
    }, [cartItems]);

    // Function to add item to the cart
    const addToCart = (product_id, product_name, product_quantity, pack_size, total_pieces, price_per_piece, product_img, product_total, product_variants, product_color, product_size, logo, product_options, product_lids, lid, lid_Price, customizeDetail, option_Price, bundle_status, order_limit, packaging_options) => {
        const newItem = {
            id: uuidv4(), // Generate a unique ID for the cart item
            product_id,
            product_name,
            product_quantity,
            pack_size,
            total_pieces,
            price_per_piece,
            product_img,
            product_total,
            product_variants,
            product_color: product_color ? product_color : null,
            product_size: product_size ? product_size : null,
            logo: logo ? logo : null,
            product_options: product_options ? product_options : null,
            product_lids: product_lids ? product_lids : null,
            lid: lid ? lid : null,
            lid_Price: lid_Price ? lid_Price : lid_Price,
            customizeDetail: customizeDetail ? customizeDetail : null,
            option_Price: option_Price,
            bundle_status: bundle_status !== undefined ? bundle_status : false,
            order_limit: order_limit !== null ? order_limit : 1000,
            packaging_options: packaging_options ? packaging_options : null,
        };
        setCartItems((prevItems) => [...prevItems, newItem]);
    };

    // Function to update the quantity of an item in the cart
    // const updateQuantity = (itemId, quantity) => {
    //     setCartItems((prevItems) =>
    //         prevItems.map(item => {
    //             if (item.id === itemId) { // Check by unique ID
    //                 const newQuantity = Math.max(1, quantity); // Ensure quantity is at least 1

    //                 // Ensure item.pack_size and item.product_sub_total are valid numbers
    //                 const packSize = Number(item.pack_size) || 1; // Default to 1 if not a valid number
    //                 const productSubTotal = item.lid_Price ? (Number(item.lid_Price) + Number(item.price_per_piece) + Number(item.option_Price)) : Number(item.price_per_piece) || 0; // Default to 0 if not a valid number

    //                 // Update total_pieces
    //                 const newTotalPieces = newQuantity * packSize;

    //                 // Recalculate product_total
    //                 const newProductTotal = newTotalPieces * productSubTotal;

    //                 return {
    //                     ...item,
    //                     product_quantity: newQuantity,
    //                     total_pieces: newTotalPieces,
    //                     product_total: newProductTotal.toFixed(2) // Ensure price is in 2 decimal places
    //                 };
    //             }
    //             return item;
    //         })
    //     );
    // };
    const updateQuantity = (itemId, quantity) => {
    setCartItems((prevItems) =>
        prevItems.map((item) => {
            if (item.id === itemId) {
                // Check if exceeding order limit
                if (quantity > item.order_limit) {
                     toast.warning(`Maximum order limit (${item.order_limit}) reached!`);
                    return item;
                }

                const newQuantity = Math.max(1, quantity);

                const packSize = Number(item.pack_size) || 1;
                const productSubTotal = item.lid_Price
                    ? (Number(item.lid_Price) + Number(item.price_per_piece) + Number(item.option_Price)) + Number(item?.packaging_options?.price)
                    : Number(item.price_per_piece) + Number(item?.packaging_options?.price) || 0;

                const newTotalPieces = newQuantity * packSize;
                const newProductTotal = newTotalPieces * productSubTotal;

                return {
                    ...item,
                    product_quantity: newQuantity,
                    total_pieces: newTotalPieces,
                    product_total: newProductTotal.toFixed(2),
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

    const updatePackSize = (itemId, selectedPackSize) => {
        setCartItems((prevItems) =>
            prevItems.map((item) => {
                if (item.id === itemId) {
                    // Find the selected variant based on the selected pack size
                    const selectedVariant = item.product_variants.find(
                        (variant) => variant.pack_size === selectedPackSize
                    );

                    // Ensure that the selected variant exists
                    if (selectedVariant) {
                        const validPackSize = Number(selectedVariant.pack_size) || 1; // Default to 1 if invalid
                        const packPrice = Number(selectedVariant.price) || 0; // Pack price from the variant

                        // Calculate new total pieces based on pack size and product quantity
                        const newTotalPieces = validPackSize * item.product_quantity;

                        // Calculate the price per piece and total price
                        const newPricePerPiece = Number(selectedVariant.price_per_piece) || 0;
                        const newProductTotal =  item.lid_Price ? (newTotalPieces * (Number(item.lid_Price) + Number(newPricePerPiece) + Number(item?.packaging_options?.price ? item?.packaging_options?.price : 0) + Number(item.option_Price ? item.option_Price : 0))) : newTotalPieces * ( newPricePerPiece + Number(item.option_Price ? item.option_Price : 0)  + Number(item?.packaging_options?.price ? item?.packaging_options?.price : 0)  ) || 0;
                        //  newTotalPieces * newPricePerPiece;
                        console.log('option', item.option_Price)
                        console.log('lid price', item.lid_Price)
                        console.log('per price', newPricePerPiece)

                        return {
                            ...item,
                            pack_size: validPackSize, // Update to the new pack size
                            total_pieces: newTotalPieces, // Update total pieces
                            price_per_piece: newPricePerPiece.toFixed(2), // Update price per piece
                            product_total: newProductTotal.toFixed(2), // Update product total
                        };
                    }
                }
                return item;
            })
        );
    };


    const updateProductOption = (itemId, newValue, type) => {
        setCartItems((prevItems) =>
            prevItems.map(item => {
                if (item.id === itemId) {
                    console.log("Current Item:", item); // Log current item details

                    // Find the product option that matches the new size or color
                    const selectedOption = item.product_options.find(option =>
                        type === 'size' ? option.size === newValue : option.option === newValue
                    );
                    console.log("Selected Option:", selectedOption); // Log selected option
    console.log('variant', item.product_variants)
                    // Find the price of the current selected pack size
                    const selectedPack = item.product_variants.find(variant => variant.pack_size == item.pack_size);
                    console.log("Selected Pack:", selectedPack); // Log selected pack

                    // Ensure the selected option and pack size exist
                    if (selectedOption && selectedPack) {
                        // Calculate new price per piece by adding the option price and the pack price
                        const packPricePerPiece = Number(selectedPack.price) / Number(selectedPack.pack_size) || 0; // Price per piece from pack
                        const optionPricePerPiece = Number(selectedOption.options_price) || 0; // Option price per piece
                        const lidsPrice = Number(item.lid_Price) || 0;
console.log('lid Price' ,lidsPrice)
                        const newPricePerPiece = item?.packaging_options?.price + packPricePerPiece + optionPricePerPiece + lidsPrice; // Total price per piece
                        const newProductTotal = item.product_quantity * newPricePerPiece * item.pack_size; // Total price calculation

                        return {
                            ...item,
                            product_size: type === 'size' ? newValue : item.product_size, // Update the size if type is size
                            product_color: type === 'color' ? newValue : item.product_color, // Update the color if type is color
                            price_per_piece: newPricePerPiece.toFixed(2), // Update price per piece
                            product_total: newProductTotal.toFixed(2), // Update total price
                        };
                    }
                }
                return item;
            })
        );
    };

    // Function to get the total quantity of products in the cart
    const getTotalQuantity = () => {
        return cartItems.reduce((total, item) => total + item.product_quantity, 0);
    };

    // Function to get the total price of products in the cart
    const getTotalPrice = () => {
        return cartItems.reduce((total, item) => total + parseFloat(item.product_sub_total), 0);
    };

    // Function to remove an item from the cart
    // const removeFromCart = (product_id) => {
    //     setCartItems((prevItems) => prevItems.filter(item => item.product_id !== product_id));
    // };


    return (
        <CartContext.Provider value={{ cartItems, addToCart, getTotalQuantity, getTotalPrice, updateQuantity, updatePackSize, removeFromCart, updateProductOption }}>
            {children}
        </CartContext.Provider>
    );
};

// Custom hook to use the Cart context
export const useCart = () => useContext(CartContext);

