import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { FaSearch } from 'react-icons/fa';
import api from '../api/axiosInstance';
import Header from '../components/Header';
import AdCarousel from '../components/AdCarousel';
import CategoryFilter from '../components/CategoryFilter';
import FoodCard from '../components/FoodCard';
import Footer from '../components/Footer';

// ডেমো বা ব্যাকএন্ড ডাটা ফেচিং
const fetchRestaurants = async () => {
  try {
    const res = await api.get('/restaurants.php');
    return res.data;
  } catch (err) {
    // API রেডি না থাকলে ডেমো ডাটা রিটার্ন করবে
    return [
      { id: 1, name: "Chef's Kitchen", cuisine: "Burgers, Fast Food", rating: "4.5", discount: "20% OFF" },
      { id: 2, name: "Rangpur Spice & Grill", cuisine: "Biryani, Indian", rating: "4.7", discount: "Free Delivery" },
      { id: 3, name: "Chinese Spice & Burger", cuisine: "Chinese, Fast Food", rating: "4.3", discount: "" }
    ];
  }
};

export default function Home() {
  const [selectedCategory, setSelectedCategory] = useState("All");
  const [searchQuery, setSearchQuery] = useState("");

  const { data: restaurants, isLoading } = useQuery({
    queryKey: ['restaurants'],
    queryFn: fetchRestaurants,
  });

  // সার্চ ও ক্যাটাগরি অনুযায়ী ফিল্টারিং
  const filteredRestaurants = restaurants?.filter(res => {
    const matchesCategory = selectedCategory === "All" || res.cuisine.includes(selectedCategory);
    const matchesSearch = res.name.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesCategory && matchesSearch;
  });

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col justify-between pb-16">
      <div>
        {/* Navbar */}
        <Header />

        <main className="max-w-6xl mx-auto px-4 py-4">
          {/* Ad Banner Carousel */}
          <AdCarousel />

          {/* Search Bar */}
          <div className="relative my-4">
            <input 
              type="text"
              placeholder="Search for restaurants or food..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full bg-white border border-gray-200 pl-10 pr-4 py-2.5 rounded-xl text-sm focus:outline-[#FF4500] shadow-sm"
            />
            <FaSearch className="absolute left-3.5 top-3.5 text-gray-400" size={14} />
          </div>

          {/* Category Filter */}
          <CategoryFilter 
            selectedCategory={selectedCategory} 
            onSelectCategory={setSelectedCategory} 
          />

          {/* Restaurants Grid Section */}
          <section className="my-6">
            <h2 className="text-lg font-bold text-gray-800 mb-4">
              {selectedCategory === "All" ? "আপনার নিকটের রেস্টুরেন্টসমূহ" : `${selectedCategory} রেস্টুরেন্ট`}
            </h2>

            {isLoading ? (
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {[1, 2, 3].map(n => (
                  <div key={n} className="h-52 bg-gray-200 rounded-xl animate-pulse"></div>
                ))}
              </div>
            ) : (
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {filteredRestaurants?.length > 0 ? (
                  filteredRestaurants.map((restaurant) => (
                    <FoodCard key={restaurant.id} restaurant={restaurant} />
                  ))
                ) : (
                  <p className="text-gray-500 text-sm col-span-full text-center py-8">
                    কোনো রেস্টুরেন্ট পাওয়া যায়নি!
                  </p>
                )}
              </div>
            )}
          </section>
        </main>
      </div>

      {/* Fixed Delivery Charge Notice Banner */}
      <div className="fixed bottom-0 left-0 right-0 bg-amber-100 border-t border-amber-300 p-2.5 text-xs text-amber-900 text-center z-30 shadow-md">
        <p><strong>ডেলিভারি চার্জ: ৳০</strong> (রেস্টুরেন্ট থেকে আপনার দূরত্বের ওপর ভিত্তি করে সঠিক ডেলিভারি চার্জ কল করে জানিয়ে দেওয়া হবে)।</p>
        <p className="font-semibold text-[11px]">অনুরোধ: অনুগ্রহ করে যেকোনো একটি রেস্টুরেন্ট থেকে অর্ডার সম্পন্ন করুন।</p>
      </div>

      {/* Footer Component */}
      <Footer />
    </div>
  );
}