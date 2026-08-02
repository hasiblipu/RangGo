import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import api from '../api/axiosInstance';
import toast from 'react-hot-toast';

export default function AdminDashboard() {
  const [activeTab, setActiveTab] = useState('orders');

  // Fetch Orders
  const { data: orders, refetch: refetchOrders } = useQuery({
    queryKey: ['admin-orders'],
    queryFn: async () => {
      const res = await api.get('/admin/orders.php');
      return res.data?.data || [];
    }
  });

  // Order Status Update Logic
  const handleUpdateStatus = async (orderId, newStatus) => {
    try {
      await api.post('/admin/orders.php', { order_id: orderId, status: newStatus });
      toast.success("অর্ডার স্ট্যাটাস আপডেট হয়েছে");
      refetchOrders();
    } catch (err) {
      toast.error("আপডেট করা সম্ভব হয়নি");
    }
  };

  return (
    <div className="min-h-screen bg-gray-100 p-6">
      <div className="max-w-6xl mx-auto bg-white rounded-xl p-6 shadow-sm">
        <h1 className="text-2xl font-bold text-[#FF4500] mb-6">RangGo Admin Control Panel</h1>

        {/* Tab Navigation */}
        <div className="flex gap-4 border-b mb-6">
          <button 
            onClick={() => setActiveTab('orders')} 
            className={`pb-2 px-2 font-semibold text-sm ${activeTab === 'orders' ? 'border-b-2 border-[#FF4500] text-[#FF4500]' : 'text-gray-500'}`}
          >
            Orders Management
          </button>
        </div>

        {/* Orders Table */}
        {activeTab === 'orders' && (
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm border-collapse">
              <thead>
                <tr className="bg-gray-50 border-b">
                  <th className="p-3">Order ID</th>
                  <th className="p-3">Customer</th>
                  <th className="p-3">Phone</th>
                  <th className="p-3">Total</th>
                  <th className="p-3">Status</th>
                  <th className="p-3">Action</th>
                </tr>
              </thead>
              <tbody>
                {orders?.map((o) => (
                  <tr key={o.id} className="border-b">
                    <td className="p-3 font-bold">#{o.id}</td>
                    <td className="p-3">{o.customer_name}</td>
                    <td className="p-3">{o.customer_phone}</td>
                    <td className="p-3 font-semibold text-[#FF4500]">৳{o.total_amount}</td>
                    <td className="p-3">
                      <span className="bg-amber-100 text-amber-800 text-xs px-2 py-1 rounded font-bold">
                        {o.status}
                      </span>
                    </td>
                    <td className="p-3 flex gap-2">
                      <button 
                        onClick={() => handleUpdateStatus(o.id, 'approved')} 
                        className="bg-green-600 text-white text-xs px-2.5 py-1 rounded hover:bg-green-700"
                      >
                        Approve
                      </button>
                      <button 
                        onClick={() => handleUpdateStatus(o.id, 'cancelled')} 
                        className="bg-red-600 text-white text-xs px-2.5 py-1 rounded hover:bg-red-700"
                      >
                        Cancel
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}