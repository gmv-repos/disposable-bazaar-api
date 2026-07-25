import ReactDOM from 'react-dom/client';
import Main from "./Main";
import { BrowserRouter } from 'react-router-dom';
import '../../assets/adminPanel/css/index.css'
import { UserProvider } from './Context/UserContext';
import { WishlistProvider } from './Context/WishlistContext';
import { CartProvider } from './Context/CartContext';
import { GoogleOAuthProvider } from '@react-oauth/google';
import { BundleProvider } from './Context/BundleContext';
import { ToastContainer } from 'react-toastify';
import FloatingButtons from './components/FloatingButtons';

// ✅ Force add trailing slash if missing
function ensureTrailingSlash() {
  const { pathname, search, hash } = window.location;
  if (!pathname.endsWith('/')) {
    const newUrl = `${pathname}/${search}${hash}`;
    window.history.replaceState({}, '', newUrl);
  }
}
ensureTrailingSlash();

ReactDOM.createRoot(document.getElementById('app')).render(

    //  basename='Disposable-Bazar'

    <BrowserRouter >
         <ToastContainer autoClose={500} />
         <FloatingButtons />
        <GoogleOAuthProvider clientId='341574951595-7kmbo799rdp05d4dcjgn0vfkpfsrrs44.apps.googleusercontent.com'>
            <UserProvider>
                <CartProvider>
                    <BundleProvider>
                    <WishlistProvider>
                        <Main />
                    </WishlistProvider>
                    </BundleProvider>
                </CartProvider>
            </UserProvider>
        </GoogleOAuthProvider>
    </BrowserRouter>
);
