import React, { useEffect, useRef, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import axios from "../Utils/axios"; // Adjust path as needed
import { setAccessToken, setUserData } from "../Utils/storage"; // Adjust path as needed
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { FaEye, FaEyeSlash, FaFacebookF, FaGoogle } from "react-icons/fa"; // Import eye icons
import AOS from "aos";
import "aos/dist/aos.css";
import { Image_Url } from "../const";
import { GoogleLogin } from "@react-oauth/google";
import { LoginSocialFacebook } from "reactjs-social-login";
import { FacebookLoginButton } from "react-social-login-buttons";
import { useGoogleLogin } from "@react-oauth/google";
import { useUser } from "../Context/UserContext";
import { useWishlist } from "../Context/WishlistContext";

function Login() {
    const [forgetPassword, setForgetPassword] = useState(true);
    const [sendOtp, setSendOtp] = useState(true);
    const [changePass, setChangePass] = useState(true);
    const [otpInput, setOtpInput] = useState(Array(6).fill("")); // Updated for 6-digit OTP
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState(""); // State for confirm password
    const [showPassword, setShowPassword] = useState(false); // State to toggle password visibility
    const [showResetPassword, setShowResetPassword] = useState(false); // State to toggle password visibility for reset
    const [otpCode, setOtpCode] = useState(""); // State for OTP code
    const [error, setError] = useState(""); // State for OTP code
    const [loading, setLoading] = useState(false); // State for OTP code
    const { setUser } = useUser();
    const { addToWishlist } = useWishlist();
    const otpRefs = useRef([]);

    const navigate = useNavigate();

    useEffect(() => {
        AOS.init({ duration: "500", delay: "0" });
    }, []);

    // const handleOtpChange = (value, index) => {
    //     let otpArray = [...otpInput];
    //     otpArray[index] = value;
    //     setOtpInput(otpArray);

    //     // Focus next input field if value is entered
    //     if (value && index < otpInput.length - 1) {
    //         document.querySelectorAll('input')[index + 1].focus();
    //     }
    // };

    const isForgetPass = () => {
        setForgetPassword(!forgetPassword);
        setSendOtp(true); // Reset OTP state when showing forgot password
    };

    const isChangePass = () => {
        setChangePass(!changePass);
    };

    const handleLogin = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            const response = await axios.public.post("login", {
                email,
                password,
            });

            // Check for HTTP status code 200 (success)
            if (response.status === 200) {
                const { token, user } = response.data.data;
                setAccessToken(token);
                setUser(user);
                await setUserData({
                    user_id: user.id,
                    name: user.name,
                    email: user.email,
                    address: user.address,
                    phone: user.phone,
                    photo: user.photo,
                });

                // Fetch wishlist count
                const wishlistCountResponse = await axios.protected.get(
                    "user/wishlist/count"
                );
                const wishlistCount = wishlistCountResponse.data.count; // Accessing count from API response
                for (let index = 0; index < wishlistCount; index++) {
                    addToWishlist();
                }

                // Navigate after a delay to allow toast to appear
                setTimeout(() => {
                    navigate("/"); // Navigate to home or dashboard
                }, 1000); // Wait for 2 seconds before redirecting

                console.log(response.data);
            }
        } catch (err) {
            // Show error message on failure
            toast.error(err.response?.data?.message || "Login failed");
        } finally {
            setLoading(false);
        }
    };

    const responseFacebook = (response) => {
        console.log("Facebook response:", response);
        // Handle response here (e.g., save user info, authenticate, etc.)
    };

    // Function to handle OTP sending
    const isOtpSend = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.public.post("user/forgetPassword", {
                email: email,
            });
            if (response.data.status === "success") {
                toast.success("OTP sent to your email!");
                setSendOtp(false);
                setChangePass(true);
            }
        } catch (err) {
            toast.error(err.response?.data?.message || "Failed to send OTP");
        }
    };

    // Function to handle OTP input
    const handleOtpChange = (value, index) => {
        const updatedOtp = [...otpInput];
        updatedOtp[index] = value;
        setOtpInput(updatedOtp);
        console.log(otpInput);
        // Move to the next input if the current value is not empty
        if (value && index < otpRefs.current.length - 1) {
            otpRefs.current[index + 1].focus();
        }

    };

    // Function to reset password
    const resetPassword = async (e) => {
        e.preventDefault();
        if (password !== confirmPassword) {
            toast.error("Passwords do not match!");
            return;
        }
        try {
            const payload = {
                email,
                password,
                password_confirmation: confirmPassword,
                otp: otpInput.join(""), // Join OTP digits to form a complete code
                // option_type: 2,
            };
            const response = await axios.public.post("user/resetPassword", payload);
            if (response.data.status === "success") {
                toast.success("Password successfully reset!");
                // Reset states after success
                setForgetPassword(true);
                setSendOtp(true);
                setChangePass(false);
                setOtpInput(Array(6).fill(''));
                setEmail("");
                setPassword("");
                setConfirmPassword("");
                navigate("/login/"); // Redirect to login page
            }
        } catch (err) {
            toast.error(err.response?.data?.message || "Failed to reset password");
        }
    };

    // Function to resend OTP
    const resendOtp = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.public.post("auth/forget_password", {
                email: email,
                // option_type: 2,
            });
            if (response.data.status === "success") {
                toast.success("OTP resent to your email!");
            }
        } catch (err) {
            toast.error(err.response?.data?.message || "Failed to resend OTP");
        }
    };

    // Function to handle save password (optional)
    const savePass = () => {
        navigate("/login/"); // Navigate to login after saving the password
    };

    const handleGoogleLogin = async (response) => {
        try {
            const token = response.credential;
            console.log(response);

            console.log(token);

            // Send this token to your backend
            const res = await fetch(
                "http://localhost/ecommerce-inventory/api/auth/google/callback",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ id_token: token }),
                }
            );

            if (!res.ok) {
                throw new Error("Failed to authenticate with Google");
            }

            const data = await res.json();
            const { access_token, user } = data.data; // Correctly accessing data from the parsed response

            setAccessToken(access_token);
            setUser(user);

            await setUserData({
                name: user.name,
                email: user.email,
                id: user.id,
                mobile_no: user.mobile_no,
                role: user.role,
                profile_picture: user.profile_picture,
            });

            // toast.success('Login successful!');
            setTimeout(() => {
                navigate("/"); // Navigate to home or dashboard
            }, 1000);
        } catch (error) {
            console.error("Error:", error);
            // Handle error, perhaps show a toast notification
            toast.error("Email is already register as Contributor");
        }
    };

    return (
        <>
            <ToastContainer autoClose={500} />
            <div className="flex items-center md:flex-row flex-col-reverse justify-center text-white h-screen container mx-auto py-[470px] md:py-[430px] lg:px-40 px-8">
                {forgetPassword && (
                    <div
                        data-aos="fade-left"
                        className=" h-[500px] md:w-3/5 w-full flex md:py-24 py-8 flex-col items-center justify-center border-4 border-[#1E7773] md:rounded-l-3xl rounded-0"
                    >
                        <h4 className="text-xl md:text-4xl mb4 font-bazaar">
                            Login
                        </h4>
                        <div className="flex items-center mt-2 mb-2">
                            <hr className="flex-grow w-12 border-t border-gray-400"></hr>
                            <span className="mx-2 text-white text-lg font-bold">
                                OR
                            </span>
                            <hr className="flex-grow w-12 border-t border-gray-400"></hr>
                        </div>
                        <section className="flex flex-col gap-3 justify-center items-center">
                            {/* google auth */}
                            <div className="relative overflow-hidden">
                                <div className="opacity-0 inset-0 absolute cursor-pointer">
                                    <GoogleLogin
                                        onSuccess={handleGoogleLogin}
                                    />
                                </div>
                                <button className="flex justify-center gap-2 items-center rounded-full p-2 border-2 text-[#1E7773] border-[#1E7773]">
                                    {/* <img src="https://img.icons8.com/?size=100&id=17949&format=png&color=000000" alt="Google Icon" className="w-5 h-5 mr-2" /> */}
                                    <FaGoogle size={20} />

                                    <span className="textblack text-md text-white fontbazaar ">
                                        Login with Google
                                    </span>
                                </button>
                            </div>
                           {/* Mani Have Code  */}
                        </section>
                        <form
                            className="md:w-4/5 w-full flex justify-center flex-col items-center text-white py-3 gap-3"
                            onSubmit={handleLogin}
                        >
                            <input
                                className="w-4/5 p-2 px-4 rounded-lg bg-transparent border-2 border-[#55555f]"
                                type="email"
                                name="email"
                                placeholder="Email"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                            />
                            <div className="relative w-4/5">
                                <input
                                    className="w-full p-2 px-4 rounded-lg bg-transparent border-2 border-[#55555f]"
                                    type={showPassword ? "text" : "password"}
                                    name="password"
                                    placeholder="Password"
                                    value={password}
                                    onChange={(e) =>
                                        setPassword(e.target.value)
                                    }
                                />
                                <button
                                    type="button"
                                    className="absolute inset-y-0 right-0 flex items-center px-3"
                                    onClick={() =>
                                        setShowPassword(!showPassword)
                                    }
                                >
                                    {showPassword ? <FaEyeSlash /> : <FaEye />}
                                </button>
                            </div>
                            <button
                                onClick={isForgetPass}
                                className="text-white text-lg font-light"
                            >
                                Forgot your password?
                            </button>
                            {error && (
                                <p className="text-red-500 text-center text-sm">
                                    {error}
                                </p>
                            )}
                            <button
                                type="submit"
                                className="mt-6 rounded-lg bg-[#1E7773] py-2 px-10 pt-3 w40 font-bazaar text-lg text-white duration-300"
                                disabled={loading}
                            // onClick={handleLogin}
                            >
                                LOGIN
                            </button>
                            {/* <div className="flex items-center mt-2 mb-2">
                            <hr className="flex-grow w-24 border-t border-gray-400"></hr>
                            <span className="mx-2 text-white text-lg font-bold">OR</span>
                            <hr className="flex-grow w-24 border-t border-gray-400"></hr>
                        </div>
                        <GoogleLogin onSuccess={handleGoogleLogin} /> */}
                        </form>
                        {/* bgimages */}
                        <img
                            // data-aos="fade-down"
                            className="absolute z-0 top-10 right-0 w-16 hscreen"
                            src={`${Image_Url}basket.svg`}
                            alt="bgGradient"
                        />
                        <img
                            // data-aos="fade-down"
                            className="absolute z-0 top-30 left-0 w-12 hscreen"
                            src={`${Image_Url}plate.svg`}
                            alt="bgGradient"
                        />
                        <img
                            // data-aos="fade-down"
                            className="absolute z-0 bottom-0 right-20 w-20 hscreen"
                            src={`${Image_Url}FooterAssets/footerCenterImg.svg`}
                            alt="bgGradient"
                        />
                    </div>
                )}
                {!forgetPassword && sendOtp && (
                    <div
                        data-aos="fade-left"
                        className="h-[500px] md:w-3/5 w-full flex md:py-24 py-8 flex-col items-center justify-center border-4 border-[#1E7773] md:rounded-l-3xl rounded-0"
                    >
                        <h4 className="text-xl md:text-4xl py-10 font-bazaar">
                            Forget Password
                        </h4>
                        <form
                            onSubmit={isOtpSend}
                            className="md:w-4/5 w-full flex justify-center flex-col items-center text-white py-3 gap-3"
                        >
                            <input
                                className="w-4/5 p-2 px-4 rounded-lg bg-transparent border-2 border-[#55555f]"
                                type="email"
                                name="email"
                                placeholder="Email"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                            />
                            <button
                                type="submit"
                                className="mt-6 rounded-lg bg-[#1E7773] py-2 px-10 pt-3 w40 font-bazaar text-lg text-white duration-300"
                            >
                                SEND OTP
                            </button>
                        </form>
                        {/* bgimages */}
                        <img
                            // data-aos="fade-down"
                            className="absolute z-0 top-10 right-0 w-16 hscreen"
                            src={`${Image_Url}basket.svg`}
                            alt="bgGradient"
                        />
                        <img
                            // data-aos="fade-down"
                            className="absolute z-0 top-30 left-0 w-12 hscreen"
                            src={`${Image_Url}plate.svg`}
                            alt="bgGradient"
                        />
                        <img
                            // data-aos="fade-down"
                            className="absolute z-0 bottom-0 right-20 w-20 hscreen"
                            src={`${Image_Url}FooterAssets/footerCenterImg.svg`}
                            alt="bgGradient"
                        />
                    </div>
                )}
                {!sendOtp && changePass && (
                    <div
                        data-aos="fade-left"
                        className="h-[500px] md:w-1/2 w-full flex md:py-24 py-8 flex-col items-center justify-center border-4 border-[#1E7773] md:rounded-l-3xl rounded-0"
                    >
                        <div className="mx-auto flex w-full max-w-md flex-col space-y-16">
                            <div className="flex flex-col items-center justify-center text-center space-y-2">
                                <div className="font-light font-bazaar text-xl md:text-3xl">
                                    <p>Email Verification</p>
                                </div>
                                <div className="flex flex-row text-sm font-medium text-gray-400">
                                    <p>We have sent a code to your email {email}</p>
                                </div>
                            </div>
                            <div>
                                <form onSubmit={resetPassword} className="w-full flex flex-col items-center text-black py-3 gap-3">
                                    <div className="flex flex-row items-center justify-between mx-auto w-full max-w-xs">
                                        {otpInput.map((value, index) => (
                                            <div className="w-16 h-16" key={index}>
                                                <input
                                                    className="w-full h-full flex flex-col text-black items-center justify-center text-center px-5 outline-none rounded-xl border border-gray-200 text-2xl bg-white focus:bg-gray-50 focus:ring-1 ring-blue-700"
                                                    type="text"
                                                    maxLength="1"
                                                    ref={(e) => (otpRefs.current[index] = e)}
                                                    value={value}
                                                    onChange={(e) => handleOtpChange(e.target.value, index)}
                                                />
                                            </div>
                                        ))}
                                    </div>
                                    <input
                                        className="w-4/5 p-2 px-4 rounded-lg bg-transparent border-2 border-[#55555f]"
                                        type="password"
                                        name="password"
                                        placeholder="Password"
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                    />
                                    <input
                                        className="w-4/5 p-2 px-4 rounded-lg bg-transparent border-2 border-[#55555f]"
                                        type="password"
                                        name="confirmPassword"
                                        placeholder="Confirm Password"
                                        value={confirmPassword}
                                        onChange={(e) => setConfirmPassword(e.target.value)}
                                    />
                                    <button
                                        type="submit"
                                        className="mt-6 rounded-full bg-[#1E7773] p-3 w-1/2 font-bold hover:w-4/5 text-white duration-300"
                                    >
                                        Verify Account
                                    </button>
                                    <div className="flex flex-row items-center justify-center text-center text-sm font-medium space-x-1 text-gray-500">
                                        <p>Didn't receive code?</p>
                                        <a
                                            className="flex flex-row items-center text-blue-600"
                                            href="#"
                                            onClick={resendOtp}
                                        >
                                            Resend
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                )}
                {/* {!changePass && (
                    <div
                        data-aos="fade-left"
                        className="h-[500px] md:w-1/2 w-full flex md:py-24 py-8 flex-col items-center justify-center border-4 border-[#1E7773] md:rounded-l-3xl rounded-0"
                    >
                        <h4 className="text-xl md:text-4xl py-10 font-bazaar">Change Password</h4>
                        <form className="w-full flex justify-center flex-col items-center text-black py-3 gap-3">
                            <input
                                className="w-4/5 p-2 px-4 rounded-lg bg-transparent border-2 border-[#55555f]"
                                type="password"
                                name="password"
                                placeholder="Password"
                            />
                            <input
                                className="w-4/5 p-2 px-4 rounded-lg bg-transparent border-2 border-[#55555f]"
                                type="password"
                                name="confirmPassword"
                                placeholder="Confirm Password"
                            />
                            <button
                                onClick={savePass}
                                type="submit"
                                className="mt-6 rounded-full bg-[#1E7773] p-3 w-1/2 font-bold hover:w-4/5 text-white duration-300"
                            >
                                Save Password
                            </button>
                        </form>
                    </div>
                )} */}
                <div
                    data-aos="fade-right"
                    className="flex flex-col items-center justify-center md:py-24 py-8 bg-[#1E7773] h-[500px] md:rounded-r-3xl rounded-0 md:w-2/5 w-full"
                >
                    <h4 className="text-xl md:text-4xl text-center font-bazaar">
                        Hello Friends!
                    </h4>
                    <h4 className=" text-center pt-5 w-80">
                        Enter your personal details and start your journey with
                        us
                    </h4>
                    <Link
                        to="/register/"
                        className="mt-6 rounded-lg border-2 border-[#fff] py-2 px-10 pt-3 w40 font-bazaar text-lg text-white duration-300"
                    >
                        REGISTER
                    </Link>
                    {/* <Link to='/contributor-register' className='md:mb-16 mt-4 rounded-full flex justify-center p-2 px-4 w-58 bg-white font-bold hover:px-10 text-[#272887] duration-500'>REGISTER AS CONTRIBUTOR</Link> */}
                </div>
            </div>
        </>
    );
}

export default Login;
