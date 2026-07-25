import React, { useEffect, useState } from 'react'
import { Image_Url } from '../../const'
import { LuFacebook } from 'react-icons/lu'
import { FaFacebook, FaFacebookF, FaInstagram, FaLinkedinIn, FaTiktok, FaYoutube } from 'react-icons/fa'
import { FiYoutube } from 'react-icons/fi'
import { RiTiktokLine, RiTwitterXLine } from 'react-icons/ri'
import { MdEmail, MdLocationOn, MdPhone } from 'react-icons/md'
import { Link, useNavigate } from 'react-router-dom'
import axios from '../../Utils/axios'

function FooterBottom() {
    const [categories, setCategories] = useState([]);
    const navigate = useNavigate(); // For navigating to the category page

    const handleSearch = (category) => {
        return () => {
            // Redirect to the category page with search query
            // navigate(`/category/${categoryId}?q=`); // Adjusted to ensure it navigates correctly
            console.log('category',category);

            navigate(`product-category/${category.slug}`, { state: category.id })
        };
    };

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.public.get('product/category');
                setCategories(response.data.data);
            } catch (error) {
                console.log('Error', error);
            }
        };
        fetchData();
    }, []);

    return (
        <div className="pt-10 w-full justify-center items-center flex lg:flex-row flex-col  text-white">
            <div className="flex w-11/12 lg:flex-row flex-col justify-around items-center md:items-start gap-5 p-5">
                <div className="w-full lg:w-[25%] text-start flex flex-col flex-start items-center md:items-start gap-2 text-md">
                    <img className='cursor-pointer w-44 lg:w-64' src={`${Image_Url}/Logoo.png`} alt="" />
                    <p className='text-center md:text-start'>In the vast world of food packaging, the emphasis on sustainability and safety has never been higher. At Disposable Bazaar, we understand the modern consumer’s pulse, marrying the needs of both the environment...</p>
                </div>
                <img data-aos='fade-up-right' src={`${Image_Url}HomeAssets/PremiumAssets/shoper.svg`} className='absolute block md:hidden absolute w-24 -left-8 top-[32%] md:right-[15%] md:bottom-[25%]' alt="" />


                <ul className='block md:hidden flex flex-row justify-center gap-2'>
                     <li className='bg-white text-[#1E7773] p-2 rounded-full'><a href="https://www.facebook.com/DisposableBazar/"><LuFacebook className='text-md text-bolder ' /></a></li>
                            <li className='bg-white text-[#1E7773] p-2 rounded-full'><a href="https://www.instagram.com/disposablebazaar/"><FaInstagram className='text-md text-bolder' /></a></li>
                            <li className='bg-white text-[#1E7773] p-2 rounded-full'><a href="https://www.tiktok.com/@disposablebazaar"><RiTiktokLine className='text-md text-bolder' /></a></li>
                            <li className='bg-white text-[#1E7773] p-2 rounded-full'><a href="https://www.youtube.com/@disposablebazaar"><FiYoutube className='text-md text-bolder' /></a></li>
                            <li className='bg-white text-[#1E7773] p-2 rounded-full'> <a href="https://pk.linkedin.com/company/disposablebazaar">
                                <FaLinkedinIn className="text-md text-bolder" />
                            </a></li>
                </ul>

                <div className='flex lg:flex-row flex-col justify-evenly gap-14 lg:items-start items-center text-center md:text-start py10'>

                    <img data-aos='fade-up-right' src={`${Image_Url}HomeAssets/Deals/fork.svg`} className='absolute block md:hidden top-[58%] -right-8 w-24' style={{ transform: 'rotate(-90deg)' }} alt="" />

                    <ul className='flex flex-col gap-3  md:items-start items-center text-sm'>
                        <li className='text-2xl font-light py-2 font-bazaar'>Categories</li>
                        {categories.slice(0, 8).map((category) => (
                            <li key={category.id} onClick={handleSearch(category)} className="cursor-pointer">
                                {category.name}
                            </li>
                        ))}
                    </ul>
                    <ul className='flex flex-col gap-3 md:items-start items-center text-sm'>
                        <li className='text-2xl font-light py-2 font-bazaar'>Shop Info</li>
                        <li><Link to='/'>Home</Link></li>
                        <li><Link to='/about-us/'>About Us</Link></li>
                        <li><Link to='/contact-us/'>Contact Us</Link></li>
                        <li><Link to='/shop/'>Shop</Link></li>
                        <li><Link to='/blog/'>Blog</Link></li>
                    </ul>
                    <div className='flex flex-col gap-3 md:items-start items-center text-sm'>

                        <img data-aos='fade-up-right' src={`${Image_Url}FooterAssets/footerRightImg.svg`} className='absolute block md:hidden w-24 md:w-auto -left-4 ' alt="" />

                        <ul className='flex flex-col gap-3 md:items-start items-center text-sm'>
                            <li className='text-2xl font-light py-2 font-bazaar'>Policy</li>
                            <li><Link to='/privacy-policy/'>Privacy Policy</Link></li>
                            <li><Link to='/terms-conditions/'>Terms & Condition</Link></li>
                            <li><Link to='/return-policy/'>Return Policy</Link></li>
                        </ul>
                        <p className='hidden md:block text-2xl font-light pt-4 font-bazaar'> Follow Us</p>
                        <ul className='hidden md:flex flex-row gap-3'>
                            {/* <li className='p-2 rounded-lg duration-300'><a href="https://www.facebook.com/DisposableBazar/"><LuFacebook className='text-white text-md' /></a></li>
                            <li className='p-2 rounded-lg duration-300'><a href="https://www.instagram.com/disposablebazaar/"><FaInstagram className='text-white text-md' /></a></li>
                            <li className='p-2 rounded-lg duration-300'><a href="https://www.youtube.com/@disposablebazaar"><FiYoutube className='text-white text-md' /></a></li>
                            <li className='p-2 rounded-lg duration-300'><a href="https://www.tiktok.com/@disposablebazaar"><RiTiktokLine className='text-white text-md' /></a></li>
                            <li className='bg-[#1E7773] text-white p-2 rounded-full'><a href="https://pk.linkedin.com/company/disposablebazaar"><FaLinkedinIn className='text-white text-md' /></a></li>
                            <li className='p-2 rounded-lg duration-300'><RiTwitterXLine className='text-white text-md' /> </li> */}
                            <li className='bg-white text-[#1E7773] p-2 rounded-full'><a href="https://www.facebook.com/DisposableBazar/"><LuFacebook className='text-md text-bolder ' /></a></li>
                            <li className='bg-white text-[#1E7773] p-2 rounded-full'><a href="https://www.instagram.com/disposablebazaar/"><FaInstagram className='text-md text-bolder' /></a></li>
                            <li className='bg-white text-[#1E7773] p-2 rounded-full'><a href="https://www.tiktok.com/@disposablebazaar"><RiTiktokLine className='text-md text-bolder' /></a></li>
                            <li className='bg-white text-[#1E7773] p-2 rounded-full'><a href="https://www.youtube.com/@disposablebazaar"><FiYoutube className='text-md text-bolder' /></a></li>
                            <li className='bg-white text-[#1E7773] p-2 rounded-full'> <a href="https://pk.linkedin.com/company/disposablebazaar">
                                <FaLinkedinIn className="text-md text-bolder" />
                            </a></li>

                        </ul>
                    </div>
                    <ul className='flex flex-col gap-3 md:items-start items-center text-sm'>
                        <li className='text-2xl font-light py-2 font-bazaar'>Contact</li>
                        {/* <li className='flex flex-row gap-2 items-center'><MdLocationOn className='text-xl' /> Address: 1429 Netus Rd, NY 48247</li> */}
                        <li className='flex flex-row gap-2 items-center'><MdEmail className='text-xl' /> info@disposablebazaar.com</li>
                        <li className='flex flex-row gap-2 items-center'><MdPhone className='text-xl' />  0321-3850002</li>
                    </ul>
                </div>
            </div>
        </div>
    )
}

export default FooterBottom
