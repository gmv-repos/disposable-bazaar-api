import React, { useEffect, useRef, useState } from "react";
import { Assets_Url, Image_Url, Profile_Assets_Url } from "../../const";
import { LuFacebook } from "react-icons/lu";
import { FaInstagram, FaLinkedinIn, FaRegUser } from "react-icons/fa";
import { FiYoutube } from "react-icons/fi";
import {
    RiShoppingBasket2Line,
    RiTiktokLine,
    RiTwitterXLine,
} from "react-icons/ri";
import {
    MdEmail,
    MdOutlineFileDownload,
    MdOutlineRestaurantMenu,
    MdPhone,
} from "react-icons/md";
import { CiHeart, CiSearch, CiUser } from "react-icons/ci";
import { TiThMenuOutline } from "react-icons/ti";
import { CgMenuLeft, CgMenuRight } from "react-icons/cg";
import { Link, NavLink, useNavigate } from "react-router-dom";
import { useUser } from "../../Context/UserContext";
import { useWishlist } from "../../Context/WishlistContext";
import { useCart } from "../../Context/CartContext";
import axios from "../../Utils/axios";
import { removeAccessToken, removeUserData } from "../../Utils/storage";
import { PiCaretDownThin } from "react-icons/pi";

import './Header.css'

function Header() {
    const [categories, setCategories] = useState([]);
    const [subCategories, setSubCategories] = useState([]);
    const [mobMenu, setMobMenu] = useState(false);
    const [isCustomBtn, setIsCustomBtn] = useState(false);
    const [showMegaMenu, setShowMegaMenu] = useState(false);
    const { user } = useUser();
    const { wishlistCount } = useWishlist();
    const [category, setCategory] = useState(null);
    const [searchTerm, setSearchTerm] = useState("");
    const { cartItems, getTotalQuantity } = useCart();
    const [isDropdown, setIsDropdown] = useState(false);
    const [expandedCategories, setExpandedCategories] = useState([]);
    // Get total items and total price from cart
    const totalItems = getTotalQuantity();
    const navigate = useNavigate(); // For navigating to the category page


    const dropdownRef = useRef(null);

    const toggleSubcategories = (categoryId) => {
        if (expandedCategories.includes(categoryId)) {
            setExpandedCategories(expandedCategories.filter((id) => id !== categoryId));
        } else {
            setExpandedCategories([...expandedCategories, categoryId]);
        }
    };
    // Close dropdown if clicking outside
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                setIsCustomBtn(false);
            }
        };

        // Add mousedown event listener
        document.addEventListener("mousedown", handleClickOutside);

        // Cleanup the event listener
        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
        };
    }, [dropdownRef]);
    useEffect(() => {
        const isProductCategory = location.pathname.startsWith('/product-category/');
        const isShopWithQuery = location.pathname.startsWith('/shop') && location.search;

        if (!isProductCategory && !isShopWithQuery) {
            setCategory(null);
            setSearchTerm('');
        }
    }, [location.pathname, location.search]);

    const calculateSubtotal = () => {
        return cartItems.reduce(
            (total, item) => total + Number(item.product_total),
            0
        );
    };
    const subtotal = calculateSubtotal();

    // const handleSearch = (e) => {
    //     e.preventDefault();
    //     if (searchTerm) {
    //         // Redirect to the category page with search query
    //         navigate(`/category/${category}?q=${encodeURIComponent(searchTerm)}`);
    //     }
    // };
    const handleSearch = (e) => {
        e.preventDefault();
        if (searchTerm) {
            // Redirect to the category page with search query
            console.log('category', category);
            if (category === null) {
                navigate(
                    `/shop/?q=${encodeURIComponent(searchTerm)}`);
            } else {
                navigate(
                    `/product-category/${category.slug}?q=${encodeURIComponent(searchTerm)}`, { state: category.id }
                );
            }
        }
    };
    const handleCategoryClick = (category) => {
        // When a category is clicked, redirect with the search term
        handleSearch(new Event("click"), category);
        setShowMegaMenu(false); // Optionally close the mega menu
    };

    const handleToggleMegaMenu = () => {
        setShowMegaMenu(!showMegaMenu);
    };

    const dummyProfilePic = "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxEQDw8QEBAQDg8PDw0PDQ0QDQ8NDw0PFhEWFhURExMYHSggGBolGxMTITEhJSk3Li4uFx8zODMsNygtLisBCgoKDQ0NDg0NDysZFRkrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrK//AABEIAOEA4QMBIgACEQEDEQH/xAAbAAEAAgMBAQAAAAAAAAAAAAAAAwQBAgUGB//EADAQAQACAAMFBgYCAwEAAAAAAAABAgMEESExQVFxBRJhgZHBIjJSobHRovATQuFy/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAH/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwD7iAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANZxI5tf8ANHP7SCQR/wCaP7EsxiRzBuMRLIAAAAAAAAAAAAAAAAAAAMWtpvBlpfEiP0hvizO7ZCMEtsaeiOZYEAAAAGYlvXFmPFGAs1xYnwSKTemJMeMclFoa0tE7mwAAAAAAAAAAAAAMWnTaDF7aK1rTO8vbWWqAAAAAAAEAAAAAM1nTcs4d9evFVZrOm0Fwa0trDZQAAAAAAAAAAVsa+s6cITYttIVQAEAABpjY0UjWfKOMmNiRWszPDhznk5OJiTadZ3/gE2LnLTu+GPDf6oJnXft67WBUIlPhZu9ePejlO37oAHWwMxF92yeMJXFraYnWNkxul1ctjd+uvGNkx4oqUAAAG+HfSfDitKSxgW2acvwolAAAAAAAAAkFfHtt6ImZlhAAAABz+0cTW0V4RtnrKolzU/Hb/wBTHoiVAAAABYyOJpeI4W2T7K7NZ0mJ5TEg7QywigADfCtpMejQBdGKTrEMqAAAAAADXEnZLZHj/LPl+QVgEAAAAHJzddL266+u1Evdo4e63lPsoqgAAAA2w662iOcxH3arXZ+FrbvcK/kHSYBFAAAAWcCdiRFl909fZKoAAAAAAI8f5Z8vykaYsbJBVAQAAAAYtWJiYnbE73LzGBNJ5xwl1WLViY0mNYngDii9i5D6Z08J/aC2UvH+uvSYlUQCaMrf6fvEJ8LIfVPlH7BVwcKbzpHnPCHWwsOKxERw+/izSkVjSI0hlFAAAAAAWMvunr7JUeBGxIoAAAAAAMTDICnLCTGrpPXajQAAAQ5jMxTZvnlHuCYmXLxM1e3HSOUbEEyo7XejnHqd6OcerigO13o5x6nejnHq4oDtxI4iXDzN67p18J2wDrCvl83Ftk/DP2nosIAAANsOuswCzSNIhsCgAAAAAAACPGrrHRWXVXFppPhO4GgMXtpEzO6I1QQZzMd2NI+aftHNzJbXvNpmZ3y1VAAAAAAAABfyWZ1+G2//AFnn4KBE6bt/AHbGmBid6sT69W6KLGBXjzQ0rrOi3EKAAAAAAAAAADW9dY0bAKdo02KvaFtKac5iPf2dPEpr14S5XakaRWPGQc8AQAAAAAAAAABf7NtstHKYn++i7EOf2Z81unu7GFh6dfwKzh00jx4twAAAAAAAAAAAAAQ5nL1xI0t5TxhMA8/msnbD37a8LRu8+Su9RMKGY7MrbbX4J5b6+nAHGFjGyWJTfXWOdfihXEAAAAATYOUvfdWdOc7IBCmy+WtiT8MbONp3Q6OX7LiNt570/TGyv/XQrWIjSI0iN0RsgVBlMpXDjZttO+3P9LAAAAAAAAAAAAAAAAAAAAI8TArb5qxPjMRr6pAFO/ZmHPCY6Wn3Rz2TT6rfx/ToAOdHZNPqt/H9JK9mYcfVPW36XQEWHlqV3ViPHTWfVKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/9k=";

    const toggleMenu = () => {
        setMobMenu(!mobMenu);
    };
    // const handleLogout = async () => {
    //     try {
    //         // Send the logout request to the server
    //         const response = await axios.protected.get("logout");

    //         // Check if the logout request was successful
    //         if (response.status === 200) {
    //             console.log("Logout successful");

    //             // Remove access token and user data from local storage
    //             removeAccessToken();
    //             removeUserData();
    //             localStorage.removeItem('toastShown');

    //             // Optionally, show a success toast message
    //             // toast.success(response.data.message || "Successfully logged out");

    //             // Reload the page or navigate to login page after logout
    //             window.location.reload(); // or navigate to login: navigate("/login");
    //             toast.success('Logout Sucessfully');
    //         } else {
    //             console.error("Logout failed with status:", response.status);
    //             console.error("Response data:", response.data);

    //             // Optionally, show an error message
    //             // toast.error("Failed to log out. Please try again.");
    //         }
    //     } catch (error) {
    //         console.error("Error during logout:", error);

    //         // Optionally, show a user-friendly error message
    //         // toast.error("An error occurred while logging out. Please try again.");
    //     }
    // };

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.public.get("product/category?sectionName=headerDropdown"
                );

                setCategories(response.data.data);

                // setSubCategories(response.data.data.subCategories);
                // console.log('res', subCategories);


                // setCategory(response.data.data[0].id);
            } catch (error) {
                console.log("Error", error);
            }
        };
        fetchData();
    }, []);
    const handleCategoryLink = (item) => {
        console.log('id', item.subCategories);
        setCategory(item);
        // console.log('id', category);

        navigate(`product-category/${item.slug}`, { state: item.id })
    }

    // useEffect(() => {
    //     const fetchData = async () => {
    //         try {
    //             const response = await axios.protected.get('user/wishlist/count');
    //             setWishListCount(response.data);
    //             // console.log(wishlistCount.length);

    //         } catch (error) {
    //             console.log(error);
    //         }
    //     };
    //     fetchData();
    // }, []);

    useEffect(() => {
        const header = document.getElementById("header");
        if (!header) return; // Avoid errors if header is not in the DOM

        let lastScrollY = window.scrollY;

        const handleScroll = () => {
            if (window.scrollY > lastScrollY) {
                // Scrolling Down
                header.classList.add("header-hidden");
            } else {
                // Scrolling Up
                header.classList.remove("header-hidden");
            }
            lastScrollY = window.scrollY; // Update the lastScrollY value
        };

        window.addEventListener("scroll", handleScroll);

        return () => {
            // Cleanup the event listener on component unmount
            window.removeEventListener("scroll", handleScroll);
        };
    }, []);

    return (
        <div id="header" className="fixed z-50 w-full flex flex-col ">
            <div className="bg-[#1E7773] md:px-20 w-full">
                <div className="flex flex-row lg:justify-between justify-center">
                    <ul className="lg:flex hidden  flex-row gap-1 text-sm cursor-pointer">
                        <li className="p-2 rounded-lg duration-300">
                            <a href="https://www.facebook.com/DisposableBazar/">
                                <LuFacebook className="text-white text-md" />
                            </a>
                        </li>
                        <li className="p-2 rounded-lg duration-300">
                            <a href="https://www.instagram.com/disposablebazaar/">
                                <FaInstagram className="text-white text-md" />
                            </a>
                        </li>
                        <li className="p-2 rounded-lg duration-300">
                            <a href="https://www.youtube.com/@disposablebazaar">
                                <FiYoutube className="text-white text-md" />
                            </a>
                        </li>
                        <li className="p-2 rounded-lg duration-300">
                            <a href="https://www.tiktok.com/@disposablebazaar">
                                <RiTiktokLine className="text-white text-md" />
                            </a>
                        </li>
                        <li className="bg-[#1E7773] text-white p-2 rounded-full">
                            <a href="https://pk.linkedin.com/company/disposablebazaar">
                                <FaLinkedinIn className="text-white text-md" />
                            </a>
                        </li>
                        {/* <li className="p-2 rounded-lg duration-300">
                            <RiTwitterXLine className="text-white text-md" />{" "}
                        </li> */}
                    </ul>
                    <ul className="lg:flex  flex flex-row md:gap-1 text-md cursor-pointer">
                        <li className="p-2 flex items-center text-white gap-2 md:text-[12px] text-[8px] duration-300">
                            <MdEmail className="text-white " />
                            info@disposablebazaar.com
                        </li>
                        <li className="p-2 flex items-center text-white gap-2 md:text-[12px] text-[8px] duration-300">
                            <MdPhone className="text-white " />
                            0321-3850002
                        </li>
                        <li className="p-2 md:text-[12px] text-[8px] duration-300">
                            <button
                                className="flex items-center text-white gap-2"
                                onClick={() =>
                                    window.open(
                                        "https://disposablebazaar.com/wp-content/uploads/2024/07/Disposable-Price-List-18072024.pdf",
                                        "_blank"
                                    )
                                }
                            >
                                <MdOutlineFileDownload className="text-white" />
                                Download Catalogue
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div className="w-full flex justify-around items-center py-2 bg-white">
                <ul className=" lg:hidden flex">
                    <Link to="/cart/">
                        <li className="flex">
                            <RiShoppingBasket2Line className="bg-[#1E7773] rounded-lg text-white p-1 text-3xl" />
                            <p className="text-xs px-2">
                                {" "}
                                <span className="text-[15px] font-semibold">
                                    My Cart
                                </span>{" "}
                                <br /> {totalItems} items - Rs {subtotal}{" "}
                            </p>
                        </li>
                    </Link>
                </ul>
                <div
                    className={`relative ${showMegaMenu ? "text-[#227c85]" : "text-black"
                        } hover:text-[#227c85] text-lg duration-300 cursor-pointer`}
                >
                    <div className="flex flex-col justify-center items-center">
                        <Link to="/">
                            <img
                                className="cursor-pointer w-32 md:w-40"
                                src={`${Image_Url}/DB-Logo-01.jpg`}
                                alt=""
                            />
                        </Link>
                        {/* <button
                            onClick={handleToggleMegaMenu}
                            className="bg-[#1E7773] hidden lg:flex font-semibold flex flex-row gap-2 rounded-lg justify-center items-center text-white py-1.5 px-3 text-xs"
                        >
                            <img
                                className="cursor-pointer w-4"
                                src={`${Image_Url}/HeaderAssets/category-icon.svg`}
                                alt=""
                            />{" "}
                            ALL CATEGORIES
                        </button> */}
                    </div>
                    {/* Mega Menu */}
                    {/* <div className="relative"
                        onMouseEnter={() => setShowMegaMenu(true)}
                        onMouseLeave={() => setShowMegaMenu(false)}
                    >
                        <div
                            className={`hidden lg:flex absolute -top-[0.5rem] -left-[60px] z-10 mt-3 h-screen overflow-y-auto flex flex-col w-60 transition-transform  duration-300 ease-in-out ${showMegaMenu ? "translate-x-0" : "-translate-x-full  bg-transparent border-none"} text-sm text-[#227c85] bg-white border border-gray-300 shadow-lg rounded`}
                        >
                            {categories.map((category, index) => (
                                <Link
                                    key={index}
                                    to={`/category/${category.id}`} // For direct link usage
                                    onClick={() =>
                                        handleCategoryClick(category.id)
                                    } // Call handleSearch on click
                                    className="font-sans m-1 px-4 py-2 hover:text-white hover:bg-[#227c85] duration-200 rounded"
                                >
                                    {category.name}
                                </Link>
                            ))}
                        </div>
                    </div> */}
                </div>
                {/* toggle button */}
                <div className=" flex justify-center pl-10">
                    <button
                        onClick={toggleMenu}
                        className="flex lg:hidden p-2 rounded text-3xl text-black"
                    >
                        <CgMenuRight />
                    </button>
                </div>

                {/* desktop menu */}
                <div className="lg:flex hidden flex w-1/2">
                    <div className="w-full">
                        <form
                            onSubmit={handleSearch}
                            className="relative flex justify-center items-center"
                        >
                            {/* <select
                                className="absolute left-3 w-48 border-r pr-2 p-2 border-gray-400 bg-white focus:outline-none"
                                value={category}
                                onChange={(e) => {
                                    handleCategoryLink(e.target.value), setCategory(e.target.value)
                                }} // Use onChange instead of onClick
                            >
                                <option value="null" className="mt2">
                                    All Categories
                                </option>
                                {categories.map((category, index) => (
                                    <option key={index} value={category.id}>
                                        {category.name}
                                    </option>
                                ))}
                            </select> */}
                            <div className="absolute left-3 w-72 border-r pr-2 p-2 border-gray-400 focus:outline-none"
                                onMouseEnter={() => setIsDropdown(!isDropdown)}
                                onMouseLeave={() => setIsDropdown(!isDropdown)}>
                                <div
                                    // onClick={() => setIsDropdown(!isDropdown)}
                                    className="flex justify-between items-center rounded cursor-pointer"
                                >
                                    <p>{category === "null" ? "All Categories" : category?.name || "Select a Category"}</p>
                                    <PiCaretDownThin size={20} />
                                </div>

                                {isDropdown && (
                                    <div className="absolute z-10 sm:col-span-6 col-span-full w-full rounded-lg top-10 left-0 overflow-y-auto h-56 bg-white border border-gray-200">
                                        {Array.isArray(categories) &&

                                            categories.map((cat) => (
                                                <div key={cat.id}>
                                                    <div
                                                        className="text-black p-2 px-4 cursor-pointer hover:bg-gray-100 flex justify-between items-center"
                                                        onClick={() => handleCategoryLink(cat)}
                                                    >
                                                        <span className="text-sm">{cat.name}</span>
                                                        {cat.subCategories?.length > 0 && (
                                                            <button
                                                                onClick={(e) => {
                                                                    e.stopPropagation(); // Prevent parent click
                                                                    toggleSubcategories(cat.id);
                                                                }}
                                                                className="text-gray-500 text-3xl hover:text-black"
                                                            >
                                                                {expandedCategories.includes(cat.id) ? "-" : "+"}
                                                            </button>
                                                        )}
                                                    </div>
                                                    {expandedCategories.includes(cat.id) && (
                                                        <div className="pl-8">
                                                            {cat.subCategories.map((subCat) => (
                                                                <div
                                                                    key={subCat.id}
                                                                    className="text-gray-700 p-2 px-4 cursor-pointer hover:bg-gray-200 text-xs"
                                                                    onClick={() => handleCategoryLink(subCat)}
                                                                >
                                                                    {subCat.name}
                                                                </div>
                                                            ))}
                                                        </div>
                                                    )}
                                                </div>

                                            ))}
                                        {/* {subCategories.length === 0 ? (
                                            <div className="">{subCategories.map((data, index) => (
                                                <div className="">
                                                    {data}
                                                </div>
                                            ))}</div>
                                        ) : null} */}
                                    </div>
                                )}
                            </div>
                            <input
                                className="w-full pl-80 border-2 border-gray-400 rounded-l-lg p-2"
                                type="text"
                                placeholder="Search Products.."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                               
                            />

                            <button
                                className="rounded-r-lg bg-[#1E7773] border-2 border-[#1E7773] p-2 px-4 text-white"
                                type="submit"
                            >
                                <CiSearch size={24} />
                            </button>
                        </form>

                        <ul className="flex flex-wrap gap- 2xl:gap-4 justify-between text-sm xl:text-md cursor-pointer pt-3 font-semibold">
                            <NavLink
                                to="/"
                                className={({ isActive }) =>
                                    isActive ? "text-[#1E7773]" : "text-black "
                                }
                            >
                                <li className="hover:text-[#1E7773] font-bold px-2 rounded-lg duration-300">
                                    Home
                                </li>
                            </NavLink>

                            <NavLink
                                to="/shop/"
                                className={({ isActive }) =>
                                    isActive ? "text-[#1E7773]" : "text-black "
                                }
                            >
                                <li className="hover:text-[#1E7773] font-bold px-2 rounded-lg duration-300">
                                    Shop All
                                </li>
                            </NavLink>

                            <button
                                onClick={() => setIsCustomBtn(!isCustomBtn)}
                            >
                                <li className="hover:text-[#1E7773] font-bold px-2 rounded-lg duration-300">
                                    Custom Packaging
                                </li>

                                {isCustomBtn && (
                                    <div ref={dropdownRef} className="absolute flex flex-col gap-3 justify-start items-start bg-white rounded-lg p-2 my-3">
                                        <NavLink
                                            to="/inquiry/"
                                            className={({ isActive }) =>
                                                isActive
                                                    ? "text-[#1E7773]"
                                                    : "text-black "
                                            }
                                        >
                                            <li className="hover:text-[#1E7773] font-bold px-2 rounded-lg duration-300">
                                                Inquiry form
                                            </li>
                                        </NavLink>
                                        <NavLink
                                            onClick={() =>
                                                setIsCustomBtn(!isCustomBtn)
                                            }
                                            to="/customization/"
                                            className={({ isActive }) =>
                                                isActive
                                                    ? "text-[#1E7773]"
                                                    : "text-black "
                                            }
                                        >
                                            <li className="hover:text-[#1E7773] font-bold px-2 rounded-lg duration-300">
                                                Custom Packaging
                                            </li>
                                        </NavLink>
                                    </div>
                                )}
                            </button>

                            <NavLink
                                to="/bundles/"
                                className={({ isActive }) =>
                                    isActive ? "text-[#1E7773]" : "text-black "
                                }
                            >
                                <li className="hover:text-[#1E7773] font-bold px-2 rounded-lg duration-300">
                                    Bundles
                                </li>
                            </NavLink>

                            <NavLink
                                to="/about-us/"
                                className={({ isActive }) =>
                                    isActive ? "text-[#1E7773]" : "text-black "
                                }
                            >
                                <li className="hover:text-[#1E7773] font-bold px-2 rounded-lg duration-300">
                                    About Us
                                </li>
                            </NavLink>

                            <NavLink
                                to="/reviews/"
                                className={({ isActive }) =>
                                    isActive ? "text-[#1E7773]" : "text-black "
                                }
                            >
                                <li className="hover:text-[#1E7773] font-bold px-2 rounded-lg duration-300">
                                    Reviews
                                </li>
                            </NavLink>

                            <NavLink
                                to="/blog/"
                                className={({ isActive }) =>
                                    isActive ? "text-[#1E7773]" : "text-black "
                                }
                            >
                                <li className="hover:text-[#1E7773] font-bold px-2 rounded-lg duration-300">
                                    Blog
                                </li>
                            </NavLink>

                            <NavLink
                                to="/contact-us/"
                                className={({ isActive }) =>
                                    isActive ? "text-[#1E7773]" : "text-black "
                                }
                            >
                                <li className="hover:text-[#1E7773] font-bold px-2 rounded-lg duration-300">
                                    Contact Us
                                </li>
                            </NavLink>
                        </ul>
                    </div>
                </div>
                <ul className="lg:flex hidden flex flex-row items-center justify-center gap-2 text-sm  cursor-pointer">
                    {user && (
                        <Link to="/wishlist">
                            <li className="relative">
                                <CiHeart className="text-black text-3xl" />
                                {wishlistCount > 0 && ( // Only show the count if it's greater than 0
                                    <p className="absolute flex justify-center items-center -right-1 -top-1 bg-[#1E7773] rounded-full h-4 w-4 text-white text-[9px]">
                                        {wishlistCount}
                                    </p>
                                )}
                            </li>
                        </Link>
                    )}
                    {!user ? (
                        <Link to="/register/">
                            <CiUser className="text-black text-3xl" />
                        </Link>
                    ) : (
                        <Link to="/profile">
                            <img
                                // src={user ? `${Profile_Assets_Url}${user.photo}` : dummyProfilePic}
                                src={user?.photo ? `${Profile_Assets_Url}/${user.photo}` : dummyProfilePic}
                                alt="Profile"
                                className="w-10 h-10 rounded-full object-cover"
                            // onClick={handleLogout}
                            />
                        </Link>
                    )}
                    <Link to="/cart/">
                        {/* <li className='flex'>
                            <RiShoppingBasket2Line className='bg-[#1E7773] rounded-lg text-white p-1 text-3xl' />
                            <p className='text-xs px-2'> <span className='text-[15px] font-semibold'>My Cart</span> <br /> 0 items-Rs0.00 </p>
                        </li> */}
                        <li className="flex">
                            <RiShoppingBasket2Line className="bg-[#1E7773] rounded-lg text-white p-1 text-3xl" />
                            <p className="text-xs px-2">
                                <span className="text-[15px] font-semibold">
                                    My Cart
                                </span>{" "}
                                <br />
                                {totalItems} items - Rs {subtotal}
                            </p>
                        </li>
                    </Link>
                </ul>
            </div>
            {/* Mobile Menu */}
            <div
                className={`fixed mt-24 inset0 z-50 flex flex-col gap-4 justify-center w-full text-center pt-4 pb-8 px-4 transition-transform duration-300 bg-[#1E7773] bg-opacity-100 focus:outline-none overflow-y-auto ${mobMenu ? "translate-x-0" : "translate-x-full"
                    }`}
            >
                <form className="relative flex">
                    <input
                        className="w-full pl-4 rounded-l-lg p-2"
                        type="text"
                        placeholder="Search Products.."
                    />
                    <button className="border rounded-r-lg bg-[#1E7773] p-2 px-4 text-white text-2xl hover:px-6 duration-300">
                        <CiSearch />
                    </button>
                </form>

                <ul className="flex flex-row items-center justify-center gap-5 text-sm cursor-pointer">
                    {user && (
                        <Link to="/wishlist" onClick={() => setMobMenu(false)}>
                            <li>
                                <CiHeart className="text-white text-3xl" />
                            </li>
                        </Link>
                    )}
                    {!user ? (
                        <Link to="/register/" onClick={() => setMobMenu(false)}>
                            <CiUser className="text-white text-3xl" />
                        </Link>
                    ) : (
                        <Link to="/profile">
                            <img
                                src={user.profile_picture || dummyProfilePic}
                                alt="Profile"
                                className="w-10 h-10 rounded-full object-cover"
                            // onClick={handleLogout}
                            />
                        </Link>
                    )}
                    {/* <li><CiHeart className='text-white text-3xl' /></li>

                    <Link onClick={() => setMobMenu(false)} to='/register'>
                        <li><CiUser className='text-white text-3xl' /></li>
                    </Link> */}
                    {/* <li className='flex items-start'>
            <RiShoppingBasket2Line className='bg-white text-[#1E7773] rounded-lg text-white p-1 text-3xl' />
            <p className='text-xs px-2 text-white'>
                <span className='text-[15px] font-semibold'>My Cart</span> <br /> 0 items - Rs0.00
            </p>
        </li> */}
                </ul>

                <ul className="flex flex-col gap-2 justify-between items-center text-md cursor-pointer pt-3 font-semibold">
                    <Link onClick={() => setMobMenu(false)} to="/">
                        <li className="text-white hover:text-black w-40 hover:bg-white p-2 rounded-lg duration-300">
                            Home
                        </li>
                    </Link>
                    <Link onClick={() => setMobMenu(false)} to="/shop/">
                        <li className="text-white hover:text-black w-40 hover:bg-white p-2 rounded-lg duration-300">
                            Shop All
                        </li>
                    </Link>
                    <Link onClick={() => setMobMenu(false)} to="/bundles/">
                        <li className="text-white hover:text-black w-40 hover:bg-white p-2 rounded-lg duration-300">
                            Bundles
                        </li>
                    </Link>
                    <Link onClick={() => setMobMenu(false)} to="/customization/">
                        <li className="text-white hover:text-black w-40 hover:bg-white p-2 rounded-lg duration-300">
                            Custom Packaging
                        </li>
                    </Link>
                    <Link onClick={() => setMobMenu(false)} to="/about-us/">
                        <li className="text-white hover:text-black w-40 hover:bg-white p-2 rounded-lg duration-300">
                            About Us
                        </li>
                    </Link>
                    <Link onClick={() => setMobMenu(false)} to="/reviews/">
                        <li className="text-white hover:text-black w-40 hover:bg-white p-2 rounded-lg duration-300">
                            Reviews
                        </li>
                    </Link>
                    <Link onClick={() => setMobMenu(false)} to="/blog/">
                        <li className="text-white hover:text-black w-40 hover:bg-white p-2 rounded-lg duration-300">
                            Blog
                        </li>
                    </Link>
                    <Link onClick={() => setMobMenu(false)} to="/contact-us/">
                        <li className="text-white hover:text-black w-40 hover:bg-white p-2 rounded-lg duration-300">
                            Contact Us
                        </li>
                    </Link>
                </ul>
            </div>
        </div >
    );
}

export default Header;
