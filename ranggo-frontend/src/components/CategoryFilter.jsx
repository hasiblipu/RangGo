const categories = ["All", "Fast Food", "Biryani", "Burgers", "Chinese", "Desserts"];

export default function CategoryFilter({ selectedCategory, onSelectCategory }) {
  return (
    <div className="flex items-center gap-2 overflow-x-auto py-2 my-2 no-scrollbar">
      {categories.map((cat) => (
        <button
          key={cat}
          onClick={() => onSelectCategory(cat)}
          className={`px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition ${
            selectedCategory === cat 
              ? "bg-[#FF4500] text-white shadow-sm" 
              : "bg-white text-gray-600 border border-gray-200 hover:border-[#FF4500]"
          }`}
        >
          {cat}
        </button>
      ))}
    </div>
  );
}