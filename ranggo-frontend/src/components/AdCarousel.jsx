import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';

const banners = [
  { id: 1, title: "Special Offer: 30% Off!", bg: "from-orange-500 to-red-600", desc: "Your first delivery order has special discount." },
  { id: 2, title: "Rangpur's Top Restaurants", bg: "from-amber-500 to-orange-600", desc: "Get food delivered under 30 mins." },
];

export default function AdCarousel() {
  const [current, setCurrent] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrent((prev) => (prev + 1) % banners.length);
    }, 4000);
    return () => clearInterval(timer);
  }, []);

  return (
    <div className="relative h-44 sm:h-52 w-full rounded-2xl overflow-hidden shadow-md my-4">
      <AnimatePresence mode="wait">
        <motion.div
          key={banners[current].id}
          initial={{ opacity: 0, x: 50 }}
          animate={{ opacity: 1, x: 0 }}
          exit={{ opacity: 0, x: -50 }}
          transition={{ duration: 0.5 }}
          className={`absolute inset-0 bg-gradient-to-r ${banners[current].bg} text-white p-6 flex flex-col justify-center items-start`}
        >
          <span className="bg-white/20 text-xs px-2.5 py-1 rounded-full mb-2 font-medium">RangGo Offers</span>
          <h2 className="text-2xl sm:text-3xl font-extrabold mb-1">{banners[current].title}</h2>
          <p className="text-sm opacity-90 mb-3">{banners[current].desc}</p>
          <button className="bg-white text-[#FF4500] px-4 py-1.5 rounded-lg text-sm font-bold shadow hover:bg-gray-100">
            এখনই দেখুন
          </button>
        </motion.div>
      </AnimatePresence>
    </div>
  );
}