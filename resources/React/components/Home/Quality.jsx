import Aos from "aos";
import "aos/dist/aos.css";
import React, { useEffect, useState } from "react";
import { Image_Url } from "../../const";
import { Link, useNavigate } from "react-router-dom";

function Quality() {
    const [visibleItems, setVisibleItems] = useState("");

        const navigate = useNavigate(); // For navigating to the category page
    

    useEffect(() => {
        Aos.init({ duration: "2000", delay: "0" });
    }, []);
    const handleResize = () => {
        if (window.innerWidth < 768) {
            setVisibleItems("HomeAssets/Quality/qualitybanner.png"); // Show 2 items on mobile
        } else {
            setVisibleItems("HomeAssets/Quality/qualitybanner.png"); // Show 3 items on larger screens
        }
    };

    useEffect(() => {
        window.addEventListener("resize", handleResize);
        handleResize(); // Call it once on component mount

        return () => window.removeEventListener("resize", handleResize);
    }, []);
    return (
        <div className="pt-10 relative">
            <div
                className="flex items-center md:h-screen"
                style={{
                    backgroundImage: `url(${Image_Url}${visibleItems})`,
                    backgroundSize: "cover", // Cover the entire element
                    backgroundRepeat: "no-repeat", // Prevent repeating the image
                    backgroundPosition: "center", // Center the image in the element
                    // height: '100vh',                // Full viewport height
                    width: "100%",
                }}
            >
                <div
                    data-aos="fade-right"
                    className="flex flex-col text-white md:pl-20 pl-5 gap-3 md:py-60 pt-16 pb-8  md:w-1/2 w-[70%]"
                >
                    <h3 className="md:text-6xl text-4xl font-bazaar">
                        Sip Fresh, Stay Stylish!
                    </h3>
                    <p className=" md:text-xl text-sm font-semibold">
                        Discover high-quality juice cups crafted for freshness,
                        durability, and unbeatable value.
                    </p>
                    {/* <Link to="/product-category/dips-and-cups/juice-cup/"> */}
                      <button
  className="bg-white text-black p-2 font-bazaar rounded-md text-xs mt-2 w-28"
  onClick={() => {
    navigate('/product-category/dips-and-cups/juice-cup/', { state: 21 });
  }}
>
  BROWSE NOW
</button>


                    {/* </Link> */}
                </div>
                <img
                    data-aos="fade-up-right"
                    src={`${Image_Url}HomeAssets/Quality/cup.svg`}
                    className="absolute  hidden md:block top-20 left-20 w-32"
                    alt=""
                />
            </div>
        </div>
    );
}

export default Quality;
