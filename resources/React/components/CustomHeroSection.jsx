import React, { useEffect } from "react";
import { Assets_Url, Image_Url } from "../const";
import Aos from "aos";
import "aos/dist/aos.css";
import { Link } from "react-router-dom";

function CustomHeroSection({
    heading,
    path,
    path2,
    title,
    bgImage,
    heroImage,
    custom
}) {
    useEffect(() => {
        Aos.init({ duration: "2000", delay: "0" });
    }, []);

    const backgroundImageUrl = heroImage
        ? `${Assets_Url}${heroImage}`
        : bgImage
          ? `${Image_Url}${bgImage}`
          : `${Assets_Url}CustomHeroAssets/CustomHeroBgImg.svg`;

    console.log("Background Image URL:", backgroundImageUrl);

    console.log("Category ?? heroImage", heroImage);
    console.log(
        "Category ?? url",
        `${Assets_Url}${heroImage || "CustomHeroAssets/CustomHeroBgImg.svg"}`,
    );

    return (
        <div
            className={`w-full 2xl:mt-[58px] flex bg-contain bg-cover  justify-start items-center text-black relative lg:bg-right bg-center h-[90vh] ${custom == `customization` ? `2xl:h-[1100px]` :`2xl:h-[790px]`} overflow-hidden`}
            style={{
                backgroundImage: `url('${backgroundImageUrl}')`,
                // backgroundPosition: "center",
        // backgroundSize: "contain",
        backgroundRepeat: "no-repeat",
                
            }}
            // style={{
            //  background: `url('${backgroundImageUrl}')`,
            //   backgroundSize: 'auto',
            //   backgroundRepeat: 'no-repeat',
            //   // backgroundRepeat: 'no-repeat',
            //   // backgroundPosition: 'center',
            //   // width: '100%',
            //   // height: '29rem',
            // }}
        >
            {/* Left text content */}
            <div className="md:w-[40%] md:pl-20 pl-10">
                <h1 className="md:text-6xl text-5xl font-bazaar text-white">
                    {heading}
                </h1>
                <p className="md:text-5xl text-4xl font-bazaar text-white">
                    {title}
                </p>

                {path2 ? (
                    <p className="text-lg text-white">
                        <Link to={"/"}>Home</Link> /{" "}
                        <Link to={"/shop"}>{path}</Link> / {path2}
                    </p>
                ) : (
                    <p className="text-lg text-white">
                        <Link to={"/"}>Home</Link> / {path}
                    </p>
                )}
            </div>

            {/* Decorative images */}
            {/* <img data-aos='fade-right' src={`${Image_Url}CustomHeroAssets/Cup.svg`} className='absolute -bottom-10 left-0 w-18' alt="" />
      <img data-aos='fade-down' src={`${Image_Url}CustomHeroAssets/glass.svg`} className='absolute top-6 md:right-36 right-0 w-18' alt="" />
      <img data-aos='fade-left' src={`${Image_Url}CustomHeroAssets/shopper.svg`} className='absolute md:bottom-16 bottom-8 right-0 w-18' alt="" />
      <img data-aos='fade-up' src={`${Image_Url}CustomHeroAssets/basket.svg`} className='absolute hidden md:block bottom-16 right-[30rem] w-18' alt="" /> */}
        </div>
    );
}

export default CustomHeroSection;
