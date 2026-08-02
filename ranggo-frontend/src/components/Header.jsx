import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { FaShoppingCart, FaUser, FaMapMarkerAlt } from 'react-icons/fa';
import { useAuth } from '../context/AuthContext';
import { useCart } from '../context/CartContext';
import LoginModal from './LoginModal';
import PartnerModal from './PartnerModal';

export default function Header() {
  const navigate = useNavigate();
  const { user, logout } = useAuth();
  const { cart } = useCart();
  
  const [isLoginOpen, setIsLoginOpen] = useState(false);
  const [isPartnerOpen, setIsPartnerOpen] = useState(false);

  const totalCartItems = cart.reduce((sum, item) => sum + item.qty, 0);

  return (
    <>
      <header className="bg-white shadow-sm sticky top-0 z-40">
        <div className="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-2">
          
          {/* Logo & Location */}
          <div className="flex items-center gap-4">
            <h1 
              onClick={() => navigate('/')} 
              className="text-2xl font-black text-[#FF4500] cursor-pointer tracking-tight"
            >
              RangGo
            </h1>
            <div className="hidden sm:flex items-center gap-1 text-xs text-gray-600 bg-gray-100 px-2.5 py-1.5 rounded-full">
              <FaMapMarkerAlt className="text-[#FF4500]" />
              <span className="font-medium">Rangpur City Center</span>
            </div>
          </div>

          {/* Action Buttons */}
          <div className="flex items-center gap-2 sm:gap-3">
            <button 
              onClick={() => setIsPartnerOpen(true)}
              className="text-xs sm:text-sm border border-[#FF4500] text-[#FF4500] px-2.5 py-1.5 rounded-lg hover:bg-orange-50 font-medium transition"
            >
              Sign up as RangGo Partner
            </button>

            {/* Cart Icon */}
            <button 
              onClick={() => navigate('/checkout')}
              className="relative p-2 text-gray-700 hover:text-[#FF4500] transition"
            >
              <FaShoppingCart size={20} />
              {totalCartItems > 0 && (
                <span className="absolute -top-1 -right-1 bg-[#FF4500] text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                  {totalCartItems}
                </span>
              )}
            </button>

            {/* Login / Profile Button */}
            {user ? (
              <button 
                onClick={logout}
                className="bg-gray-100 text-gray-800 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-200"
              >
                Logout
              </button>
            ) : (
              <button 
                onClick={() => setIsLoginOpen(true)}
                className="bg-[#FF4500] text-white px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold flex items-center gap-1.5 hover:bg-orange-600 transition"
              >
                <FaUser size={12} /> Login
              </button>
            )}
          </div>
        </div>
      </header>

      {/* Modals */}
      <LoginModal isOpen={isLoginOpen} onClose={() => setIsLoginOpen(false)} />
      <PartnerModal isOpen={isPartnerOpen} onClose={() => setIsPartnerOpen(false)} />
    </>
  );
}