import { useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { FaPlus, FaMinus, FaArrowLeft, FaShoppingCart } from 'react-icons/fa';
import toast from 'react-hot-toast';

// ডেমো রেস্টুরেন্ট ও মেনু ডাটা
const mockMenu = [
  { id: 101, name: "Classic Chicken Burger", price: 180, description: "Crispy chicken patty with special sauce" },
  { id: 102, name: "Cheese Blast Burger", price: 230, description: "Double patty loaded with melted cheese" },
  { id: 103, name: "French Fries", price: 90, description: "Salted crispy potato fries" },
];

export default function RestaurantDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [cart, setCart] = useState({});

  // Quantity Update Handler
  const updateQuantity = (itemId, change) => {
    setCart((prevCart) => {
      const currentQty = prevCart[itemId] || 0;
      const newQty = currentQty + change;
      
      if (newQty <= 0) {
        const newCart = { ...prevCart };
        delete newCart[itemId];
        return newCart;
      }
      return { ...prevCart, [itemId]: newQty };
    });
  };

  const totalItems = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
  const totalPrice = mockMenu.reduce((sum, item) => sum + (item.price * (cart[item.id] || 0)), 0);

  const handleCheckout = () => {
    if (totalItems === 0) {
      toast.error("অনুগ্রহ করে অন্তত একটি খাবার কার্টে যোগ করুন!");
      return;
    }
    // কার্ট ডাটা নিয়ে চেকআউট পেজে যাওয়া
    navigate('/checkout', { state: { cart, totalPrice } });
  };

  return (
    <div className="min-h-screen bg-gray-50 pb-24">
      {/* Dynamic Header */}
      <header className="bg-white shadow-sm p-4 sticky top-0 z-40 flex items-center justify-between">
        <button onClick={() => navigate(-1)} className="flex items-center gap-2 text-gray-600 hover:text-black font-semibold">
          <FaArrowLeft /> পিছনে যান
        </button>
        <h1 className="font-bold text-lg text-[#FF4500]">Chef's Kitchen (Menu)</h1>
        <div></div>
      </header>

      {/* Menu List */}
      <main className="max-w-3xl mx-auto px-4 py-6">
        <h2 className="text-xl font-bold mb-4">খাবারের তালিকা</h2>
        
        <div className="space-y-4">
          {mockMenu.map((item) => (
            <div key={item.id} className="bg-white p-4 rounded-xl shadow-sm border flex items-center justify-between">
              <div>
                <h3 className="font-bold text-base">{item.name}</h3>
                <p className="text-gray-500 text-xs mb-1">{item.description}</p>
                <p className="text-[#FF4500] font-bold">৳{item.price}</p>
              </div>

              {/* Quantity Selector (+ / -) */}
              <div className="flex items-center gap-3 bg-gray-100 px-3 py-1.5 rounded-lg">
                <button onClick={() => updateQuantity(item.id, -1)} className="text-gray-600 hover:text-[#FF4500]">
                  <FaMinus size={12} />
                </button>
                <span className="font-bold text-sm w-4 text-center">{cart[item.id] || 0}</span>
                <button onClick={() => updateQuantity(item.id, 1)} className="text-gray-600 hover:text-[#FF4500]">
                  <FaPlus size={12} />
                </button>
              </div>
            </div>
          ))}
        </div>
      </main>

      {/* Floating Bottom Cart Action */}
      {totalItems > 0 && (
        <div className="fixed bottom-14 left-0 right-0 bg-white border-t p-4 shadow-lg z-40">
          <div className="max-w-3xl mx-auto flex items-center justify-between">
            <div>
              <p className="text-xs text-gray-500">{totalItems} টি আইটেম সিলেক্ট করা হয়েছে</p>
              <p className="text-lg font-bold text-[#FF4500]">মোট: ৳{totalPrice}</p>
            </div>
            <button 
              onClick={handleCheckout}
              className="bg-[#FF4500] text-white px-6 py-2.5 rounded-xl font-semibold flex items-center gap-2 hover:bg-orange-600 transition"
            >
              <FaShoppingCart /> চেকআউট করুন
            </button>
          </div>
        </div>
      )}
    </div>
  );
}