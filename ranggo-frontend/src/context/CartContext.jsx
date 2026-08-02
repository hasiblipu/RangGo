import { createContext, useContext, useState } from 'react';

const CartContext = createContext(null);

export const CartProvider = ({ children }) => {
  const [cart, setCart] = useState([]);

  const addToCart = (item, restaurantId) => {
    setCart((prev) => {
      // ভিন্ন রেস্টুরেন্টের খাবার থাকলে সতর্ক করা
      if (prev.length > 0 && prev[0].restaurantId !== restaurantId) {
        alert("আপনি শুধুমাত্র যেকোনো একটি রেস্টুরেন্ট থেকে খাবার অর্ডার করতে পারবেন!");
        return prev;
      }
      const existing = prev.find((i) => i.id === item.id);
      if (existing) {
        return prev.map((i) => i.id === item.id ? { ...i, qty: i.qty + 1 } : i);
      }
      return [...prev, { ...item, qty: 1, restaurantId }];
    });
  };

  const removeFromCart = (itemId) => {
    setCart((prev) => prev.filter((item) => item.id !== itemId));
  };

  const clearCart = () => setCart([]);

  return (
    <CartContext.Provider value={{ cart, addToCart, removeFromCart, clearCart }}>
      {children}
    </CartContext.Provider>
  );
};

export const useCart = () => useContext(CartContext);