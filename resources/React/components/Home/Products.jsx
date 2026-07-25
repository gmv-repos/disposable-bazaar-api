import { Swiper, SwiperSlide } from 'swiper/react';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/autoplay';
import 'swiper/css/pagination';
import { Autoplay } from 'swiper/modules';
import { Assets_Url, Image_Url } from '../../const';
import Aos from 'aos';
import 'aos/dist/aos.css';
import { useEffect, useState } from 'react';
import axios from '../../Utils/axios';
import { Link, useNavigate } from 'react-router-dom';

const Products = () => {
    const [product, setProduct] = useState([])
    const navigate = useNavigate();

    useEffect(() => {
        Aos.init({ duration: '2000', delay: '0' });
    }, []);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.public.get("product/category?sectionName=oneStopShop")
                setProduct(response.data.data)

            } catch (error) {
                console.log('Error', error);
            }
        }

        console.log("products", product);
        fetchData()
    }, [])

    const handleCategoryLink = (product) => {
        navigate(`product-category/${product.slug}`, { state: product.id })
    }

    return (
        <div className="w-full py-10 text-white">
            <div className="flex md:flex-row flex-col justify-around md:gap-10 gap-2 my-16 md:text-start text-center items-center">
                <h1 data-aos='fade-right' className='md:w-1/2 w-11/12 font-bazaar md:text-6xl text-4xl'>Our One-Stop Shop for Disposable Products</h1>
                <p data-aos='fade-left' className='md:w-1/3 w-11/12 md:text-md text-sm'>Discover our extensive range of high-quality disposable items designed for convenience and sustainability.</p>
            </div>
            <Swiper
                data-aos='fade-up'
                autoplay={{
                    delay: 2000,
                    disableOnInteraction: false,
                }}
                breakpoints={{
                    100: {
                        slidesPerView: 2,
                    },
                    400: {
                        slidesPerView: 2,
                    },
                    768: {
                        slidesPerView: 3,
                    },
                    992: {
                        slidesPerView: 4,
                    },
                    1100: {
                        slidesPerView: 5,
                    },
                    // width: 220,
                }}
                spaceBetween={10}
                modules={[Autoplay]}
                className="mySwiper"
            >

                {product.map((product, index) => (
                    <SwiperSlide key={index}>
                        <div
                            data-aos='fade-up'
                            className={`${index % 2 === 0 ? 'mb-10' : 'mt-10'} relative cursor-pointer flex md:ml5 md:mx-2 rounded-2xl flex-col items-center  overflow-hidden lg:h-96 sm:h-80 h-72`}
                            onClick={() => {
                                handleCategoryLink(product);
                            }}
                            style={{
                                background: `url('${Assets_Url}${product.image}')`,
                                backgroundSize: 'cover',
                                backgroundPosition: 'center',
                                width: '100%',
                                // height: '300px',
                            }}
                        >
                            <div className="absolute bottom-0 w-full p-4 bg-gradient-to-t from-black/70 to-transparent ">
                                <h2 className="md:text-3xl text-xl font-bazaar ">{product.name}</h2>
                                {/* <Link to={`/product-category/${product.slug}`}> */}
                                    <button className="mt-2 w-full mt-5 bg-teal-600 text-white py-3 md:text-lg text-xs font-bazaar px-4 rounded-lg"
                                    onClick={() => {
                                        handleCategoryLink(product);
                                    }}>
                                        EXPLORE PRODUCTS
                                    </button>
                                {/* </Link> */}
                            </div>
                        </div>
                    </SwiperSlide>
                ))}
            </Swiper>
        </div>
    );
};

export default Products;
