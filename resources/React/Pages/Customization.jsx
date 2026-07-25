import React, { useEffect, useState } from 'react'
import CustomHeroSection from '../components/CustomHeroSection'
import PriceRange from '../components/Shop/PriceRange'
import { Assets_Url, Image_Not_Found, Image_Url } from '../const'
import { BiFilterAlt } from 'react-icons/bi'
import { RiFilter3Line } from 'react-icons/ri'
import PriceRangeMob from '../components/Shop/PriceRangeMob'
import { Link, useLocation, useParams } from 'react-router-dom'
import CustomPriceRange from '../components/Customizaton/CustomPriceRange'
import CustomPriceRangeMob from '../components/Customizaton/CustomPriceRangeMob'
import axios from '../Utils/axios'
import { Loader } from '../components/Loader'
import CustomSeo from '../components/CustomSeo'


function Customization() {
    const [grid, setGrid] = useState(3)
    const { category } = useParams(); // Get category from URL
    const location = useLocation();
    const searchParams = new URLSearchParams(location.search);
    const searchTermFromURL = searchParams.get('q');
    const [isFilter, setIsFilter] = useState(false)
    const [loading, setLoading] = useState(false);
    const [visibleProducts, setVisibleProducts] = useState(12);
    const [filteredProduct, setFilteredProduct] = useState([]);
    const [productVariants, setProductVariants] = useState([]);
    const [searchTerm, setSearchTerm] = useState(searchTermFromURL || ''); // Use search term from URL or empty string
    const [filter, setFilter] = useState({
        price_from: 0, // Default min price
        price_to: 0, // Default max price
        sort_by: 1, // Default sorting (A to Z)
        category_Id: [], // Set to undefined if category is not available
        pack_size: [], // Set to undefined if category is not available
        size_id: [], // Set to undefined if category is not available
        option_id: [], // Set to undefined if category is not available
        rating: [], // Set to undefined if category is not available
    });

    const handleResize = () => {
        const screenWidth = window.innerWidth;
        if (screenWidth < 400) {
            setGrid(1); // 1 column on very small screens
        } else if (screenWidth < 768) {
            setGrid(2); // 2 columns on tablets
        } else if (screenWidth < 1024) {
            setGrid(3); // 3 columns on medium screens
        } else {
            setGrid(3); // 4 columns on large screens
        }
    };


    // useEffect to add the resize event listener
    useEffect(() => {
        handleResize();
        window.addEventListener('resize', handleResize);
        return () => {
            window.removeEventListener('resize', handleResize);
        };
    }, []);

    // Fetch data based on the current filter state
    const fetchData = async () => {
        setLoading(true);
        try {
            console.log('filter', filter);
            // Initialize the URLSearchParams
            const params = new URLSearchParams();

            // Add other filters to the params
            params.append('price_from', filter.price_from);
            params.append('price_to', filter.price_to);
            params.append('sort_by', filter.sort_by);
            // params.append('category_id', filter.category_Id);
            // if (Array.isArray(filter.category_Id)) {
            //     filter.category_Id.forEach((cat) => {
            //         params.append('category_id', cat);
            //     });
            // }
            if (Array.isArray(filter.category_Id)) {
                filter.category_Id.forEach((id) => {
                    params.append('category_id[]', id); // Append each ID separately
                });
            } else {
                console.error('category_Id is not an array:', filter.category_Id);
            }



            // params.append('pack_size', filter.pack_size);
            // Ensure pack_size is an array before using forEach
            if (Array.isArray(filter.pack_size)) {
                filter.pack_size.forEach((pack) => {
                    params.append('pack_size', pack);
                });
            }

            // Ensure size is an array before using forEach
            if (Array.isArray(filter.size)) {
                filter.size.forEach((siz) => {
                    params.append('size_id', siz);
                });
            }

            // Ensure option_id is an array before using forEach
            if (Array.isArray(filter.option_id)) {
                filter.option_id.forEach((opt) => {
                    params.append('option_id', opt);
                });
            }

            // Ensure rating is an array before using forEach
            if (Array.isArray(filter.rating)) {
                filter.rating.forEach((rate) => {
                    params.append('rating', rate);
                });
            }


            // Log the final URL for debugging
            console.log('Fetching data with URL:', `search/Customizeproduct?${params.toString()}`);

            // Make the API request
            const response = await axios.public.get(`search/Customizeproduct?${params.toString()}`);

            // Update the filtered products
            setFilteredProduct(response.data.data);
        } catch (error) {
            console.error('Error fetching products:', error);
        } finally {
            setLoading(false);
        }
    };

    // Update searchTerm when the URL changes
    useEffect(() => {
        setSearchTerm(searchTermFromURL || ''); // Update state with new search term
    }, [location.search]); // Listen for changes in the URL's search parameters

    // Update filter based on category changes
    useEffect(() => {
        setFilter(prev => ({
            ...prev,
            category_Id: category || undefined,
        }));
    }, [category]); // Update filter when category changes

    // Fetch data when filter or searchTerm changes
    useEffect(() => {
        if (searchTerm || filter.price_from > 0 || filter.price_to > 0 || filter.category_Id || filter.pack_size) {
            const delayDebounceFn = setTimeout(() => {
                fetchData();
            }, 300); // 300ms debounce delay

            return () => clearTimeout(delayDebounceFn); // Cleanup the timeout on component unmount
        }
    }, [filter, searchTerm]); // Ensure effect runs when these change

    // Fetch data when the component mounts and when the category or search term changes
    useEffect(() => {
        fetchData();
    }, [category, searchTerm]); // Ensure effect runs on category and search term change

    // Fetch data whenever the filter state changes
    useEffect(() => {
        fetchData();
    }, [filter]);  // Add filter as a dependency

    const handleFilter = (filters) => {
        setFilter((prev) => ({
            ...prev,
            // ...filter,
            price_from: filters.price_from || filter.price_from,
            price_to: filters.price_to || filter.price_to,
            sort_by: filters.selected || filter.sort_by,
            category_Id: filters.category_Id || filter.category_Id,
            pack_size: filters.pack_size || filter.pack_size,
            size: filters.size || filter.size,
            option_id: filters.option_id || filter.option_id,
            rating: filters.rating || filter.rating
        }));
    };

    // Example handleRating function for checkbox selection
    // const handleRating = (id) => {
    //     setFilter((prev) => ({
    //         ...prev,
    //         rating: prev.rating.includes(id) 
    //             ? prev.rating.filter(rate => rate !== id) 
    //             : [...prev.rating, id],
    //     }));
    // };
    // Load more products
    const handleLoadMore = () => {
        setVisibleProducts((prevVisibleProducts) => prevVisibleProducts + 12); // Show 12 more products each time
    };


    return (
        <div className="py-24">
            <CustomSeo id={3} />
            <CustomHeroSection heading='Build Your Perfect Match' path='Customization ' custom="customization" bgImage="HomeAssets/HeroSecton/Banner3.png" />
            <div className="md:py-20 py-10 lg:px-10 px-0 flex">
                <section className="hidden lg:flex flex flex-col p-5 text-white hscreen lg:w-1/5">
                    <CustomPriceRange onFilter={handleFilter} />
                </section>
                <div className="">
                    <CustomPriceRangeMob onFilter={handleFilter} isFilter={isFilter} setIsFilter={setIsFilter} />
                </div>
                <section className="flex     p-5 hscreen lg:w-4/5 w-full">
                    <div className="py-4 w-full flex flex-col gap2 text-white rounded-lg">
                        <div className="flex justify-between">
                            <h4 className='text-4xl font-bazaar'>Shop All</h4>
                            <div className="">
                                <button onClick={() => setIsFilter(true)}>
                                    <RiFilter3Line className='lg:hidden block text-4xl rounded-full p-2 bg-[#1E7773]' />
                                </button>
                                <div className="hidden lg:flex justify-between gap-3 items-center">
                                    <h4 className='text-md font-bazaar'>View</h4>
                                    <img onClick={() => setGrid(4)} className='cursor-pointer' src={`${Image_Url}${grid === 4 ? 'ShopAssets/4greenGridImg.svg' : 'ShopAssets/4gridImg.svg'}`} alt="" />
                                    <img onClick={() => setGrid(3)} className='cursor-pointer' src={`${Image_Url}${grid === 3 ? 'ShopAssets/3greenGridImg.svg' : 'ShopAssets/3gridImg.svg'}`} alt="" />
                                    <img onClick={() => setGrid(2)} className='cursor-pointer' src={`${Image_Url}${grid === 2 ? 'ShopAssets/2greenGridImg.svg' : 'ShopAssets/2gridImg.svg'}`} alt="" />
                                </div>
                            </div>
                        </div>

                        {loading ? (
                            <div className="flex justify-center py-10">
                                <Loader />
                            </div>
                        ) : filteredProduct?.length === 0 ? (
                            <div className="flex justify-center h-screen items-center py-10">
                                <h4 className='text-4xl font-bazaar'>No products found</h4>
                            </div>
                        ) : (
                            <>
                                <div className={`py-10 grid ${grid === 4 ? 'grid-cols-4' : grid === 3 ? 'grid-cols-3' : grid === 2 ? 'grid-cols-2' : 'grid-cols-1'} gap-4 justify-center w-full`}>
                                    {filteredProduct?.slice(0, visibleProducts).map((product, index) => (
                                        <Link key={index}
                                            to={`/customization/${product.slug}`}
                                        >
                                            <div className={`flex ${grid === 2 && index % 2 === 0 ? 'justify-end' : 'justify-start'}`}>
                                                <div className={`w-${grid === 2 ? 'fit w-82 h-full' : 'full'} xl:p-4 h76 p-2 flex flex-col border border-[#1E7773] bg-[#32303e] rounded-2xl  group`}>
                                                    <div className="relative p-5 flex flex-col justify-center items-center">
                                                        <img
                                                            className=" w-full h-full block group-hover:hidden rounded-xl object-cover"
                                                            src={product.product_image ? `${Assets_Url}${product.product_image[0]?.image}` : `${Image_Url}defaultImage.svg`}
                                                            alt={product.product_image[0]?.image_alt || 'Product Image'}
                                                            style={{ transition: 'opacity 0.5s ease 0.3s' }}
                                                            loading='lazy'
                                                            onError={(e) => {
                                                                e.currentTarget.src = Image_Not_Found; // Path to your dummy image
                                                              }}
                                                        />
                                                        <img
                                                            className=" w-full h-full hidden group-hover:block rounded-xl object-cover"
                                                            src={product.product_image ? `${Assets_Url}${product.product_image[1]?.image}` : `${Image_Url}defaultImage.svg`}
                                                            alt={product.product_image[1]?.image_alt || 'Product Image'}
                                                            style={{ transition: 'opacity 0.5s ease 0.3s' }}
                                                            loading='lazy'
                                                            onError={(e) => {
                                                                e.currentTarget.src = Image_Not_Found; // Path to your dummy image
                                                              }}
                                                        />
                                                    </div>
                                                    <h4 className='font-semibold xl:text-lg'>{`${product.name}`}</h4>

                                                    <p className='text-md py-3 font-semibold'>
                                                        {product.product_variants && product.product_variants?.length > 0 ? (
                                                            <>
                                                                Rs {product.product_variants[0].price} - Rs {product.product_variants[product.product_variants?.length - 1].price}
                                                            </>
                                                        ) : (
                                                            <span>No variants available</span>
                                                        )}
                                                    </p>
                                                </div>
                                            </div>
                                        </Link>
                                    ))}
                                </div>

                                {/* Show "Load More" button if there are more products to display */}
                                {filteredProduct?.length > 12 && visibleProducts < filteredProduct?.length ? (
                                    <div className="flex justify-center">
                                        <button className='p-2 px-4 bg-[#1E7773] w-fit lg:text-md pt-3 text-md font-bazaar rounded-lg' onClick={handleLoadMore}>
                                            LOAD MORE
                                        </button>
                                    </div>
                                ) : (
                                    <div className="flex justify-center">
                                        <p>No More Products</p>
                                    </div>
                                )}
                            </>
                        )}
                        {/* <div className="flex justify-center">
                            <button className='p-2 px-4 bg-[#1E7773] w-fit lg:text-sm text-md font-semibold rounded-lg'>Load more</button>
                        </div> */}
                    </div>
                </section>
            </div >
        </div>
    )
}

export default Customization
