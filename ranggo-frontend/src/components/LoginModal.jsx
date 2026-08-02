import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { motion, AnimatePresence } from 'framer-motion';
import toast from 'react-hot-toast';
import { IoMdClose } from 'react-icons/io';
import { useAuth } from '../context/AuthContext';
import api from '../api/axiosInstance';

export default function LoginModal({ isOpen, onClose }) {
  const [isSignUp, setIsSignUp] = useState(false);
  const { login } = useAuth();
  const { register, handleSubmit, reset, formState: { errors } } = useForm();

  const onSubmit = async (data) => {
    try {
      const endpoint = isSignUp ? '/auth/signup.php' : '/auth/login.php';
      const res = await api.post(endpoint, data);
      
      // Auth Context-এ ডাটা ও টোকেন সেভ করা
      login(res.data.user, res.data.token);
      if (res.data.status === 'success') {
        toast.success(isSignUp ? 'একাউন্ট সফলভাবে তৈরি হয়েছে!' : 'লগইন সফল হয়েছে!');
      }else{
        toast.error(res.data.message || 'কোনো সমস্যা হয়েছে! আবার চেষ্টা করুন।');
      }
      reset();
      onClose();
    } catch (error) {
      toast.error(error.response?.data?.message || 'কোনো সমস্যা হয়েছে! আবার চেষ্টা করুন।');
    }
  };

  if (!isOpen) return null;

  return (
    <AnimatePresence>
      <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <motion.div 
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          exit={{ opacity: 0, scale: 0.9 }}
          className="bg-white rounded-2xl p-6 w-full max-w-md relative shadow-2xl"
        >
          <button onClick={onClose} className="absolute top-4 right-4 text-gray-500 hover:text-black text-2xl">
            <IoMdClose />
          </button>
          
          <h2 className="text-2xl font-bold mb-4 text-[#FF4500]">
            {isSignUp ? 'RangGo-তে সাইন আপ করুন' : 'RangGo-তে লগইন করুন'}
          </h2>
          
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
            {isSignUp && (
              <div>
                <label className="block text-sm font-medium mb-1">আপনার নাম</label>
                <input 
                  {...register("name", { required: "নাম প্রদান করা আবশ্যক" })} 
                  className="w-full border p-2.5 rounded-lg focus:outline-[#FF4500]"
                  placeholder="আপনার নাম"
                />
                {errors.name && <span className="text-red-500 text-xs">{errors.name.message}</span>}
              </div>
            )}

            <div>
              <label className="block text-sm font-medium mb-1">মোবাইল নম্বর</label>
              <input 
                {...register("phone", { required: "মোবাইল নম্বর আবশ্যক" })} 
                className="w-full border p-2.5 rounded-lg focus:outline-[#FF4500]"
                placeholder="017XXXXXXXX"
              />
              {errors.phone && <span className="text-red-500 text-xs">{errors.phone.message}</span>}
            </div>

            <div>
              <label className="block text-sm font-medium mb-1">পাসওয়ার্ড</label>
              <input 
                type="password"
                {...register("password", { required: "পাসওয়ার্ড আবশ্যক", minLength: { value: 6, message: "কমপক্ষে ৬ ডিজিট দিন" } })} 
                className="w-full border p-2.5 rounded-lg focus:outline-[#FF4500]"
                placeholder="******"
              />
              {errors.password && <span className="text-red-500 text-xs">{errors.password.message}</span>}
            </div>

            <button type="submit" className="w-full bg-[#FF4500] text-white py-2.5 rounded-lg font-bold hover:bg-orange-600 transition">
              {isSignUp ? 'একাউন্ট খুলুন' : 'লগইন করুন'}
            </button>
          </form>

          <p className="text-xs text-center text-gray-500 mt-4">
            {isSignUp ? 'আগে থেকেই একাউন্ট আছে?' : 'নতুন একাউন্ট তৈরি করতে চান?'} {' '}
            <button 
              onClick={() => { setIsSignUp(!isSignUp); reset(); }}
              className="text-[#FF4500] font-bold underline"
            >
              {isSignUp ? 'লগইন করুন' : 'সাইন আপ করুন'}
            </button>
          </p>
        </motion.div>
      </div>
    </AnimatePresence>
  );
}