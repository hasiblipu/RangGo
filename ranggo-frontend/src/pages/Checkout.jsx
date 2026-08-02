import { useForm } from 'react-hook-form';
import { useLocation, useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import { useAuth } from '../context/AuthContext';

export default function Checkout() {
  const { user } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const { totalPrice } = location.state || { totalPrice: 0 };
  
  const { register, handleSubmit, formState: { errors } } = useForm();

  const handleOrderSubmit = (data) => {
    // লগইন চেক (ইউজার না থাকলে অর্ডার প্লেস হবে না)
    if (!user) {
      toast.error("অর্ডার সম্পন্ন করতে আগে লগইন/সাইন-আপ করুন!");
      return;
    }

    toast.success("অর্ডার সফলভাবে জমা হয়েছে! শীঘ্রই আমাদের প্রতিনিধি কল করবে।");
    navigate('/');
  };

  return (
    <div className="min-h-screen bg-gray-50 py-6 px-4 pb-20">
      <div className="max-w-md mx-auto bg-white p-6 rounded-2xl shadow-sm border">
        <h2 className="text-xl font-bold mb-4 text-[#FF4500]">অর্ডার কনফার্মেশন</h2>
        
        <form onSubmit={handleSubmit(handleOrderSubmit)} className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-1">আপনার নাম</label>
            <input 
              {...register("name", { required: "নাম প্রদান করা আবশ্যক" })}
              placeholder="আপনার নাম লিখুন"
              className="w-full border p-2.5 rounded-lg focus:outline-[#FF4500]"
            />
            {errors.name && <span className="text-red-500 text-xs">{errors.name.message}</span>}
          </div>

          <div>
            <label className="block text-sm font-medium mb-1">মোবাইল নম্বর</label>
            <input 
              {...register("phone", { required: "মোবাইল নম্বর আবশ্যক" })}
              placeholder="017XXXXXXXX"
              className="w-full border p-2.5 rounded-lg focus:outline-[#FF4500]"
            />
            {errors.phone && <span className="text-red-500 text-xs">{errors.phone.message}</span>}
          </div>

          <div>
            <label className="block text-sm font-medium mb-1">সম্পূর্ণ ঠিকানা</label>
            <textarea 
              {...register("address", { required: "ঠিকানা প্রদান করা আবশ্যক" })}
              placeholder="বাসা/রোড নম্বর এবং এলাকা..."
              className="w-full border p-2.5 rounded-lg focus:outline-[#FF4500] h-20"
            />
            {errors.address && <span className="text-red-500 text-xs">{errors.address.message}</span>}
          </div>

          {/* Delivery Note Box (User Requirement Specific Text) */}
          <div className="bg-amber-50 border border-amber-300 p-3.5 rounded-xl text-xs text-amber-900 space-y-1">
            <p>
              <strong>ডেলিভারি চার্জ: ৳০</strong> (রেস্টুরেন্ট থেকে আপনার দূরত্বের ওপর ভিত্তি করে সঠিক ডেলিভারি চার্জ কল করে জানিয়ে দেওয়া হবে)।
            </p>
            <p className="font-semibold text-amber-800">
              অনুরোধ: অনুগ্রহ করে যেকোনো একটি রেস্টুরেন্ট থেকে অর্ডার সম্পন্ন করুন।
            </p>
          </div>

          <div className="border-t pt-3 flex justify-between items-center font-bold text-lg">
            <span>সর্বমোট:</span>
            <span className="text-[#FF4500]">৳{totalPrice}</span>
          </div>

          <button 
            type="submit"
            className="w-full bg-[#FF4500] text-white py-3 rounded-xl font-bold hover:bg-orange-600 transition shadow-md"
          >
            অর্ডার কনফার্ম করুন
          </button>
        </form>
      </div>
    </div>
  );
}