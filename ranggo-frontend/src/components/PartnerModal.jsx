import { useForm } from 'react-hook-form';
import { motion, AnimatePresence } from 'framer-motion';
import toast from 'react-hot-toast';
import { IoMdClose } from 'react-icons/io';
import api from '../api/axiosInstance';

export default function PartnerModal({ isOpen, onClose }) {
  const { register, handleSubmit, reset, formState: { errors } } = useForm();

  const onSubmit = async (data) => {
    try {
      await api.post('/partner-register', data);
      toast.success('পার্টনার রিকোয়েস্ট সফলভাবে পাঠানো হয়েছে!');
      reset();
      onClose();
    } catch (error) {
      toast.error('সমস্যা হয়েছে! আবার চেষ্টা করুন।');
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
          
          <h2 className="text-xl font-bold mb-4 text-[#FF4500]">Sign up as RangGo Partner</h2>
          
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
            <div>
              <label className="block text-sm font-medium mb-1">রেস্টুরেন্টের নাম</label>
              <input 
                {...register("restaurantName", { required: "রেস্টুরেন্টের নাম আবশ্যক" })} 
                className="w-full border p-2 rounded-lg focus:outline-[#FF4500]"
              />
              {errors.restaurantName && <span className="text-red-500 text-xs">{errors.restaurantName.message}</span>}
            </div>

            <div>
              <label className="block text-sm font-medium mb-1">মোবাইল নম্বর</label>
              <input 
                {...register("phone", { required: "মোবাইল নম্বর আবশ্যক" })} 
                className="w-full border p-2 rounded-lg focus:outline-[#FF4500]"
              />
              {errors.phone && <span className="text-red-500 text-xs">{errors.phone.message}</span>}
            </div>

            <button type="submit" className="w-full bg-[#FF4500] text-white py-2 rounded-lg font-semibold hover:bg-orange-600 transition">
              সাবমিট করুন
            </button>
          </form>
        </motion.div>
      </div>
    </AnimatePresence>
  );
}