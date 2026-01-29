import { Metadata } from 'next';
import Image from 'next/image';

export const metadata: Metadata = {
  title: 'Tech Coffee Menu | Coffee, Milk Tea, Matcha & Signature Drinks Da Nang',
  description: 'Explore Tech Coffee\'s full menu featuring specialty coffee, Vietnamese coffee, milk tea, matcha, fresh juices, smoothies, and signature drinks. Prices from 30,000 VND.',
  keywords: [
    'Tech Coffee menu',
    'Da Nang coffee menu',
    'Vietnamese coffee Da Nang',
    'milk tea Da Nang',
    'matcha latte Da Nang',
    'fresh juice Da Nang',
    'smoothie Da Nang',
    'cafe menu Vietnam',
    'best coffee Da Nang',
    'specialty drinks Da Nang'
  ],
  openGraph: {
    title: 'Tech Coffee Menu | Coffee, Milk Tea & Signature Drinks',
    description: 'Explore our specialty coffee, milk tea, matcha, fresh juices, and signature drinks at Tech Coffee Da Nang.',
    images: ['/images/menu/menu-full.jpg'],
  },
};

export default function MenuPage() {
  const menuCategories = [
    {
      name: 'Trà / Tea',
      nameEn: 'Tea',
      items: [
        { name: 'Trà Dâu Tằm', nameEn: 'Mulberry Iced Tea', price: '49,000' },
        { name: 'Trà Đào Cam Sả', nameEn: 'Peach & Orange Tea', price: '49,000' },
        { name: 'Trà Bưởi Hồng', nameEn: 'Pink Grapefruit Ice Tea', price: '49,000' },
        { name: 'Trà Cam Mận', nameEn: 'Plum & Orange Iced Tea', price: '49,000' },
        { name: 'Trà Vải Bưởi Hồng', nameEn: 'Pink Grapfruit & Lychee Iced Tea', price: '49,000' },
        { name: 'Trà Xoài Thanh Đào', nameEn: 'Mango & Peach Iced Tea', price: '49,000' },
        { name: 'Trà Vải Lựu Đỏ', nameEn: 'Pomegranate & Lychee Iced Tea', price: '49,000' },
        { name: 'Trà Cam Gừng Mật Ong', nameEn: 'Hot Ginger & Honey Tea (hot)', price: '49,000' },
        { name: 'Trà Vải Bưởi hồng', nameEn: 'Lychee Grapefruit Tea', price: '49,000' },
        { name: 'Trà Vải Dưa hấu', nameEn: 'Lychee Watermelon Tea', price: '49,000' },
        { name: 'Trà Bưởi hồng Dưa hấu', nameEn: 'Pink Grapefruit Watermelon Tea', price: '49,000' },
      ],
    },
    {
      name: 'Cà Phê / Coffee',
      nameEn: 'Coffee',
      items: [
        { name: 'Cà phê đen', nameEn: 'Espresso', price: '30,000' },
        { name: 'Cà phê sữa', nameEn: 'Caphesua', price: '35,000' },
        { name: 'Cà phê bạc xỉu', nameEn: 'Light coffee', price: '39,000' },
        { name: 'Cà phê muối', nameEn: 'Salted coffee', price: '39,000' },
        { name: 'Cà phê dừa', nameEn: 'Coconut coffee', price: '49,000' },
        { name: 'Cà phê trứng', nameEn: 'Egg Coffee', price: '39,000' },
        { name: 'Americano', nameEn: 'Americano', price: '39,000' },
        { name: 'Cappuccino', nameEn: 'Cappuccino', price: '40,000' },
        { name: 'Coffee Latte', nameEn: 'Coffee Latte', price: '45,000' },
        { name: 'Special Tech coffee', nameEn: 'Taro cream & coffee', price: '55,000', featured: true },
      ],
    },
    {
      name: 'Matcha',
      nameEn: 'Matcha',
      items: [
        { name: 'Matcha Latte', nameEn: 'Matcha Latte (hot / ice)', price: '49,000' },
        { name: 'Matcha Oats Milk', nameEn: 'Matcha Oats Milk (hot / ice)', price: '55,000', featured: true },
        { name: 'Matcha Kem Muối', nameEn: 'Salted Cream Matcha', price: '49,000' },
        { name: 'Matcha Xoài Chín', nameEn: 'Mamago', price: '55,000', featured: true },
      ],
    },
    {
      name: 'Trà Sữa / Milk Tea',
      nameEn: 'Milk Tea',
      items: [
        { name: 'Hồng Trà Sữa Shan Tuyết', nameEn: 'Shan Tuyet Milk Tea', price: '49,000' },
        { name: 'Sữa tươi Trân châu đường đen', nameEn: 'Black Sugar Pearl Fresh Milk', price: '45,000' },
        { name: 'Trà Sữa Gạo Rang', nameEn: 'Roasted Rice Milk Tea', price: '49,000' },
      ],
    },
    {
      name: 'Sữa Chua / Yogurt',
      nameEn: 'Yogurt',
      items: [
        { name: 'Sữa chua Trái cây', nameEn: 'Fruit Yogurt', price: '45,000' },
        { name: 'Sữa chua Đào', nameEn: 'Peach Yogurt', price: '45,000' },
        { name: 'Sữa chua Việt Quất', nameEn: 'Blueberry Yogurt', price: '45,000' },
      ],
    },
    {
      name: 'Nước Ép / Juices',
      nameEn: 'Fresh Juices',
      items: [
        { name: 'Nước ép Cam', nameEn: 'Orange juice', price: '45,000' },
        { name: 'Nước ép Dứa', nameEn: 'Pineapple Juice', price: '45,000' },
        { name: 'Nước ép Dưa hấu', nameEn: 'Watermelon juice', price: '45,000' },
        { name: 'Nước ép Chanh dây', nameEn: 'Passion juice', price: '45,000' },
      ],
    },
    {
      name: 'Sinh Tố / Smoothies',
      nameEn: 'Smoothies',
      items: [
        { name: 'Sinh Tố Xoài Dừa Non', nameEn: 'Mango Coconut Smoothie', price: '59,000', featured: true },
        { name: 'Sinh Tố Dừa Non Matcha', nameEn: 'Coconut Matcha Smoothie', price: '59,000' },
        { name: 'Sinh Tố Việt Quất', nameEn: 'Blueberry Smoothie', price: '59,000' },
        { name: 'Sinh Tố Oreo', nameEn: 'Oreo Smoothie', price: '59,000', featured: true },
      ],
    },
    {
      name: 'Cacao',
      nameEn: 'Cacao',
      items: [
        { name: 'Cacao Sữa', nameEn: 'Cocoa Latte (hot / ice)', price: '45,000' },
        { name: 'Cacao Kem Trứng', nameEn: 'Egg Cream Cocoa (hot / ice)', price: '45,000' },
        { name: 'Cacao Kem Muối', nameEn: 'Salted Cream Cocoa', price: '45,000' },
      ],
    },
    {
      name: 'Cold Brew',
      nameEn: 'Cold Brew',
      items: [
        { name: 'Cold Brew Dâu Tằm', nameEn: 'Cold Brew Mulberry', price: '55,000', featured: true },
        { name: 'Cold Brew Cam Mận', nameEn: 'Cold Brew Plum & Orange', price: '55,000' },
      ],
    },
  ];

  return (
    <div className="min-h-screen py-12 md:py-20">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        {/* Hero Section */}
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">Menu</h1>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            Discover our specialty drinks crafted with passion
          </p>
          <p className="text-sm text-gray-500 mt-2">
            WiFi: "TECH COFFEE" | Password: 88888888
          </p>
        </div>

        {/* Full Menu Image */}
        <section className="mb-16">
          <div className="relative rounded-lg overflow-hidden shadow-xl max-w-4xl mx-auto">
            <Image
              src="/images/menu/menu-full.jpg"
              alt="Tech Coffee Full Menu - Complete drink menu with prices"
              width={1200}
              height={800}
              className="w-full h-auto"
              priority
            />
          </div>
        </section>

        {/* Drink Gallery */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">Featured Drinks</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="relative group overflow-hidden rounded-lg shadow-lg">
              <Image
                src="/images/drinks/fruit-drinks.jpg"
                alt="Tech Coffee Tropical Fruit Drinks"
                width={400}
                height={500}
                className="w-full h-80 object-cover transition-transform duration-300 group-hover:scale-110"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
                <div className="text-white">
                  <h3 className="font-bold text-lg">Tropical Collection</h3>
                  <p className="text-sm text-gray-200">Fresh fruit drinks</p>
                </div>
              </div>
            </div>
            <div className="relative group overflow-hidden rounded-lg shadow-lg">
              <Image
                src="/images/drinks/matcha-space.jpg"
                alt="Tech Coffee Matcha Galaxy Series"
                width={400}
                height={500}
                className="w-full h-80 object-cover transition-transform duration-300 group-hover:scale-110"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
                <div className="text-white">
                  <h3 className="font-bold text-lg">Matcha Galaxy</h3>
                  <p className="text-sm text-gray-200">Premium matcha series</p>
                </div>
              </div>
            </div>
            <div className="relative group overflow-hidden rounded-lg shadow-lg">
              <Image
                src="/images/drinks/moon-drinks.jpg"
                alt="Tech Coffee Moonlight Collection"
                width={400}
                height={500}
                className="w-full h-80 object-cover transition-transform duration-300 group-hover:scale-110"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
                <div className="text-white">
                  <h3 className="font-bold text-lg">Moonlight Collection</h3>
                  <p className="text-sm text-gray-200">Space-themed specials</p>
                </div>
              </div>
            </div>
            <div className="relative group overflow-hidden rounded-lg shadow-lg">
              <Image
                src="/images/drinks/coffee-robot.jpg"
                alt="Tech Coffee AI Robot Barista"
                width={400}
                height={500}
                className="w-full h-80 object-cover transition-transform duration-300 group-hover:scale-110"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
                <div className="text-white">
                  <h3 className="font-bold text-lg">AI Coffee</h3>
                  <p className="text-sm text-gray-200">Robot-assisted brewing</p>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Menu Categories */}
        {menuCategories.map((category, index) => (
          <section key={index} className="mb-12">
            <h2 className="text-2xl font-bold mb-2">{category.name}</h2>
            <p className="text-gray-500 text-sm mb-6">{category.nameEn}</p>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {category.items.map((item, itemIndex) => (
                <div
                  key={itemIndex}
                  className={`p-4 rounded-lg border transition-shadow hover:shadow-md ${
                    item.featured 
                      ? 'bg-gradient-to-r from-primary/10 to-secondary/10 border-primary/30' 
                      : 'bg-white border-gray-200'
                  }`}
                >
                  <div className="flex justify-between items-start">
                    <div>
                      <h3 className="font-semibold text-gray-800">{item.name}</h3>
                      <p className="text-sm text-gray-500">{item.nameEn}</p>
                    </div>
                    <span className="text-primary font-bold whitespace-nowrap ml-4">
                      {item.price} ₫
                    </span>
                  </div>
                  {item.featured && (
                    <span className="inline-block mt-2 text-xs bg-primary text-white px-2 py-1 rounded">
                      ★ Recommended
                    </span>
                  )}
                </div>
              ))}
            </div>
          </section>
        ))}

        {/* Special Note */}
        <section className="bg-gradient-primary text-white rounded-lg p-8 md:p-12 text-center">
          <h2 className="text-3xl font-bold mb-4">Special Tech Coffee</h2>
          <p className="text-xl mb-4">Taro Cream & Coffee - Our Signature Drink</p>
          <p className="text-4xl font-bold mb-4">55,000 ₫</p>
          <p className="text-gray-100">
            A unique blend of Vietnamese coffee with creamy taro, 
            crafted exclusively at Tech Coffee Da Nang
          </p>
        </section>

        {/* Location Info */}
        <section className="mt-12 text-center">
          <p className="text-gray-600">
            <strong>Address:</strong> 90 Bạch Đằng, Hải Châu, Đà Nẵng
          </p>
          <p className="text-gray-600 mt-2">
            <strong>Hours:</strong> Mon-Sun 8:00 AM - 11:00 PM
          </p>
        </section>
      </div>
    </div>
  );
}
