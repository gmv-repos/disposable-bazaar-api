import React, { useEffect } from "react";
import "../css/app.css";
import Header from "./components/Header/Header";
import {
    Route,
    BrowserRouter as Router,
    Routes,
    useLocation,
    Navigate,
} from "react-router-dom";
import Home from "./Pages/Home";
// import Signup from './Pages/Signup';

import Footer from "./components/Footer/Footer";
import Shop from "./Pages/Shop";
import Customization from "./Pages/Customization";
import AboutUs from "./Pages/AboutUs";
import Review from "./Pages/ContactUs";
import Blog from "./Pages/Blog";
import ContactUs from "./Pages/ContactUs";
// import { BlogDetail } from './Pages/BlogDetail';
import InquiryForm from "./Pages/InquiryForm";
import ShopDetails from "./Pages/ShopDetails";
import Reviews from "./Pages/Reviews";
import Cart from "./components/cart/Cart";
import Checkout from "./Pages/Checkout";
import Register from "./Pages/Register";
import Login from "./Pages/Login";
import CustomDetails from "./Pages/CustomDetails";
import Wishlist from "./Pages/Wishlist";
import AccountSettings from "./Pages/AccountSettings";
import PrivacyPolicy from "./Pages/PrivacyPolicy";
import TermsAndConditions from "./Pages/TermsAndConditions";
import ReturnPolicy from "./Pages/ReturnPolicy";
import ErrorPage from "./Pages/ErrorPage";
import { useUser } from "./Context/UserContext";
import InvoicePopup from "./components/InvoicePopup";
import { BlogDetail } from "./Pages/BlogDetail";
import CategoryDetail from "./Pages/CategoryDetail";
import BundleShop from "./Pages/BundleShop";
import BundleDetail from "./Pages/BundleDetail";
import CustomizationCategory from "./Pages/CustomizationCategory";

// text -> text-[#9F9F9F]
// Green -> [#1E7773]

function Main() {
    const { user } = useUser();
    function ScrollToTop() {
        const { pathname } = useLocation();
        useEffect(() => {
            window.scrollTo(0, 0);
        }, [pathname]);
        return null;
    }
    return (
        <div className="bg-[#20202c] overflow-hidden ">
            <ScrollToTop />
            <Header />
            <Routes>
                <Route path="/" element={<Home />} />
                {/* <Route path='/signup' element={<Signup />} /> */}
                {/* <Route path='/shop' element={<Shop />} /> */}
                <Route path="/shop/" element={<Shop />} />
                {/* Dynamic category filter route for Shop page */}
                <Route path="/shop?q=:category" element={<Shop />} />
                <Route path="/customization/" element={<Customization />} />
                <Route path="/bundles/" element={<BundleShop />} />
                <Route path="/bundle/:id" element={<BundleDetail />} />
                <Route path="/about-us/" element={<AboutUs />} />
                <Route path="/contact-us/" element={<ContactUs />} />
                <Route path="/reviews/" element={<Reviews />} />
                <Route path="/blog/" element={<Blog />} />
                <Route path="/:id" element={<BlogDetail />} />
                <Route path="/inquiry/" element={<InquiryForm />} />
                <Route path="/product/:id" element={<ShopDetails />} />
                <Route
                    path="/product-category/:category"
                    element={<CategoryDetail />}
                />
                <Route
                    path="/product-category/:mainCategory/:subCategory"
                    element={<CategoryDetail />}
                />
                  <Route
                    path="/customization-category/:mainCategory/:subCategory"
                    element={<CustomizationCategory />}
                />
                
                <Route path="/customization/:id" element={<CustomDetails />} />
                <Route path="/cart/" element={<Cart />} />
                <Route path="/checkout/" element={<Checkout />} />
                <Route path="/register/" element={<Register />} />
                <Route path="/login/" element={<Login />} />
                <Route path="checkout/thankyou" element={<InvoicePopup />} />

                <Route path="/privacy-policy/" element={<PrivacyPolicy />} />
                <Route
                    path="/terms-conditions/"
                    element={<TermsAndConditions />}
                />
                <Route path="/return-policy/" element={<ReturnPolicy />} />

                <Route path="*" element={<ErrorPage />} />

                {user ? (
                    <>
                        <Route path="/wishlist" element={<Wishlist />} />
                        <Route path="/profile" element={<AccountSettings />} />
                        {/* Other authenticated routes can go here */}
                    </>
                ) : (
                    // Redirect to login if no user is logged in
                    <Route path="*" element={<Navigate to="/login" />} />
                )}
            </Routes>
            <Footer />
        </div>
    );
}
export default Main;
