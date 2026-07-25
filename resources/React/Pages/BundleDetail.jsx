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

function BundleDetail() {
    const [productDetail, setProductDetail] = useState([]);
    const [productImages, setProductImages] = useState([]);
    const [productId, setProductId] = useState(0);
    const [selectedImage, setSelectedImage] = useState(''); // State to track the selected image
    const [quantity, setQuantity] = useState(null);
    const [productTextDetail, setProductTextDetail] = useState('Description');
     const [subQuantity, setSubQuantity] = useState(1);
    const [isLoading, setIsLoading] = useState(false); // New state for loading
    const { addToCart } = useCart();
    const [isCartModalOpen, setIsCartModalOpen] = useState(false);
    const { user } = useUser();
    const navigate = useNavigate()
      const location = useLocation();
    const { id } = location.state || {};
    const dropdownRef = useRef(null);

    // Close dropdown if clicked outside
    // useEffect(() => {
    //     function handleClickOutside(event) {
    //         if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
    //             setBrandsOpen(false);
    //         }
    //     }

    //     document.addEventListener("mousedown", handleClickOutside);
    //     return () => {
    //         document.removeEventListener("mousedown", handleClickOutside);
    //     };
    // }, []);

    // Fetch productId from the URL using useParams
    // const { id } = useParams();

    const fetchDataById = async (id) => {

        setIsLoading(true);
        try {
            const response = await axios.public.get(`bundles/getByID/${id}`);
            const resData = response.data.data
            setProductDetail(resData);
            setSelectedImage(resData?.bundle_images[0].image || ''); // Set initial image
            setProductImages(resData?.bundle_images)
            // setRecomendedProducts(resData.recommended_products)
            // setBrands(resData.product.product_brands.filter(i => i.status === 1))
            // setSelectedBrands(resData.product.product_brands.filter(i => i.status === 1)[0]?.name)
            // setSelectedBrandId(resData.product.product_brands.filter(i => i.status === 1)[0]?.id)
            // setProductVariants(resData.product.product_variants)
            // setProductLids(resData.product.product_lid_options)
            setProductId(resData.id)
            // const seletedBrandId = resData.product.product_brands.filter(i => i.status === 1)[0]?.id
            // if (resData.product.product_variants.filter(i => i.brand_id === seletedBrandId)) {
            //     console.log(true);
            //     setSelectedProductVariants(resData.product.product_variants.filter(i => i.brand_id === seletedBrandId))
            // }
            // const firstVariant = resData.product.product_variants[0];
            // console.log(firstVariant);

            // if (firstVariant) {
            //     setQuantity(firstVariant.pack_size);
            //     setSelectedVariantId(firstVariant.id); // Set the default selected variant
            //     setSelectedVariant(firstVariant.pack_size)
            //     setSelectedVariantPrice(firstVariant.price_per_piece);
            // }
            // const price = quantity && selectedVariantPrice && (quantity * subQuantity * selectedVariantPrice)
            // console.log('Price', selectedVariantPrice);
            // console.log('P', quantity);


            setIsLoading(false);
        } catch (error) {
            console.log('Error fetching product details:', error);
        } finally {
setIsLoading(false);
        }
    };

    useEffect(() => {
        if (id) {
            fetchDataById(id); // Call the function with the ID
            // fetchReviewById(id)
        }
    }, [id]);

    const handleImageClick = (image) => {
        setSelectedImage(image); // Update the selected image when clicked
    };

        const handleSubmit = (e) => {
        e.preventDefault();
    };

    // Handle Add to Cart Logic
    const handleAddCart = (product) => {
        const product_id = product.id;
        const product_name = product?.name;
        const pack_size = null;
        const product_quantity = subQuantity;
        const total_pieces = subQuantity;
        const price_per_piece = Number(product?.discount_amount) > 0 ? (product?.payable_amount - product?.discount_amount) : product?.payable_amount;

          // Calculate base total
  const baseTotal = subQuantity * product?.payable_amount ;

  // Apply discount if available
  let finalTotal = baseTotal;
  const discountPercentage = parseFloat(product?.discount_amount);
  if (!isNaN(discountPercentage) && discountPercentage > 0) {
    finalTotal = baseTotal - discountPercentage;
  }
  const product_total = finalTotal.toFixed(2);
        // const product_total = ((quantity * subQuantity * selectedVariantPrice) + (quantity * subQuantity * selectedLidPrice)).toFixed();
        const product_img = selectedImage;
        const product_variants = null;
        // New
        const product_lids =  null;
        const lid =  null;
        const lid_Price =  0;
        const customizeDetail = null ;
        const product_color = null;
        const product_size = null;
        const product_options = null;
        const option_Price = 0;
        const logo = null;
        const bundle_status = true;

        // Add the product to the cart
        addToCart(product_id, product_name, product_quantity, pack_size, total_pieces, price_per_piece, product_img, product_total, product_variants, product_color, product_size, logo, product_options, product_lids, lid, lid_Price, customizeDetail, option_Price, bundle_status);
        setIsCartModalOpen(true);
        // Show success toast
        // toast.success(`${product.name} added to cart`);
    };

    const whatsappNumber = "1234567890"; // Replace with your WhatsApp number (in international format without + or 00)
    const productUrl = window.location.href; // Replace with the product's web URL
    const inquiryMessage = encodeURIComponent(
        `Hello! I am interested in the following product:\n\n${productDetail.product?.name}\n\n ${productUrl}`
    );

    if (isLoading) return <Loader />;
    return (
        <div className="relative py-32 px-10 text-white overflow-hidden">
            {/* <CustomDetailSeo
                title={productDetail?.seoMetadata?.meta_title}
                des={productDetail?.seoMetadata?.meta_description}
                focuskey={productDetail?.seoMetadata?.focus_keyword}
                canonicalUrl={productDetail?.seoMetadata?.canonical_url}
                schema={productDetail?.seoMetadata?.schema}
                og_title={productDetail?.product?.name}
                og_des={productDetail?.product?.description}
                og_img={productDetail?.product?.product_image[0]?.image}
            /> */}
            <ToastContainer autoClose={500} />
            {/* Breadcrumb and Title */}
            <div className="flex flex-col py-5">
                <p><Link to='/'>Home</Link> / <Link to='/shop/'>Bundle</Link> / {productDetail?.name || 'Product Name'}</p>
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
                        <h1 className='md:text-5xl text-3xl font-semibold'>
                            {productDetail?.name || 'Product Name'}
                        </h1>

                        <form
                        onSubmit={handleSubmit}
                         className='max-w-96  w-fit flex flex-col gap-5 '>
                            {/* Quantity Selection */}
                            {/* <div className="my-form border w-full border-[#1E7773] rounded-full flex items-stretch">
                                <p className="my-form-heading bg-[#1E7773] rounded-l-full h-full p-1 px-5 flex items-center">Pieces</p>
                                <div className="flex flex-wrap gap-4 justify-start p-1 px-2 items-center">
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
                            </div> */}
{/* Product Lids Selection*/}
                            {Array.isArray(productDetail?.bundle_items) && productDetail.bundle_items.length > 0 && (
                                <div className=" my-form border rounded-lg h32 w-6/7 md:w-96 border-[#1E7773]">
                                    <p className="bg-[#1E7773] rounded-t-lg py-0.5 px-5">Products</p>
                                    <div className="flex flex-wrap gap-4 justify-start p-3 items-center">
                                        {productDetail && productDetail?.bundle_items.length > 0 ? (
                                            productDetail?.bundle_items?.map((product) => (
                                                <div key={product.id} className="flex flex-row items-center justify-center pr-3 gap-2">
                                                       {product?.product?.name} , {product?.quantity } QTY, Price {product?.price} <br />
                                                </div>
                                            ))
                                        ) : (
                                            <p>No Products available.</p> // Message if no variants exist
                                        )}
                                    </div>
                                </div>
                            )}

                            <div className='flex flex-row gap-3'>
                                <div className="border border-[#1E7773] rounded-md flex flex-row justify-between items-center px-2 w-24 h-10">
                                    <button disabled={subQuantity === 1} onClick={() => setSubQuantity(subQuantity - 1)}>-</button>
                                    <p className=''>{subQuantity}</p>
                                    <button onClick={() => setSubQuantity(subQuantity + 1)}>+</button>
                                </div>
                                <button className='p-2 pt-3 bg-[#1E7773] w-full lg:text-[15px] font-bazaar text-xs rounded-md'
                                    onClick={() => handleAddCart(productDetail)}>
                                    ADD TO CART
                                </button>
                            </div>
                        </form>



                        {/* <p>₨ {quantity && subQuantity && (quantity * subQuantity * productDetail.product?.current_sale_price)} / Per Pieces : {productDetail.product?.current_sale_price}</p> */}
                       <div className=''>
                        <p className='text-sm'><span className='text-lg text-bolder'>₨ {productDetail.payable_amount ? Number(productDetail.payable_amount) * subQuantity : 'Nan' }
                             </span></p>
                           {productDetail?.discount_amount !== null && ( <p className='text-sm '>{Number(productDetail?.discount_amount)}% OFF</p>)}
                           </div>
                        <div className="flex flex-row md:gap-5 gap-2">
                            <button className='p-3 border flex flex-row justify-between items-center gap-2  border-[#1E7773] w32 lg:text-[15px]  font-bazaar text-xs rounded-md'
                                onClick={() => window.open(`https://wa.me/${whatsappNumber}?text=${inquiryMessage}`, '_blank')}>
                                <FaWhatsapp className='text-[#1E7773] text-2xl' /> <p className="pt-2">ORDER ON WHATSAPP</p>
                            </button>
                        </div>
                    </div>
                </section>

                {/* Product Description and Additional Information */}
                <section className='flex flex-col gap-8 md:py-20 py-5'>
                    <div className="flex flexrow w-][40%] borderb border[#1E7773] justify-center items-center">
                        <div className="flex flex-row justify-center md:gap-5 gap-2 items-center">
                            <h2 onClick={() => setProductTextDetail('Description')} className={`font-bazaar cursor-pointer py-2 ${productTextDetail === 'Description' ? ' border-b-2 border-[#1E7773]' : 'text-[#55555F]'} md:text-xl text-xs`}>Product Description </h2>
                         </div>
                    </div>
                    <div>
                        {productTextDetail === 'Description' && (
                            <div className="flex flex-col gap-2 ">
                                {productDetail?.description ? (
                                    // <p
                                    //     dangerouslySetInnerHTML={{
                                    //         __html: productDetail.product.description,
                                    //     }}
                                    //     className="text-md"
                                    // />
                                    <DecodeTextEditor
                                        body={productDetail?.description}
                                    />
                                ) : (
                                    <p className="text-md">No Description Found</p>
                                )}
                            </div>
                        )}

                    </div>
                </section>
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

export default BundleDetail;
