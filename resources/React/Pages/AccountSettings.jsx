import React, { useState } from 'react';
import { FaPen } from 'react-icons/fa'; // Use react-icons for the edit pen icon
import { Profile } from './Profile';
import { Security } from './Security';
import { OrderTrack } from './OrderTrack';
import { OrderHistory } from './OrderHistory';
import { RiFilter3Line } from 'react-icons/ri';
import axios from '../Utils/axios';
import { useNavigate } from 'react-router-dom';
import { removeAccessToken, removeUserData } from '../Utils/storage';
import { MdOutlineCancel } from 'react-icons/md';

const AccountSettings = () => {
  const [activePage, setActivePage] = useState('info');
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);
  const navigate = useNavigate(); // Initialize navigate

  const handlePageChange = (page) => {
    setActivePage(page);
    // setMobileMenu(false); // Close mobile menu after selecting a page
  };

  const handleLogout = async () => {
    try {
      // Send the logout request to the server
      const response = await axios.protected.get("logout");

      // Check if the logout request was successful
      if (response.status === 200) {
        console.log("Logout successful");

        // Remove access token and user data from local storage
        removeAccessToken();
        removeUserData();
        localStorage.removeItem('toastShown');
        navigate("/login/"); // Redirect to the home page
        window.location.reload(); // or navigate to login: navigate("/login");
      } else {
        console.error("Logout failed with status:", response.status);
        console.error("Response data:", response.data);
      }
    } catch (error) {
      console.error("Error during logout:", error);
    }
  };

  const toggleSidebar = () => {
    setIsSidebarOpen(!isSidebarOpen);
  };

  return (

    <div className='flex flex-col'>
      <div className="lg:hidden p-4">
        <button
          onClick={() => setIsSidebarOpen(true)}
          className="text-white text-xl flex justify-center items-center bg-[#1E7773] p-2 mt-24 rounded-full"
        >
          <RiFilter3Line />
        </button>
      </div>

      <div className="flex justify-center md:my-40 ">

        {/*desktop Sidebar */}
        <aside className="hidden md:block w-1/5 h-fit mt-8 pr-6 py10 border-r border-white ">
          <h2 className="text-2xl text-white font-bold mb-10">Accounts Settings</h2>
          <ul className="space-y-4">
            <li className={`text-[#1E7773] cursor-pointer ${activePage === 'info' ? 'font-bold' : ''}`} onClick={() => handlePageChange('info')}>Personal Information</li>
            <li className={`text-[#1E7773] cursor-pointer ${activePage === 'security' ? 'font-bold' : ''}`} onClick={() => handlePageChange('security')}>Security Settings</li>
            <li className={`text-[#1E7773] cursor-pointer ${activePage === 'track' ? 'font-bold' : ''}`} onClick={() => handlePageChange('track')}>Track Order</li>
            <li className={`text-[#1E7773] cursor-pointer ${activePage === 'history' ? 'font-bold' : ''}`} onClick={() => handlePageChange('history')}>Order History</li>
            <li className={`text-[#1E7773] cursor-pointer`} onClick={() => handleLogout()}>Logout</li>
          </ul>
        </aside>

        {/*mobile Sidebar */}
        {/* hidden lg:flex absolute -top-[0.5rem] -left-[55px] z-10 mt-3 h-screen overflow-y-auto flex flex-col w-60 transition-transform  duration-300 ease-in-out ${showMegaMenu ? "translate-x-0" : "-translate-x-full"} text-sm text-[#227c85] bg-white border border-gray-300 shadow-lg rounded` */}
        {/* md:hidden block w-1/5 h-fit mt-8 pr-6 py10 border-r border-white  */}
        <aside className={`lg:hidden flex absolute top-20 py-10 px-5 left-0 z-10 mt-3 h-screen overflow-y-auto flex flex-col w-80 transition-transform  duration-300 ease-in-out ${isSidebarOpen ? "translate-x-0" : "-translate-x-full"} text-sm text-[#227c85] bg-[#1E7773] shadow-lg rounded`}>

          <h2 className="text-2xl flex flex-row justify-between items-center text-white font-bold mb-10">Accounts Settings <MdOutlineCancel onClick={() => setIsSidebarOpen(false)} size={30} /> </h2>

          <ul className="space-y-4">
            <li className={`text-white cursor-pointer ${activePage === 'info' ? 'font-bold' : ''}`} onClick={() => { handlePageChange('info'); setIsSidebarOpen(false) }}>Personal Information</li>
            <li className={`text-white cursor-pointer ${activePage === 'security' ? 'font-bold' : ''}`} onClick={() => { handlePageChange('security'); setIsSidebarOpen(false) }}>Security Settings</li>
            <li className={`text-white cursor-pointer ${activePage === 'track' ? 'font-bold' : ''}`} onClick={() => { handlePageChange('track'); setIsSidebarOpen(false) }}>Track Order</li>
            <li className={`text-white cursor-pointer ${activePage === 'history' ? 'font-bold' : ''}`} onClick={() => { handlePageChange('history'); setIsSidebarOpen(false) }}>Order History</li>
            <li className={`text-white cursor-pointer`} onClick={() => { handleLogout(); setIsSidebarOpen(false) }}>Logout</li>
          </ul>
        </aside>

        {/* Main Content */}
        {/* <main className=""> */}
        {activePage === 'info' && <Profile />}
        {activePage === 'security' && <Security />}
        {activePage === 'track' && <OrderTrack />}
        {activePage === 'history' && <OrderHistory setActivePage={setActivePage}/>}
        {/* </main> */}


      </div>
    </div>
  );
};

export default AccountSettings;
