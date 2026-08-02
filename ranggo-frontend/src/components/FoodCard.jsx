import { useNavigate } from 'react-router-dom';
import { FaStar } from 'react-icons/fa';

export default function FoodCard({ restaurant }) {
  const navigate = useNavigate();

  return (
    <div 
      onClick={() => navigate(`/restaurant/${restaurant.id}`)}
      className="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition cursor-pointer flex flex-col justify-between"
    >
      <div>
        <div className="h-40 bg-gray-200 rounded-lg mb-3 overflow-hidden relative">
          <img 
            src={restaurant.image || "https://via.placeholder.com/300x180?text=RangGo+Food"} 
            alt={restaurant.name}
            className="w-full h-full object-cover"
          />
          {restaurant.discount && (
            <span className="absolute top-2 left-2 bg-[#FF4500] text-white text-xs px-2 py-0.5 rounded font-bold">
              {restaurant.discount}
            </span>
          )}
        </div>
        <h3 className="font-bold text-lg text-gray-800">{restaurant.name}</h3>
        <p className="text-gray-500 text-xs mt-1">{restaurant.cuisine}</p>
      </div>

      <div className="mt-3 flex items-center justify-between border-t pt-2">
        <span className="text-xs bg-amber-50 text-amber-700 px-2 py-1 rounded flex items-center gap-1 font-semibold">
          <FaStar className="text-amber-500" /> {restaurant.rating || "4.5"}
        </span>
        <button className="bg-[#FF4500] text-white text-xs px-3 py-1.5 rounded-lg font-semibold hover:bg-orange-600">
          মেনু দেখুন
        </button>
      </div>
    </div>
  );
}