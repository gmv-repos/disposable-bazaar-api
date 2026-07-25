import React, { useEffect, useState } from 'react';
import { PiCaretDownThin } from 'react-icons/pi';
import axios from '../Utils/axios';
import { Image_Url } from '../const';
import Aos from 'aos';
import 'aos/dist/aos.css';
import { toast, ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css'; // Import the CSS for Toastify
import CustomSeo from '../components/CustomSeo';
import { Link } from 'react-router-dom';

function InquiryForm() {
    const [productCategory, setProductCategory] = useState([]);
    const [isDropdown, setIsDropdown] = useState(false);
    const [selectedProduct, setSelectedProduct] = useState('Choose one product');
    const [selectedProductId, setSelectedProductId] = useState();

    // Form fields
    const [name, setName] = useState('');
    const [companyNumber, setCompanyNumber] = useState('');
    const [contactNumber, setContactNumber] = useState('');
    const [location, setLocation] = useState('');
    const [email, setEmail] = useState('');
    const [file, setFile] = useState(null); // For file upload

    // Success/Error message state
    const [message, setMessage] = useState('');
    const [isError, setIsError] = useState(false);

    useEffect(() => {
        Aos.init({ duration: 2000, delay: 0 });
    }, []);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.public.get('search/product');
                setProductCategory(response.data.data);
            } catch (error) {
                console.log(error);
            }
        };
        fetchData();
    }, []);

    const handleSelectProduct = (product) => {
        setSelectedProduct(product.name);
        setSelectedProductId(product.id);
        setIsDropdown(false); // Close the dropdown after selection
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        // Create form data
        const formData = new FormData();
        formData.append('name', name);
        formData.append('company_number', companyNumber);
        formData.append('contact_no', contactNumber);
        formData.append('location', location);
        formData.append('email', email);
        formData.append('product_id', selectedProductId);
        if (file) {
            formData.append('logo_design', file);
        }

        try {
            const response = await axios.public.post('inquiry_add', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
            toast.success('Form submitted successfully!');
            // Clear the form fields
            setName('');
            setCompanyNumber('');
            setContactNumber('');
            setLocation('');
            setEmail('');
            setFile(null);
            setSelectedProduct('Choose one product');
            setSelectedProductId(null); // Set to null instead of undefined
            document.getElementById('logo').value = '';

            setIsError(false); // Mark as a success
            console.log('Form submitted successfully:', response.data);
        } catch (error) {
            toast.success('Error submitting form. Please try again.');
            setIsError(true); // Mark as an error
            console.log('Form submission error:', error);
        }
    };

    return (
        <div className="relative py-32 px-10 text-white overflow-hidden">
            <CustomSeo id={2} />
              <ToastContainer autoClose={500} />
            {/* Breadcrumb and Title */}
            <div className="flex flex-col py-5">
                <p><Link to='/'> Home </Link> / <Link to='/customization/'> Customization </Link> / Inquiry </p>
                <h1 className="py-10 font-bazaar md:text-6xl text-5xl">INQUIRY FORM</h1>
            </div>

            {/* Background Images */}
            <img
                data-aos="fade-left"
                className="absolute top-32 right-0 w-20 "
                src={`${Image_Url}basket.svg`}
                alt="Basket"
            />
            <img
                data-aos="fade-right"
                className="absolute top-[52rem] left-0 w-20 "
                src={`${Image_Url}plate.svg`}
                alt="Plate"
            />

            {/* Form */}
            <div className="flex justify-center items-center">
                <form className="w-full max-w-4xl relative" onSubmit={handleSubmit}>
                    {/* Success/Error Message */}
                    {message && (
                        <div className={`p-4 mb-4 rounded text-white`}>
                            {message}
                        </div>
                    )}

                    <div className="py-2 flex flex-col w-full">
                        <label htmlFor="name">Name</label>
                        <input
                            id="name"
                            className="p-2 rounded-md px-3 my-2 border border-white bg-transparent"
                            type="text"
                            placeholder="Enter your name"
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                        />
                    </div>

                    <div className="py-2 flex flex-col w-full">
                        <label htmlFor="company">Company Number</label>
                        <input
                            id="company"
                            className="p-2 rounded-md px-3 my-2 border border-white bg-transparent"
                            type="text"
                            placeholder="Enter your company number"
                            value={companyNumber}
                            onChange={(e) => setCompanyNumber(e.target.value)}
                        />
                    </div>

                    <div className="py-2 flex flex-col lg:w-1/3 md:w-1/2 w-full">
                        <label htmlFor="number">Contact Number</label>
                        <input
                            id="number"
                            className="p-2 rounded-md px-3 my-2 border border-white bg-transparent"
                            type="text"
                            placeholder="Enter your contact number"
                            value={contactNumber}
                            onChange={(e) => setContactNumber(e.target.value)}
                        />
                    </div>

                    <div className="py-2 flex flex-col w-full">
                        <label htmlFor="location">Location</label>
                        <input
                            id="location"
                            className="p-2 rounded-md px-3 my-2 border border-white bg-transparent"
                            type="text"
                            placeholder="Enter your location"
                            value={location}
                            onChange={(e) => setLocation(e.target.value)}
                        />
                    </div>

                    <div className="py-2 flex flex-col w-full">
                        <label htmlFor="email">Email address</label>
                        <input
                            id="email"
                            className="p-2 rounded-md px-3 my-2 border border-white bg-transparent"
                            type="email"
                            placeholder="Enter your email address"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                        />
                    </div>

                    {/* Dropdown */}
                    <div className="py-2 flex flex-col md:w-80 w-full">
                        <label htmlFor="dropdown">Please choose the product</label>
                        <div
                            onClick={() => setIsDropdown(!isDropdown)}
                            className="flex justify-between items-center p-2 rounded-md px-3 my-2 border border-white bg-transparent cursor-pointer"
                        >
                            <p>{selectedProduct}</p>
                            <PiCaretDownThin size={20} />
                        </div>

                        {isDropdown && (
                            <div className="absolute z-10 w-full md:w-1/2 rounded-lg right-0 md:bottom-32 bottom-20 overflow-y-auto h-96 bg-white border border-gray-200">
                                {productCategory.map((product, index) => (
                                    <h4
                                        key={index}
                                        className="text-black p-2 px-4 cursor-pointer hover:bg-gray-100"
                                        onClick={() => handleSelectProduct(product)}
                                    >
                                        {product.name}
                                    </h4>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* File Upload */}
                    <div className="py-10 flex flex-col w-full">
                        <label htmlFor="upload">Upload Your Artwork If Any (Logo/Designs)</label>
                        <div
                            id="upload"
                            className="flex flex-col my-2 relative w-full h-60 items-center justify-center border-2 border-dashed rounded-lg border-gray-300"
                        >
                            <button className="px-3 py-2 bg-teal-700 text-white rounded-md">
                                Select Files...
                            </button>
                            <p>or drag and drop files here</p>
                            <input
                                type="file"
                                id='logo'
                                name="thumbnail"
                                accept="image/*"
                                className="absolute inset-0 opacity-0 cursor-pointer"
                                onChange={(e) => setFile(e.target.files[0])}
                            />
                        </div>
                    </div>

                    <button type="submit" className="px-10 py-2 pt-3 my-5 bg-teal-700 text-xl font-bazaar rounded-md">
                        SUBMIT
                    </button>
                </form>
            </div>
        </div>
    );
}

export default InquiryForm;
