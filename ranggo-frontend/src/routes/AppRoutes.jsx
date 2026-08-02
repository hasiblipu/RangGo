import { Routes, Route } from 'react-router-dom';
import Home from '../pages/Home';
import RestaurantDetail from '../pages/RestaurantDetail';
import Checkout from '../pages/Checkout';

export default function AppRoutes() {
  return (
    <Routes>
      <Route path="/" element={<Home />} />
      <Route path="/restaurant/:id" element={<RestaurantDetail />} />
      <Route path="/checkout" element={<Checkout />} />
    </Routes>
  );
}