import React, { useEffect, useState } from 'react';
import './Shop.css';
import { FaCheck } from 'react-icons/fa';
import { CiSearch } from 'react-icons/ci';
import axios from '../../Utils/axios';
import { useParams } from 'react-router-dom';
import { FiCornerDownRight } from 'react-icons/fi';

const PriceRange = ({ onFilter, isCategoryShown }) => {
    const min = 0;
    const max = 10000;
    const [priceFrom, setPriceFrom] = useState(min);
    const [priceTo, setPriceTo] = useState(max);
    const [selected, setSelected] = useState(1); // A to Z selected by default
    const [searchTerm, setSearchTerm] = useState('');
    const [category_Id, setCategory_Id] = useState(); // Store selected category ID
    const [categories, setCategories] = useState([]);
    const [filteredCategories, setFilteredCategories] = useState([]);
    const { id } = useParams()

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.public.get('product/category');
                const categoryData = response.data.data;
                setCategories(categoryData);
                setFilteredCategories(categoryData);
            } catch (error) {
                console.log(error);
            }
        };
        fetchData();
    }, []);

    // Handle the min price change
    const handleMinChange = (e) => {
        const value = Math.min(Number(e.target.value), priceTo - 1); // Prevent overlap
        setPriceFrom(value);
        onFilter({ price_from: value, price_to: priceTo, selected, searchTerm, category_Id });
    };

    // Handle the max price change
    const handleMaxChange = (e) => {
        const value = Math.max(Number(e.target.value), priceFrom + 1); // Prevent overlap
        setPriceTo(value);
        onFilter({ price_from: priceFrom, price_to: value, selected, searchTerm, category_Id });
    };

    // Handle sorting checkbox change
    const handleChange = (value) => {
        setSelected(value);
        onFilter({ price_from: priceFrom, price_to: priceTo, selected: value, searchTerm, category_Id });
    };

    const handleSearch = (e) => {
        const value = e.target.value;
        setSearchTerm(value);

        // Filter categories based on the search term
        const filtered = categories.filter((category) =>
            category.name.toLowerCase().includes(value.toLowerCase())
        );
        setFilteredCategories(filtered);
        onFilter({ price_from: priceFrom, price_to: priceTo, selected, searchTerm, category_Id });
    };

    const handleFilterCategory = (cate) => {
        setCategory_Id(cate.id);
        onFilter({ price_from: priceFrom, price_to: priceTo, selected, searchTerm, category_Id: cate.id });
    };

    return (
        <div className="py-4 w-full flex flex-col gap-2 text-white rounded-lg">
            <form>
                <div>
                    <h2 className='text-4xl'>Filter</h2>
                    <h3 className="text-[#9F9F9F] text-xl font-bazaar mt-4">Price</h3>

                    {/* Price Slider */}
                    <div className="flex justify-between py-3">
                        <h2>{priceFrom}</h2>
                        <h2>{priceTo}</h2>
                    </div>

                    <div className="relative mb-3 h-6">
                        {/* Min price input */}
                        <input
                            type="range"
                            min={min}
                            max={max}
                            value={priceFrom}
                            onChange={handleMinChange}
                            className="absolute w-full h-1.5 appearance-none slider-thumb-custom min-thumb"
                            style={{ zIndex: priceFrom > priceTo ? 5 : 3 }}
                        />

                        {/* Max price input */}
                        <input
                            type="range"
                            min={min}
                            max={max}
                            value={priceTo}
                            onChange={handleMaxChange}
                            className="absolute w-full h-1.5 appearance-none slider-thumb-custom max-thumb"
                            style={{ zIndex: priceFrom < priceTo ? 5 : 3 }}
                        />

                        {/* Track */}
                        <div className="absolute top-0 h-1.5 w-full bg-gray-300 rounded"></div>

                        {/* Filled range between min and max */}
                        <div
                            className="absolute top-0 h-1.5 bg-[#1E7773] rounded"
                            style={{
                                left: `${((priceFrom - min) / (max - min)) * 100}%`,
                                right: `${100 - ((priceTo - min) / (max - min)) * 100}%`,
                            }}
                        ></div>
                    </div>

                    {/* Display price values */}
                    <div className="w-full flex flex-wrap items-center text-xs xl:text-md gap-2 justify-start">
                        <div className='flex items-center justify-between gap-2'>
                            <span>From</span>
                            <div className="p-2 bg-[#1E7773] rounded-md">
                                Rs: {priceFrom}
                            </div>
                        </div>

                        <div className='flex items-center justify-between gap-2'>
                            <span>To</span>
                            <div className="p-2 bg-[#1E7773] rounded-md">
                                Rs: {priceTo}
                            </div>
                        </div>
                    </div>
                </div>
                <hr className='my-4 border border-[#9F9F9F]' />

                {/* Sort By Section */}
                <div>
                    <div className="flex flex-col gap-2">
                        <h3 className="text-[#9F9F9F] text-xl font-bazaar ">Sort By</h3>

                        {/* A to Z Checkbox */}
                        <label className="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                checked={selected === 1}
                                onChange={() => handleChange(1)}
                                className="hidden"
                            />
                            <span className={`custom-checkbox ${selected === 1 ? 'checked' : ''}`}>
                                {selected === 1 && <span className="checkmark text-white"><FaCheck size={10} /></span>}
                            </span>
                            <span>A to Z</span>
                        </label>

                        {/* Z to A Checkbox */}
                        <label className="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                checked={selected === 2}
                                onChange={() => handleChange(2)}
                                className="hidden"
                            />
                            <span className={`custom-checkbox ${selected === 2 ? 'checked' : ''}`}>
                                {selected === 2 && <span className="checkmark text-white"><FaCheck size={10} /></span>}
                            </span>
                            <span>Z to A</span>
                        </label>
                    </div>
                </div>

                {/* Product Categories Section */}
                {isCategoryShown && (
                    <>
                        <hr className='my-4 border border-[#9F9F9F]' />
                        <div>
                            <h3 className="text-[#9F9F9F] text-xl font-bazaar mt-4">Product Categories</h3>
                            <div className="relative">
                                <input
                                    type="text"
                                    value={searchTerm}
                                    onChange={handleSearch}
                                    className="w-full p-1.5 text-black rounded my-2"
                                    placeholder="Product name"
                                />
                                <button type="button">
                                    <CiSearch className="absolute text-xl text-[#606060] top-4 right-2" />
                                </button>
                            </div>
                            <div className="flex flex-col gap-1 py-3">
                                {filteredCategories.length > 0 ? (
                                    filteredCategories.map((cate) => (
                                        <>
                                            <h2
                                                className="hover:text-gray-400 cursor-pointer  py-1"
                                                onClick={() => handleFilterCategory(cate)}
                                                key={cate.id}
                                            >
                                                {cate.name}
                                            </h2>
                                            {cate.subCategories.length !== 0 ? (
                                                cate.subCategories.map((cat, index) => (
                                                    <h2
                                                        className="hover:text-gray-400 cursor-pointer flex flex-row gap-2 items-center py-1"
                                                        onClick={() => handleFilterCategory(cat)}
                                                    // key={cate.id}
                                                    >
                                                        <FiCornerDownRight />{cat.name}
                                                    </h2>
                                                ))) : null}

                                        </>
                                    ))
                                ) : (
                                    <h2>No result found</h2>
                                )}
                            </div>
                        </div>
                    </>
                )}
            </form>
        </div>
    );
};

export default PriceRange;
