import React, { useState } from 'react'
import Hamburger from '../Hamburger';
import { Assets_Url, Image_Url } from '../../const';
import { MdOutlineNavigateBefore } from 'react-icons/md';
import { RxCross2 } from 'react-icons/rx';
import { Link } from 'react-router-dom';
import { useCart } from '../../Context/CartContext';
import { TbEdit } from 'react-icons/tb';
import { ImCancelCircle } from 'react-icons/im';
import CustomSeo from '../CustomSeo';
import { ToastContainer } from 'react-toastify';

function Cart() {
    const { cartItems, removeFromCart, updateQuantity, updatePackSize, updateProductOption } = useCart();

    // Calculate the subtotal of the cart
    const calculateSubtotal = () => {
        return cartItems.reduce((total, item) => total + Number(item.product_total), 0);
    };

    const deliveryCharges = 0; // Set your delivery charges here
    const subtotal = calculateSubtotal();
    const total = subtotal + deliveryCharges; // Calculate total
    const [isCustom, setIsCustom] = useState(false)

    const handleRemove = (itemId) => {
        removeFromCart(itemId); // Pass the product_id to remove it from the cart.
    };


    return (
        <div className="relative py-32 md:px-10 px-5">
             <ToastContainer autoClose={500} />
            <CustomSeo id={5} />
            <div className="text-white py-4">
                <Hamburger firstPage='Home' secondPage='Cart' />
                <h4 className='text-6xl pt-10 font-bazaar'>Your Cart</h4>
            </div>
            <section className='text-white flex lg:flex-row flex-col-reverse lg:gap-8'>
                {/* desktop responsive */}
                <div className="hidden md:flex flex-col itemsbetween text-white py-4 lg:w-4/5 w-full">
                    <div className="grid grid-cols-12 gap-4 py-5 border-b border-gray-600">
                        <div className="col-span-4">Product</div>
                        <div className="col-span-2">Pack Size</div>
                        <div className="col-span-2">Quantity</div>
                        <div className="col-span-2">Total Piece</div>
                        <div className="col-span-2">Total Price</div>
                    </div>


                    {cartItems.length > 0 ? (
                        cartItems?.map((product) => (
                            <div key={product.id} className="grid grid-cols-12 gap-4 py-8 border-t border-gray-600 items-center">
                                <div className="col-span-4 flex items-center">
                                    <button className="mr-2 text-white" onClick={() => handleRemove(product.id)}>
                                        <RxCross2 />
                                    </button>
                                    <img
                                        src={`${Assets_Url}${product.product_img}`}
                                        alt={product.name}
                                        className="w-28 h-20 border-2 border-[#1E7773] rounded-xl object-cover"
                                    />
                                    <div className="ml-5 w-32">
                                        <h4>{product.product_name}</h4>

                                        <div className="flex">
                                            {product.product_options && (
                                                <section className="flex flex-row justify-center gap-3 items-center">
                                                    <h4 className="font-bazaar py-1 pt-2">CUSTOMIZED</h4>
                                                    {/* <button
                                                        onClick={() => {
                                                            setIsCustom(isCustom === product.id ? null : product.id);
                                                        }}
                                                        className="cursor-pointer"
                                                    >
                                                        <TbEdit size={20} />
                                                    </button> */}
                                                    <section className="relative flex justify-center items-center">
                                                        {isCustom === product.id && (
                                                            <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                                                <div className="bg-white text-black w-2/5 p-6 rounded-lg shadow-lg relative ">
                                                                    <button className='absolute top-2 right-2' onClick={() => setIsCustom(null)}>
                                                                        <ImCancelCircle className="text-black text-xl" />
                                                                    </button>

                                                                    <section className='flex flex-row gap-4'>
                                                                        <div className="flex flex-col gap-3">
                                                                            <img
                                                                                src={`${Assets_Url}${product.product_img}`}
                                                                                alt={product.name}
                                                                                className="w-36 h-36 border2 bg-[#1E7773] rounded-xl object-cover"
                                                                            />
                                                                            <div className="w-36">
                                                                                <h4 className='text-lg text-center font-bazaar'>{product.product_name}</h4>
                                                                            </div>
                                                                        </div>
                                                                        <div className="text-black ml-10">
                                                                            {/* Sizes Section */}
                                                                            <h4 className='py-1 border-b border-black'>Sizes:</h4>
                                                                            <div className="flex flex-wrap gap-2 py-1">
                                                                                {/* Map through unique sizes from product options */}
                                                                                {[...new Set(product.product_options?.map(option => option.size))]?.map((size) => (
                                                                                    <label key={size} className="flex items-center">
                                                                                        <input
                                                                                            type="radio"
                                                                                            name={`size-${product.id}`}
                                                                                            value={size}
                                                                                            className="hidden peer" // Hidden radio input
                                                                                            onChange={() => updateProductOption(product.id, size, 'size')} // Call function on change
                                                                                            checked={product.product_size === size} // Check if this size is selected
                                                                                        />
                                                                                        <span className={`cursor-pointer text-sm px-3 p-1 border border-gray-300 rounded text-center
                                                                               ${product.product_size === size ? 'bg-[#1E7773] text-white' : 'bg-white text-black'}`}>
                                                                                            {size} {/* Display size */}
                                                                                        </span>
                                                                                    </label>
                                                                                ))}
                                                                            </div>




                                                                            {/* Colors Section */}
                                                                            <h4 className='py-1 border-b border-black'>Colors:</h4>
                                                                            <div className="flex flex-wrap gap-2 py-1">
                                                                                {[...new Set(product.product_options?.map(option => option.option?.trim().toLowerCase()))]?.map((color) => (
                                                                                    <label key={color} className="flex items-center">
                                                                                        <input
                                                                                            type="radio"
                                                                                            name={`color-${product.id}`}
                                                                                            value={color}
                                                                                            className="hidden peer"
                                                                                            onChange={() => updateProductOption(product.id, color, 'color')}
                                                                                            checked={product.product_color?.trim().toLowerCase() === color} // Set default checked color
                                                                                        />
                                                                                        <span className={`cursor-pointer text-sm px-3 p-1 border border-gray-300 rounded text-center
                                                                               ${product.product_color === color ? 'bg-[#1E7773] text-white' : 'bg-white text-black'}`}>
                                                                                            {color.charAt(0).toUpperCase() + color.slice(1)}
                                                                                        </span>
                                                                                    </label>
                                                                                ))}
                                                                            </div>


                                                                        </div>
                                                                    </section>
                                                                </div>
                                                            </div>
                                                        )}
                                                    </section>

                                                </section>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                <div className="col-span-2 flex flex-row justify-center w-24">
{product.product_variants !== null && (                                    <select
                                        className="bg-[#20202C] border border-[#1E7773] rounded-lg  w-24 outline-none p-2 mr-1"
                                        onChange={(e) => updatePackSize(product.id, e.target.value)}
                                        value={product.pack_size} // Set default value
                                    >
                                        {product.product_variants?.map((variant) => (
                                            <option key={variant.id} value={variant.pack_size} className="text-white">
                                                {variant.pack_size} Pcs
                                            </option>
                                        ))}
                                    </select>
    )}                            </div>

                                <div className="col-span-2 flex justify-around items-center px-2 border border-[#1E7773] w-24 rounded-lg">
                                    <button
                                        onClick={() => updateQuantity(product.id, Math.max(1, product.product_quantity - 1))}
                                        className="text-white py-2"
                                    >
                                        -
                                    </button>
                                    <input
                                        type="text"
                                        value={product.product_quantity}
                                        className="w-12 text-center bg-transparent border-none text-white"
                                        readOnly
                                    />
                                    <button onClick={() => updateQuantity(product.id, product.product_quantity + 1)} className="text-white py-2">
                                        +
                                    </button>
                                </div>

                                <div className="col-span-2 border border-[#1E7773] w-24 rounded-lg text-center py-2">
                                    {product.total_pieces} Pcs
                                </div>

                                <div className="col-span-2 text-2xl font-semibold text-left">
                                    Rs: {product.product_total}
                                    <div className="text-xs text-gray-400">Per Pieces: {Number(product.price_per_piece ? product.price_per_piece : 0) + Number(product.lid_Price ? product.lid_Price : 0) + Number(product?.option_Price ? product?.option_Price : 0)} Rs</div>
                                </div>
                            </div>
                        ))
                    ) : (
                        <div className="flex justify-center py-10 text-4xl font-bazaar">
                            Your Cart is Empty
                        </div>
                    )}

                </div>
                {/* mobile responsive */}
                <div className="md:hidden flex flex-col itemsbetween text-white py-4 lg:w-4/5 w-full">
                    {cartItems.length > 0 ? (
                        cartItems?.map((product) => (
                            <div key={product.id} className="flex  gap-4 py-8 border-b border-gray-600 justify-center items-center">
                                <div className="flex items-center">
                                    <button className="mr-2 text-white" onClick={() => handleRemove(product.id)}><RxCross2 /></button>
                                    <img src={`${Assets_Url}${product.product_img}`} alt={product.name} className="w-20 h-16 border-2 border-[#1E7773] rounded-xl object-cover" />
                                </div>
                                <div className="flex flex-col justify-center items-start gap-2 px4">
                                    <div className="">{product.name}</div>
                                    <div className="flex flex-row justifybetween gap-2">

                                        <div className="col-span-2 flex flex-row justify-center w-20">
             {product.product_variants !== null && (                               <select
                                                className="bg-[#20202C] border border-[#1E7773] rounded-lg  w-20 outline-none p-2 mr-1"
                                                onChange={(e) => updatePackSize(product.id, e.target.value)}
                                                value={product.pack_size} // Set default value
                                            >
                                                {product.product_variants?.map((variant) => (
                                                    <option key={variant.id} value={variant.pack_size} className="text-white">
                                                        {variant.pack_size} Pcs
                                                    </option>
                                                ))}
                                            </select>
                  )}                      </div>

                                        <div className="col-span-2 flex justify-around items-center px-2 border border-[#1E7773] w-16 rounded-lg">
                                            <button onClick={() => updateQuantity(product.id, Math.max(1, product.product_quantity - 1))} className=" text-white py-2">-</button>
                                            <input
                                                type="text"
                                                value={product.product_quantity}
                                                className="w-6 text-center bg-transparent border-none text-white"
                                                readOnly
                                            />
                                            <button onClick={() => updateQuantity(product.id, product.product_quantity + 1)} className=" text-white py-2 ">+</button>
                                        </div>

                                        <div className="border border-[#1E7773] w-16 rounded-lg text-center py-2">{product.total_pieces}Pcs</div>

                                    </div>
                                    <div className='flex justify-between w-full'>
                                        <div className="md:text-2xl text-lg font-semibold text-left">
                                            Rs: {product.product_total}
                                            <div className="text-xs text-gray-400">Per Pieces: {Number(product.price_per_piece ? product.price_per_piece : 0) + Number(product.lid_Price ? product.lid_Price : 0) + Number(product?.option_Price ? product?.option_Price : 0)}Rs</div>
                                        </div>

                                        {product.product_options && (
                                            <div className='flex'>
                                                <h4 className="font-bazaar py-1 text-sm flex justify-center items-center">CUSTOMIZED</h4>
                                                {/* <button
                                                    onClick={() => {
                                                        setIsCustom(isCustom === product.id ? null : product.id);
                                                    }}
                                                    className="cursor-pointer"
                                                >
                                                    <TbEdit size={18} />
                                                </button> */}
                                                <section className="relative flex justify-center items-center">
                                                    {isCustom === product.id && (
                                                        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                                            <div className="bg-white text-black  p-6 rounded-lg shadow-lg relative ">
                                                                <button className='absolute top-2 right-2' onClick={() => setIsCustom(null)}>
                                                                    <ImCancelCircle className="text-black text-xl" />
                                                                </button>

                                                                <section className='flex flex-row gap-4'>
                                                                    <div className="flex flex-col gap-3">
                                                                        <img
                                                                            src={`${Assets_Url}${product.product_img}`}
                                                                            alt={product.name}
                                                                            className="w-36 h-36 border2 bg-[#1E7773] rounded-xl object-cover"
                                                                        />
                                                                        <div className="w-36">
                                                                            <h4 className='text-lg text-center font-bazaar'>{product.product_name}</h4>
                                                                        </div>
                                                                    </div>
                                                                    <div className="text-black">
                                                                        {/* Sizes Section */}
                                                                        <h4 className='py-1 border-b border-black'>Sizes:</h4>
                                                                        <div className="flex flex-wrap gap-2 py-1">
                                                                            {/* Map through unique sizes from product options */}
                                                                            {[...new Set(product.product_options?.map(option => option.size))]?.map((size) => (
                                                                                <label key={size} className="flex items-center">
                                                                                    <input
                                                                                        type="radio"
                                                                                        name={`size-${product.id}`}
                                                                                        value={size}
                                                                                        className="hidden peer" // Hidden radio input
                                                                                        onChange={() => updateProductOption(product.id, size, 'size')} // Call function on change
                                                                                        checked={product.product_size === size} // Check if this size is selected
                                                                                    />
                                                                                    <span className={`cursor-pointer text-sm px-3 p-1 border border-gray-300 rounded text-center
                                                                                        ${product.product_size === size ? 'bg-[#1E7773] text-white' : 'bg-white text-black'}`}>
                                                                                        {size} {/* Display size */}
                                                                                    </span>
                                                                                </label>
                                                                            ))}
                                                                        </div>




                                                                        {/* Colors Section */}
                                                                        <h4 className='py-1 border-b border-black'>Colors:</h4>
                                                                        <div className="flex flex-wrap gap-2 py-1">
                                                                            {[...new Set(product.product_options?.map(option => option.option?.trim().toLowerCase()))]?.map((color) => (
                                                                                <label key={color} className="flex items-center">
                                                                                    <input
                                                                                        type="radio"
                                                                                        name={`color-${product.id}`}
                                                                                        value={color}
                                                                                        className="hidden peer"
                                                                                        onChange={() => updateProductOption(product.id, color, 'color')}
                                                                                        checked={product.product_color?.trim().toLowerCase() === color} // Set default checked color
                                                                                    />
                                                                                    <span className={`cursor-pointer text-sm px-3 p-1 border border-gray-300 rounded text-center
                                                   ${product.product_color === color ? 'bg-[#1E7773] text-white' : 'bg-white text-black'}`}>
                                                                                        {color.charAt(0).toUpperCase() + color.slice(1)}
                                                                                    </span>
                                                                                </label>
                                                                            ))}
                                                                        </div>

                                                                    </div>
                                                                </section>
                                                            </div>
                                                        </div>
                                                    )}
                                                </section>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))
                    ) : (
                        <div className="flex justify-center py-10 text-4xl font-bazaar">
                            Your Cart is Empty
                        </div>
                    )
                    }
                </div>
                <div className="border-r-2 border-gray-500 w-2 h-inherit"></div>
                <div className="border px-3 lg:border-none border-[#1E7773] rounded-lg flex flex-col justify-start py-5 gap-3 lg:w-1/5 md:w-1/2">
                    <h4 className='text-3xl font-semibold'>Cart Totals</h4>
                    <div className="flex flex-col justify-start pt-10 gap-5">
                        {/* <div className="flex flex-row justify-between items-center">
                            <h4>Subtotal:</h4>
                            <h4>Rs: {subtotal}</h4>
                        </div> */}
                        {/* <div className="flex flex-row justify-between items-center">
                            <h4>Delivery:</h4>
                            <h4>Rs: {deliveryCharges}</h4>
                        </div>
                        <hr className="border-r-2 border-gray-500" />
                        <div className="flex flex-row justify-between items-center">
                            <h4>Discount Code:</h4>
                            <input type="text" className='border rounded-lg bg-transparent w-24 p-1' />
                        </div> */}
                        {/* <hr className="border-r-2 border-gray-500" /> */}
                        <div className="flex flex-row justify-between items-center">
                            <h4>Total:</h4>
                            <h4>Rs: {total}</h4>
                        </div>
                        <hr className="border-r-2 border-gray-500" />
                        <div className="flex flex-col gap-2">
                            <Link to='/checkout/'>
                                <button className={`bg-[#1E7773] ${cartItems.length === 0 ? 'hidden' : 'block'} w-full rounded-lg font-bazaar p-2`}>PURSHASE</button>
                            </Link>
                            <Link to='/shop/'>
                                <button className='flex flex-row justify-center items-center border border-[#1E7773]  w-full rounded-lg font-bazaar p-2'><MdOutlineNavigateBefore size={20} /><p className="pt-0.5"> CONTINUE SHOPPING</p></button>
                            </Link>
                        </div>
                    </div>
                </div>
            </section >

            {/* Background Image */}
            < img
                data-aos="fade-right"
                className="absolute top-[16rem] -left-8 w-20"
                src={`${Image_Url}FooterAssets/footerRightImg.svg`
                }
                alt="Plate"
            />
            <img
                data-aos="fade-left"
                className="absolute -bottom-30 -right-4 w-20"
                src={`${Image_Url}HomeAssets/PremiumAssets/shoper.svg`}
                alt="Plate"
            />
        </div >
    )
}

export default Cart

// {cartItems.map((product) => (
//     <div key={product.id} className="grid grid-cols-12 gap-4 py-8 border-t border-gray-600 items-center">
//         <div className="col-span-4 flex items-center">
//             <button className="mr-2 text-white" onClick={() => handleRemove(product.id)}><RxCross2 /></button>
//             <img src={`${Assets_Url}${product.product_img}`} alt={product.name} className="w-28 h-20 border-2 border-[#1E7773] rounded-xl object-cover" />
//             <div className="ml-5 w-32">
//                 <h4>{product.product_name}</h4>

//                 <div className='flex'>
//                     {product.product_options &&
//                         // <select
//                         //     className="bg-[#20202C] w-30 outline-none p-1 mt-1 mr-1 text-[9px] border-[1px] border-[#1E7773] rounded-md"
//                         //     onChange={(e) => updateSize(product.id, e.target.value)} // Call updatePackSize on change
//                         //     value={product.product_size} // Set the selected value from the product size
//                         // >
//                         //     {product.product_options.map((option) => (
//                         //         <option key={option.id} value={option.size} className="text-white text-[9px]">
//                         //             {option.size} ({option.option}) {/* Show size and option */}
//                         //         </option>
//                         //     ))}
//                         // </select>
//                         <section className='flex flex-row justify-center gap-3 items-center'>
//                             <h4 className='font-bazaar py-1 pt-2'>CUSTOMIZED</h4>
//                             <button onClick={() => { setIsCustom(!isCustom) }} className='cursor-pointer'>
//                                 <TbEdit size={20} />
//                             </button>
//                             <section className='relative flex justify-center items-center'>
//                                 {isCustom && (
//                                     <div className="bg-white w-96 h-96 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex justify-center items-center shadow-lg rounded-lg z-50">

//                                         <button onClick={() => setIsCustom(false)}>
//                                             <ImCancelCircle className='text-black' />
//                                         </button>
//                                     </div>
//                                 )}

//                             </section>
//                         </section>
//                     }
//                 </div>

//             </div>
//         </div>

//         <div className="col-span-2 flex flex-row justify-center w-24">
//             <select
//                 className="bg-[#20202C] border border-[#1E7773] rounded-lg  w-24 outline-none p-2 mr-1"
//                 onChange={(e) => updatePackSize(product.id, e.target.value)}
//                 value={product.pack_size} // Set default value
//             >
//                 {product.product_variants.map((variant) => (
//                     <option key={variant.id} value={variant.pack_size} className='text-white'>
//                         {variant.pack_size} Pcs
//                     </option>
//                 ))}
//             </select>

//         </div>

//         <div className="col-span-2 flex justify-around items-center px-2 border border-[#1E7773] w-24 rounded-lg">
//             <button onClick={() => updateQuantity(product.id, Math.max(1, product.product_quantity - 1))} className=" text-white py-2">-</button>
//             <input
//                 type="text"
//                 value={product.product_quantity}
//                 className="w-12 text-center bg-transparent border-none text-white"
//                 readOnly
//             />
//             <button onClick={() => updateQuantity(product.id, product.product_quantity + 1)} className=" text-white py-2 ">+</button>
//         </div>

//         <div className="col-span-2 border border-[#1E7773] w-24 rounded-lg text-center py-2">{product.total_pieces}p</div>

//         <div className="col-span-2 text-2xl font-semibold text-left">
//             Rs: {product.product_total}
//             <div className="text-xs text-gray-400">Per Pieces: {product.price_per_piece}Rs</div>
//         </div>
//     </div>
// ))}
