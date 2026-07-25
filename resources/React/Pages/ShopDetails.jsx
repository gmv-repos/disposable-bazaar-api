import React, { useContext, useEffect, useRef, useState } from 'react';
import { Link, useLocation, useNavigate, useParams } from 'react-router-dom'; // Import useParams from react-router-dom
import { Assets_Url, Image_Url } from '../const';
import axios from '../Utils/axios';
import { FaAngleDown, FaCircle, FaWhatsapp } from 'react-icons/fa';
import RcmdProduct from '../components/Shop/RcmdProduct';
import Deals from '../components/Home/Deals';
import Review from '../components/Reviews/Review';
import { Loader } from '../components/Loader';
// import { useContext } from 'react';
// import { WishlistContext } from '/resources/react/Context/WishlistContext.jsx';
import { useWishlist } from '../Context/WishlistContext';
import { useCart } from '../Context/CartContext';
import { toast, ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css'; // Import the CSS for Toastify
import { useUser } from '../Context/UserContext';
import { CartModal } from '../components/cart/CartModal';
import { FiX } from 'react-icons/fi';
import CustomDetailSeo from '../components/CustomDetailSeo';
import DecodeTextEditor from '../components/DecodeTextEditor';

function ShopDetails() {
    const [productDetail, setProductDetail] = useState([]);
    const [productVariants, setProductVariants] = useState([]);
    const [selectedProductVariants, setSelectedProductVariants] = useState([]);
    const [productLids, setProductLids] = useState([]);
    const [recomendedProducts, setRecomendedProducts] = useState([]);
    const [brands, setBrands] = useState([]);
    const [selectedBrands, setSelectedBrands] = useState();
    const [selectedBrandId, setSelectedBrandId] = useState();
    const [productReview, setProductReview] = useState([]);
    const [productImages, setProductImages] = useState([]);
    const [productId, setProductId] = useState(0);
    const [selectedImage, setSelectedImage] = useState(''); // State to track the selected image
    const [quantity, setQuantity] = useState(null);
    const [selectedVariantId, setSelectedVariantId] = useState(null);
    const [selectedVariantPrice, setSelectedVariantPrice] = useState(0);
    const [selectedVariant, setSelectedVariant] = useState(0);
    const [selectedLidId, setSelectedLidId] = useState(null);
    const [selectedLidPrice, setSelectedLidPrice] = useState(0);
    const [selectedLid, setSelectedLid] = useState(0);
    const [subQuantity, setSubQuantity] = useState(1);
    const [productTextDetail, setProductTextDetail] = useState('Description');
    const [isLoading, setIsLoading] = useState(false); // New state for loading
    const [brandsOpen, setBrandsOpen] = useState(false); // New state for loading
    const { addToWishlist } = useWishlist();  // Access the addToWishlist function from context
    const { addToCart } = useCart();
    const [isCartModalOpen, setIsCartModalOpen] = useState(false);
    const { user } = useUser();
    const [IsCustomizeable, setIsCustomizeable] = useState([])
    const navigate = useNavigate()
    const dropdownRef = useRef(null);

    // Close dropdown if clicked outside
    useEffect(() => {
        function handleClickOutside(event) {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                setBrandsOpen(false);
            }
        }

        document.addEventListener("mousedown", handleClickOutside);
        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
        };
    }, []);

    // Fetch productId from the URL using useParams
    // const { id } = useParams();
    const location = useLocation();

    // Extract slug from the pathname
    let path = location.pathname;

    // Ensure the URL ends with a slash
    if (!path.endsWith('/')) {
        path += '/';
    }
    const id = path.split("/product/")[1];

    const fetchDataById = async (id) => {

        setIsLoading(true);
        try {
            const response = await axios.public.post(`product/s/details`, {
                slug: id,
            });
            const resData = response.data.data
            console.log("resdata", resData);
            // console.log("is_customizeable", resData.product.product.childProducts[0]);
            const hasChildProducts = productDetail.product?.childProducts?.length > 0;
            console.log("resData?.product?.childProducts", resData?.product?.childProducts);

            if (hasChildProducts) {
                console.log("resData Show button");
            } else {
                console.log("resData Hide button");
            }


            setProductDetail(resData);
            setSelectedImage(resData?.product.product_image[0].image || ''); // Set initial image
            setProductImages(resData?.product.product_image)
            setRecomendedProducts(resData.recommended_products)
            setBrands(resData.product.product_brands.filter(i => i.status === 1))
            setSelectedBrands(resData.product.product_brands.filter(i => i.status === 1)[0]?.name)
            setSelectedBrandId(resData.product.product_brands.filter(i => i.status === 1)[0]?.id)
            setProductVariants(resData.product.product_variants)
            setProductLids(resData.product.product_lid_options)
            setProductId(resData.product.id)
            const seletedBrandId = resData.product.product_brands.filter(i => i.status === 1)[0]?.id
            if (resData.product.product_variants.filter(i => i.brand_id === seletedBrandId)) {
                console.log(true);
                setSelectedProductVariants(resData.product.product_variants.filter(i => i.brand_id === seletedBrandId))
            }
            const firstVariant = resData.product.product_variants[0]; c

            setIsCustomizeable(productDetail.product?.childProducts?.length > 0)


            if (firstVariant) {
                setQuantity(firstVariant.pack_size);
                setSelectedVariantId(firstVariant.id); // Set the default selected variant
                setSelectedVariant(firstVariant.pack_size)
                setSelectedVariantPrice(firstVariant.price_per_piece);
            }
            const price = quantity && selectedVariantPrice && (quantity * subQuantity * selectedVariantPrice)
            // console.log('Price', selectedVariantPrice);
            // console.log('P', quantity);


            setIsLoading(false);
        } catch (error) {
            console.log('Error fetching product details:', error);
        } finally {

        }
    };
    const handleCategoryLink = (item) => {
        // console.log('id', item.subCategories);
        // setCategory(item);
        // console.log('id', category);

        navigate(`/product-category/${item.slug}`, { state: item.id })
    }


    const fetchReviewById = async (id) => {

        setIsLoading(true);
        try {
            const response = await axios.public.get(`product_reviews/${id}`);
            setProductReview(response.data)
            // console.log('shop', productReview);

            // console.log('productReview', productReview);
        } catch (error) {
            console.log('Error fetching product review:', error);
        } finally {
            setIsLoading(false);
        }
    };

    // Fetch product details by ID when the component mounts

    useEffect(() => {
        if (id) {
            fetchDataById(id); // Call the function with the ID
            fetchReviewById(id)
        }
    }, [id]);

    useEffect(() => {
        console.log("selectedBrands", selectedBrands);
        console.log("selectedBrandId", selectedBrandId);
        console.log('productLids', productLids)
    },);
    // const handleWishlist = (id) => {
    //     if (!user) {
    //         navigate('/login')
    //     }


    //     const fetchData = async () => {
    //         try {
    //             const response = await axios.protected.post(`user/wishlist/${id}/add`);
    //             if (id) {
    //                 // Call addToWishlist to update the wishlist count in the context
    //                 addToWishlist();
    //             }
    //         } catch (error) {
    //             console.log(error);
    //         }

    //         toast.success(` added to cart`);
    //     };

    //     fetchData();
    // };
    const handleWishlist = async (id) => {
        if (!user) {
            navigate('/login/');
            return;
        }

        try {
            // Make a request to check if the item is already in the wishlist
            const wishlistResponse = await axios.protected.get(`/user/wishlist/${id}/check`);
            // console.log(wishlistResponse.data);

            if (wishlistResponse.data.exists) {
                toast.error('Product already added to wishlist');
            } else {
                // If product is not in wishlist, proceed to add it
                const response = await axios.protected.post(`/user/wishlist/${id}/add`);
                if (response.status === 200) {
                    addToWishlist(); // Call to update wishlist count in context
                    toast.success('Product added to wishlist');
                }
            }
        } catch (error) {
            console.log(error);
            toast.error('An error occurred while adding to wishlist');
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
    };
    const handleSelectedBrand = (data) => {
        setSelectedBrands(data.name)
        setSelectedBrandId(data.id)
        setSelectedProductVariants(productVariants.filter(i => i.brand_id === data.id))

        const firstVariant = productVariants.filter(i => i.brand_id === data.id)[0];

        if (firstVariant) {
            setQuantity(firstVariant.pack_size);
            setSelectedVariantId(firstVariant.id); // Set the default selected variant
            setSelectedVariant(firstVariant.pack_size)
            setSelectedVariantPrice(firstVariant.price_per_piece);
        }
    };

    const handleImageClick = (image) => {
        setSelectedImage(image); // Update the selected image when clicked
    };

    // Handle Add to Cart Logic
    const handleAddCart = (product) => {
        const product_id = product.id;
        const product_name = product.name;
        const pack_size = selectedVariant;
        const product_quantity = subQuantity;
        const total_pieces = selectedVariant * subQuantity;
        const price_per_piece = selectedVariantPrice;

        // Calculate base total
        const baseTotal = (quantity * subQuantity * selectedVariantPrice) + (quantity * subQuantity * selectedLidPrice);

        // Apply discount if available
        let finalTotal = baseTotal;
        const discountPercentage = parseFloat(product?.activeDiscount?.discount_percentage);
        if (!isNaN(discountPercentage) && discountPercentage > 0) {
            finalTotal = baseTotal - (baseTotal * (discountPercentage / 100));
        }
        console.log('order-limit', product.order_limit)
        const product_total = finalTotal.toFixed(2);
        // const product_total = ((quantity * subQuantity * selectedVariantPrice) + (quantity * subQuantity * selectedLidPrice)).toFixed();
        const product_img = product.image_path;
        const product_variants = selectedProductVariants;
        // New
        const product_lids = productLids ? productLids : null;
        const lid = selectedLidId ? selectedLidId : null;
        const lid_Price = selectedLidPrice ? selectedLidPrice : 0;
        const product_color = null;
        const product_size = null;
        const product_options = null;
        const option_Price = 0;
        const logo = null;
        const order_limit = product?.order_limit !== null ? product?.order_limit : 1000;


        // Add the product to the cart
        // addToCart(product_id, product_name, product_quantity, pack_size, total_pieces, price_per_piece, product_img, product_total, product_variants, product_color, product_size, logo, product_options, product_lids, lid, lid_Price, option_Price, order_limit);
        addToCart(
            product_id,
            product_name,
            product_quantity,
            pack_size,
            total_pieces,
            price_per_piece,
            product_img,
            product_total,
            product_variants,
            product_color,
            product_size,
            logo,
            product_options,
            product_lids,
            lid,
            lid_Price,
            null,           // customizeDetail
            option_Price,
            false,          // bundle_status
            order_limit
        );
        setIsCartModalOpen(true);
        // Show success toast
        // toast.success(`${product.name} added to cart`);
    };

    const whatsappNumber = "+923213850002"; // Replace with your WhatsApp number (in international format without + or 00)
    const productUrl = window.location.href; // Replace with the product's web URL
    const inquiryMessage = encodeURIComponent(
        `Hello! I am interested in the following product:\n\n${productDetail.product?.name}\n\n ${productUrl}`
    );

    if (isLoading) return <Loader />;
    return (
        <div className="relative py-32 px-10 text-white overflow-hidden">
            <CustomDetailSeo
                title={productDetail?.seoMetadata?.meta_title}
                des={productDetail?.seoMetadata?.meta_description}
                focuskey={productDetail?.seoMetadata?.focus_keyword}
                canonicalUrl={productDetail?.seoMetadata?.canonical_url}
                schema={productDetail?.seoMetadata?.schema}
                og_title={productDetail?.product?.name}
                og_des={productDetail?.product?.description}
                og_img={productDetail?.product?.product_image[0]?.image}
            />
            {/* {console.log('product', productDetail)} */}
            <ToastContainer autoClose={500} />
            {/* Breadcrumb and Title */}
            <div className="flex flex-col py-5">
                <p><Link to='/'>Home</Link> / <Link to='/shop/'>Shop</Link> /  <span
                    onClick={() => handleCategoryLink(productDetail.product?.category)}
                    className="inline cursor-pointer  "
                >
                    {/* <Link to={`/product-category/${productDetail?.product?.category?.slug}`}> */}
                        {productDetail.product?.category?.name || "Category Name"}
                    {/* </Link> */}
                </span>
                    / {productDetail.product?.subCategory?.name ? <> <Link to='/'> {productDetail.product?.subCategory.name || 'Category Name'} </Link> /</> : ""} {productDetail.product?.name || 'Product Name'}</p>
            </div>
            <main className=''>
                <section className='flex lg:flex-row flex-col gap-8'>

                    <div className="lg:w-3/5 md:h-[34rem] h-[20rem] flex flex-row md:gap-5 gap-2">
                        {/* Thumbnails */}
                        <div className="w-1/5 flex flex-col gap-1">

                            {Array.isArray(productImages) && productImages.length === 0 ? (
                                // If productImages is an empty array, show the default image
                                <div className="w-full h-1/4 py-1">
                                    <img
                                        className="w-full h-full bg-[#32303e] rounded-xl border-2 border-[#1E7773] object-cover cursor-pointer"
                                        src={`${Image_Url}defaultImage.svg`} // Default image when no products
                                        alt="Default Product Image"
                                    />
                                </div>
                            ) : (
                                // If productImages is not empty, map over the images and display them
                                productImages.slice(0, 4).map((prod, index) => (
                                    <div key={index} className="w-full h-1/4 py-1">
                                        <img
                                            className="w-full h-full bg-[#32303e] rounded-xl border-2 border-[#1E7773] object-cover cursor-pointer"
                                            // If the prod.image array is empty, use the default image; otherwise, use the first image in the array
                                            src={`${Assets_Url}${prod.image}`}
                                            alt={prod?.image_alt || 'Product Image'}
                                            onClick={() => handleImageClick(prod.image)} // Set clicked image (first image in the array)
                                        />

                                    </div>
                                ))
                            )}

                        </div>

                        {/* Large Image Display */}
                        <div className="w-4/5 rounded-lg bg-[#32303e]">
                            {selectedImage ? (
                                <img
                                    className="w-full h-full object-cover rounded-lg"
                                    src={`${Assets_Url}${selectedImage}`} // Show selected image
                                    alt={productImages[0]?.image_alt || 'Product Image'}
                                />
                            ) : (
                                <img
                                    className="w-full h-full object-cover rounded-lg"
                                    src={`${Image_Url}defaultImage.svg`} // Show default image if no image selected
                                    alt={productImages[0]?.image_alt || 'Product Image'}
                                />
                            )}
                        </div>
                    </div>

                    <div className="lg:w-2/5 flex flex-col gap-5 text-white h[300px]">
                        <div>
                            <p className='text-sm text-gray-300'>{productDetail.product?.stock_status == 1 ? "In Stock" : "Out Of Stock"}</p>
                            <h1 className='md:text-5xl text-3xl font-semibold'>
                                {productDetail.product?.name || 'Product Name'}
                            </h1>
                        </div>
                        <h3 ref={dropdownRef} onClick={() => setBrandsOpen(!brandsOpen)} className='relative md:text-xl flex flex-row  gap-10 cursor-pointer items-center text-md font-semibold border p-2 rounded-lg px-4 w-fit '>
                            Brand : {selectedBrands || 'Brand Name'}
                            <FaAngleDown className={`${brandsOpen ? 'rotate-180' : ''} duration-300`} />

                            {brandsOpen && (
                                <div className="absolute top-12 left-0 py-2 overflow-auto rounded-lg z-10 w-full h-32 bg-white">
                                    {brands.map((data) => (
                                        <div onClick={() => handleSelectedBrand(data)} className="text-black px-4 py-1 text-md hover:bg-gray-200 duration-100">
                                            {data.name}
                                        </div>
                                    ))}
                                </div>
                            )}
                        </h3>
                        <p className='text-xl font-semibold'>
                            {/* {productVariants && productVariants.length > 0 ? (
                                <>
                                    Rs {productVariants[0].price} - {productVariants[productVariants.length - 1].price}
                                    ₨ {quantity && selectedVariantPrice && (quantity * subQuantity * selectedVariantPrice)}
                                </>
                            ) : (
                                <span>No variants available</span>
                            )} */}
                            {selectedProductVariants[0]?.price} Rs
                        </p>

                        <form onSubmit={handleSubmit} className='max-w-96  w-fit flex flex-col gap-5 '>
                            {/* Quantity Selection */}
                            <div className="my-form border w-full border-[#1E7773] rounded-full flex items-stretch">
                                <p className="my-form-heading bg-[#1E7773] rounded-l-full h-full p-1 px-5 flex items-center">Pieces</p>
                                <div className="flex flex-wrap gap-4 justify-start p-1 px-2 items-center">
                                    {/* <div className="flex flex-row items-center justify-center gap-2">
                                        <input onClick={() => setQuantity(25)} defaultChecked id="1" type="radio" name="option" />
                                        <label htmlFor="1">25 Pcs</label>
                                    </div>
                                    <div className="flex flex-row pr-2 items-center justify-center gap-2">
                                        <input onClick={() => setQuantity(50)} id="2" type="radio" name="option" />
                                        <label htmlFor="2">50 Pcs</label>
                                    </div>
                                    <div className="flex flex-row items-center justify-center gap-2">
                                        <input onClick={() => setQuantity(100)} id="3" type="radio" name="option" />
                                        <label htmlFor="3">100 Pcs</label>
                                    </div> */}
                                    {productVariants && productVariants.length > 0 ? (
                                        productVariants.map((variant) => (
                                            <div key={variant.id} className="flex flex-row items-center justify-center pr-3 gap-2">
                                                <input
                                                    onClick={() => {
                                                        setQuantity(variant.pack_size);
                                                        setSelectedVariantId(variant.id); // Set selected variant on click
                                                        setSelectedVariantPrice(variant.price_per_piece);
                                                        setSelectedVariant(variant.pack_size);
                                                    }}
                                                    id={variant.id}
                                                    type="radio"
                                                    name="option"
                                                    checked={selectedVariantId === variant.id}
                                                />
                                                {console.log('inner', variant)}
                                                <label htmlFor={variant.id}>{variant.pack_size} Pcs</label>
                                            </div>
                                        ))
                                    ) : (
                                        <p>No variants available.</p> // Message if no variants exist
                                    )}
                                </div>
                            </div>

                            <div className='flex flex-row gap-3'>
                                {/* <div className="border border-[#1E7773] rounded-md flex flex-row justify-between items-center px-2 w-24 h-10">
                                    <button disabled={subQuantity === 1} onClick={() => setSubQuantity(subQuantity - 1)}>-</button>
                                    <p className=''>{subQuantity}</p>
                                    <button disabled={subQuantity >= productDetail.product?.order_limit} onClick={() => setSubQuantity(subQuantity + 1)}>+</button>
                                </div> */}
                                <div className="border border-[#1E7773] rounded-md flex flex-row justify-between items-center px-2 w-24 h-10">
                                    <button
                                        disabled={subQuantity === 1}
                                        onClick={() => setSubQuantity(subQuantity - 1)}
                                    >
                                        -
                                    </button>

                                    <p>{subQuantity}</p>

                                    <button
                                        onClick={() => {
                                            const limit = productDetail.product?.order_limit !== null ? productDetail.product?.order_limit : 1000;
                                            if (subQuantity < limit) {
                                                setSubQuantity(subQuantity + 1);
                                            } else {
                                                toast.warning(`Maximum order limit (${limit}) reached!`);
                                            }
                                        }}
                                    >
                                        +
                                    </button>
                                </div>

                                <button className='p-2 pt-3 bg-[#1E7773] w-full lg:text-[15px] font-bazaar text-xs rounded-md'
                                    onClick={() => handleAddCart(productDetail.product)}>
                                    ADD TO CART
                                </button>
                            </div>

                            {/* Product Lids Selection*/}
                            {productLids && productLids.length > 0 && (
                                <>
                                    <div className=" my-form border rounded-lg h32 w-6/7 md:w-96 border-[#1E7773]">
                                        <p className="bg-[#1E7773] rounded-t-lg py-0.5 px-5">Lids</p>
                                        <div className="flex flex-wrap gap-4 justify-start p-3 items-center">
                                            {/* <div className="flex flex-row items-center justify-center gap-2">
                                        <input onClick={() => setQuantity(25)} defaultChecked id="1" type="radio" name="option" />
                                        <label htmlFor="1">25 Pcs</label>
                                    </div>
                                    <div className="flex flex-row pr-2 items-center justify-center gap-2">
                                        <input onClick={() => setQuantity(50)} id="2" type="radio" name="option" />
                                        <label htmlFor="2">50 Pcs</label>
                                    </div>
                                    <div className="flex flex-row items-center justify-center gap-2">
                                        <input onClick={() => setQuantity(100)} id="3" type="radio" name="option" />
                                        <label htmlFor="3">100 Pcs</label>
                                    </div> */}
                                            <input
                                                onClick={() => {
                                                    // setQuantity(lid.pack_size);
                                                    setSelectedLidId(null); // Set selected lid on click
                                                    setSelectedLidPrice(null);
                                                    setSelectedLid(null);
                                                    setSelectedImage(productDetail?.product?.product_image[0]?.image);
                                                    // console.log(selectedLidId, "-", lid.id)
                                                }}
                                                id={123}
                                                type="radio"
                                                name="lid"
                                                checked={selectedLidId === null}
                                            />
                                            <label htmlFor={123}>No Lids</label>
                                            {productLids && productLids.length > 0 ? (
                                                productLids.map((lid) => (
                                                    <div key={lid.id} className="flex flex-row items-center justify-center pr-3 gap-2">
                                                        <input
                                                            onClick={() => {
                                                                // setQuantity(lid.pack_size);
                                                                setSelectedLidId(lid.id); // Set selected lid on click
                                                                setSelectedLidPrice(lid.price);
                                                                setSelectedLid(lid.name);
                                                                setSelectedImage(lid.image);
                                                                console.log(selectedLidId, "-", lid.id)
                                                            }}
                                                            id={lid.id}
                                                            type="radio"
                                                            name="lid"
                                                            checked={selectedLidId === lid.id}
                                                        />
                                                        <label htmlFor={lid.id}>{lid.name} Pcs</label>
                                                    </div>
                                                ))
                                            ) : (
                                                <p>No lids available.</p> // Message if no variants exist
                                            )}
                                        </div>
                                    </div>
                                    {(selectedLidId && selectedVariant > 0) && <p className='text-sm'>Lids Pieces {selectedVariant}</p>}
                                </>
                            )}


                        </form>



                        {/* <p>₨ {quantity && subQuantity && (quantity * subQuantity * productDetail.product?.current_sale_price)} / Per Pieces : {productDetail.product?.current_sale_price}</p> */}
                        <div className=''>
                            <p className='text-sm'><span className='text-lg text-bolder'>₨ {selectedLid ? quantity && selectedVariantPrice && selectedLidPrice && ((quantity * subQuantity * selectedVariantPrice) + (quantity * subQuantity * selectedLidPrice)) :
                                quantity && selectedVariantPrice && (quantity * subQuantity * selectedVariantPrice)} / </span>Per Piece: ₨ {Number(selectedVariantPrice) + Number(selectedLidPrice)}</p>
                            {productDetail.product?.activeDiscount && (<p className='text-sm '>{Number(productDetail.product?.activeDiscount?.discount_percentage)}% OFF ( {productDetail.product?.activeDiscount?.name} )</p>)}
                        </div>
                        <div className="flex flex-row md:gap-5 gap-2">
                            <button onClick={() => handleWishlist(productDetail.product?.id)} className='p-2 pt-3 border-b-4 border-[#1E7773] w-32 lg:text-[15px] font-bazaar text-xs'>ADD TO WISHLIST</button>
                            <button className='p-3 border flex flex-row justify-between items-center gap-2  border-[#1E7773] w32 lg:text-[15px]  font-bazaar text-xs rounded-md'
                                onClick={() => window.open(`https://wa.me/${whatsappNumber}?text=${inquiryMessage}`, '_blank')}>
                                <FaWhatsapp className='text-[#1E7773] text-2xl' /> <p className="pt-2">ORDER ON WHATSAPP</p>
                            </button>
                        </div>
                        {productDetail.product?.childProducts?.length > 0 ? <Link to={`/customization/${productDetail.product?.childProducts[0]?.slug}`}>
                            <button className='p-3 pt-4 bg-[#1E7773] w-52 lg:text-[15px] font-bazaar text-xs rounded-md'>CUSTOMIZED PRINTING</button>
                        </Link> : null
                        }
                        {/* <Link to={`/customization/${productDetail.product?.slug}`}>
                            <button className='p-3 pt-4 bg-[#1E7773] w-52 lg:text-[15px] font-bazaar text-xs rounded-md'>CUSTOMIZED PRINTING</button>
                        </Link> */}
                    </div>
                </section>

                {/* Product Description and Additional Information */}
                <section className='flex flex-col gap-8 md:py-20 py-5'>
                    <div className="flex flexrow w-full border-b border-[#1E7773] justify-center items-center">
                        <div className="flex flex-row justify-center md:gap-5 gap-2 items-center">
                            <h2 onClick={() => setProductTextDetail('Description')} className={`font-bazaar cursor-pointer py-2 ${productTextDetail === 'Description' ? ' border-b-2 border-[#1E7773]' : 'text-[#55555F]'} md:text-xl text-xs`}>Product Description </h2>
                            <h2 onClick={() => setProductTextDetail('Additional information')} className={`font-bazaar cursor-pointer py-2 ${productTextDetail === 'Additional information' ? ' border-b-2 border-[#1E7773]' : 'text-[#55555F]'} md:text-xl text-xs`}>Additional information</h2>
                            <h2 onClick={() => setProductTextDetail('Watch Product Video')} className={`font-bazaar cursor-pointer py-2 ${productTextDetail === 'Watch Product Video' ? ' border-b-2 border-[#1E7773]' : 'text-[#55555F]'} md:text-xl text-xs`}>Watch Product Video</h2>
                        </div>
                    </div>
                    <div>
                        {productTextDetail === 'Description' && (
                            <div className="flex flex-col gap-2 ">
                                {productDetail.product?.description ? (
                                    // <p
                                    //     dangerouslySetInnerHTML={{
                                    //         __html: productDetail.product.description,
                                    //     }}
                                    //     className="text-md"
                                    // />
                                    <DecodeTextEditor
                                        body={productDetail.product.description}
                                    />
                                ) : (
                                    <p className="text-md">No Description Found</p>
                                )}
                            </div>
                        )}
                        {productTextDetail === 'Additional information' && (
                            <div className="flex flex-col gap-2">
                                {productDetail.product?.additional_information ? (
                                    // <p
                                    //     dangerouslySetInnerHTML={{
                                    //         __html: productDetail.product.additional_information,
                                    //     }}
                                    //     className="text-md"
                                    // />
                                    <DecodeTextEditor
                                        body={productDetail.product.additional_information}
                                    />
                                ) : (
                                    <p className="text-md">No Additional Information Found</p>
                                )}
                            </div>
                        )}

                        {productTextDetail === 'Watch Product Video' && (
                            <div className="flex flex-col gap-2">
                                {productDetail.product?.product_video_url ? (
                                    <iframe
                                        className="w-full h-96"
                                        src={`https://www.youtube.com/embed/${productDetail.product.product_video_url.split('v=')[1]}`}
                                        title="Product Video"
                                        frameBorder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowFullScreen
                                    />
                                ) : (
                                    <p>No Video Found</p>
                                )}

                            </div>
                        )}



                        {/* Add content for 'Additional information' and 'Watch Product Video' */}
                    </div>
                </section>
                {recomendedProducts.length === 0 ? (
                    <h2 className='text-center text-4xl py-10 font-bazaar'>No Related Product Found</h2>
                ) : (
                    <>

                        <RcmdProduct products={recomendedProducts} setIsCartModalOpen={setIsCartModalOpen} />
                    </>
                )}

                <div className="relative z-10">
                    {/* <Deals /> */}
                </div>
                <Review productId={productId} setProductReview={setProductReview} productReview={productReview} />

            </main>
            {/* Background Image */}
            <img
                data-aos="fade-left"
                className="absolute top-[44rem] right-0 md:w-28 w-16"
                src={`${Image_Url}plateRight.svg`}
                alt="Plate"
            />
            <img
                data-aos="fade-right"
                className="absolute top-[100rem] left-0 lg:w-16 w-8"
                src={`${Image_Url}leftCup.svg`}
                alt="Plate"
            />
            {/* <img
                // data-aos="fade-left"
                className="absolute z-0 top-[100rem] right-0 w-full h-screen"
                src={`${Image_Url}ShopAssets/bgGradient.svg`}
                alt="bgGradient"
            /> */}
            {isCartModalOpen && (
                <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" onClick={() => setIsCartModalOpen(false)}>
                    <div className="fixed md:top-32 md:right-4 bg-white shadow-lg p-4 rounded-lg z-50 w-[300px] transition-transform duration-500">
                        <div className='flex justify-between  text-black'>
                            <h4 className="text-md font-bold">Added to Cart</h4>
                            <FiX size={24} onClick={() => setIsCartModalOpen(false)} />
                        </div>
                        <CartModal />
                        <div className="flex flex-row gap-2 mt-2">
                            <Link to='/shop/' className='p-1 flex justify-center items-center pt-2 border text-[#1E7773] border-[#1E7773] w-full lg:text-[15px] font-bazaar text-xs rounded-md'>
                                CONTINUE
                            </Link>
                            <Link to='/cart/' className='p-1 flex justify-center items-center pt-2 bg-[#1E7773] w-full lg:text-[15px] font-bazaar text-xs rounded-md'>
                                CART
                            </Link>
                        </div>
                    </div>
                </div>
            )}
        </div >
    );
}

export default ShopDetails;
