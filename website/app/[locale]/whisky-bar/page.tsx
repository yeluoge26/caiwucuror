import { Metadata } from 'next';
import Image from 'next/image';

export const metadata: Metadata = {
  title: 'T Whisky Bar Da Nang | Premium Whisky & Signature Cocktails on 4th Floor',
  description: 'Experience T Whisky Bar on the 4th floor of Tech Coffee Da Nang. Premium whisky collection, signature cocktails, live music weekends, and an exclusive night atmosphere.',
  keywords: [
    'T Whisky Bar Da Nang',
    'whisky bar Da Nang',
    'cocktail bar Da Nang',
    'premium whisky Vietnam',
    'signature cocktails Da Nang',
    'night bar Da Nang',
    'rooftop bar Da Nang',
    'live music bar Da Nang',
    'whisky tasting Da Nang',
    'best bar Da Nang'
  ],
  openGraph: {
    title: 'T Whisky Bar | Premium Whisky & Signature Cocktails in Da Nang',
    description: 'Enjoy premium whisky, signature cocktails, and a high-end night experience at T Whisky Bar (4th floor).',
    images: ['/images/interior/whisky-bar-sign.jpg'],
  },
};

export default function WhiskyBarPage() {
  return (
    <div className="min-h-screen py-12 md:py-20 bg-gray-900 text-white">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        {/* Hero Section */}
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">T Whisky Bar</h1>
          <p className="text-xl text-gray-300 max-w-2xl mx-auto">
            Premium Whisky & Signature Cocktails
          </p>
          <p className="text-yellow-400 font-semibold mt-4">
            On the 4th Floor • Open Daily 5PM - 11PM
          </p>
        </div>

        {/* Hero Image */}
        <section className="mb-16">
          <div className="relative rounded-lg overflow-hidden shadow-xl max-w-2xl mx-auto">
            <Image
              src="/images/interior/whisky-bar-sign.jpg"
              alt="T Whisky Bar Neon Sign - Premium whisky bar on the 4th floor"
              width={600}
              height={800}
              className="w-full h-auto"
              priority
            />
          </div>
        </section>

        {/* About the Bar */}
        <section className="mb-16">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl font-bold mb-4">About T Whisky Bar</h2>
              <p className="text-gray-300 mb-4">
                Located on the 4th floor of Tech Coffee, T Whisky Bar offers an exclusive experience 
                for whisky enthusiasts and cocktail lovers. Our curated selection of 
                premium whiskies from around the world, combined with expertly crafted 
                signature cocktails, creates the perfect atmosphere for a sophisticated evening.
              </p>
              <p className="text-gray-300 mb-4">
                Whether you're a connoisseur or new to whisky, our knowledgeable staff 
                will guide you through our collection and help you discover your perfect pour.
              </p>
              <ul className="space-y-2 text-gray-300">
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">★</span>
                  Premium whisky collection from Scotland, Japan, Ireland & USA
                </li>
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">★</span>
                  Expertly crafted signature cocktails
                </li>
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">★</span>
                  Live music every weekend
                </li>
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">★</span>
                  Tech-themed atmosphere with neon aesthetics
                </li>
              </ul>
            </div>
            <div className="bg-gradient-to-br from-yellow-900/50 to-gray-800/50 backdrop-blur-sm rounded-lg p-8 text-center">
              <div className="text-8xl mb-4">🥃</div>
              <h3 className="text-2xl font-bold mb-2">Premium Selection</h3>
              <p className="text-gray-300">World-class whiskies and cocktails</p>
              <div className="mt-6 pt-6 border-t border-gray-700">
                <p className="text-yellow-400 font-semibold">Happy Hour</p>
                <p className="text-gray-300">Daily 5PM - 7PM</p>
                <p className="text-white font-bold">20% Off All Cocktails</p>
              </div>
            </div>
          </div>
        </section>

        {/* Signature Cocktails */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">Signature Cocktails</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {[
              {
                name: 'Tech Coffee Old Fashioned',
                description: 'A modern twist on the classic, infused with Vietnamese coffee notes and aromatic bitters',
                price: '180,000 VND',
                icon: '☕',
              },
              {
                name: 'AI Manhattan',
                description: 'Sophisticated blend of premium whisky, sweet vermouth, and a dash of innovation',
                price: '170,000 VND',
                icon: '🤖',
              },
              {
                name: 'Creative Space Sour',
                description: 'Refreshing whisky sour with a creative twist of passion fruit and egg white foam',
                price: '160,000 VND',
                icon: '🎨',
              },
              {
                name: 'Da Nang Sunset',
                description: 'Tropical cocktail inspired by Da Nang\'s beautiful sunsets with local fruits',
                price: '150,000 VND',
                icon: '🌅',
              },
              {
                name: 'Neon Nights',
                description: 'A glowing blue cocktail with butterfly pea flower and citrus, perfect for photos',
                price: '165,000 VND',
                icon: '💙',
              },
              {
                name: 'Robot Bartender Special',
                description: 'Our signature mix featuring premium bourbon, honey, and a hint of smoke',
                price: '190,000 VND',
                icon: '🤖',
              },
            ].map((cocktail, index) => (
              <div key={index} className="bg-gray-800 p-6 rounded-lg border border-gray-700 hover:border-yellow-600 transition-colors">
                <div className="flex justify-between items-start mb-2">
                  <div className="flex items-center">
                    <span className="text-2xl mr-3">{cocktail.icon}</span>
                    <h3 className="text-xl font-semibold">{cocktail.name}</h3>
                  </div>
                  <span className="text-yellow-400 font-bold whitespace-nowrap ml-4">{cocktail.price}</span>
                </div>
                <p className="text-gray-300 text-sm ml-10">{cocktail.description}</p>
              </div>
            ))}
          </div>
        </section>

        {/* Whisky Collection */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">Whisky Collection</h2>
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            {[
              { name: 'Single Malt Scotch', region: 'Scotland', icon: '🏴󠁧󠁢󠁳󠁣󠁴󠁿' },
              { name: 'Japanese Whisky', region: 'Japan', icon: '🇯🇵' },
              { name: 'Bourbon', region: 'USA', icon: '🇺🇸' },
              { name: 'Irish Whiskey', region: 'Ireland', icon: '🇮🇪' },
              { name: 'Rye Whiskey', region: 'USA', icon: '🌾' },
              { name: 'Blended Scotch', region: 'Scotland', icon: '🥃' },
              { name: 'Premium Reserve', region: 'Various', icon: '⭐' },
              { name: 'Limited Edition', region: 'Exclusive', icon: '💎' },
            ].map((category, index) => (
              <div key={index} className="bg-gray-800 p-4 rounded-lg text-center border border-gray-700 hover:border-yellow-600 transition-colors">
                <div className="text-3xl mb-2">{category.icon}</div>
                <h3 className="font-semibold">{category.name}</h3>
                <p className="text-gray-400 text-sm">{category.region}</p>
              </div>
            ))}
          </div>
        </section>

        {/* Events & Promotions */}
        <section className="bg-gray-800 rounded-lg p-8 md:p-12 mb-16">
          <h2 className="text-3xl font-bold mb-6 text-center">Events & Promotions</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[
              {
                title: 'Whisky Tasting Night',
                date: 'Every Friday',
                description: 'Sample premium whiskies with expert guidance. Learn about different regions and flavor profiles.',
                icon: '🥃',
              },
              {
                title: 'Happy Hour',
                date: 'Daily 5-7 PM',
                description: '20% off on all cocktails. The perfect way to start your evening.',
                icon: '🍸',
              },
              {
                title: 'Live Music',
                date: 'Fri & Sat Nights',
                description: 'Enjoy live acoustic performances while you drink. Local and international artists.',
                icon: '🎵',
              },
            ].map((event, index) => (
              <div key={index} className="bg-gray-900 p-6 rounded-lg border border-gray-700">
                <div className="text-4xl mb-4">{event.icon}</div>
                <h3 className="text-xl font-semibold mb-2">{event.title}</h3>
                <p className="text-yellow-400 mb-2">{event.date}</p>
                <p className="text-gray-300 text-sm">{event.description}</p>
              </div>
            ))}
          </div>
        </section>

        {/* Atmosphere Gallery */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">The Atmosphere</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video">
              <Image
                src="/images/interior/neon-lounge.jpg"
                alt="T Whisky Bar Lounge - Neon-lit atmosphere"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video">
              <Image
                src="/images/interior/toy-display.jpg"
                alt="T Whisky Bar Decor - Collectibles and neon lights"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
          </div>
        </section>

        {/* Reservation */}
        <section className="text-center">
          <h2 className="text-3xl font-bold mb-4">Make a Reservation</h2>
          <p className="text-gray-300 mb-6">Book your table for a premium experience</p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <a
              href="tel:+84388997186"
              className="px-8 py-4 bg-yellow-600 text-white rounded-lg font-semibold hover:bg-yellow-700 transition-colors"
            >
              Call to Reserve: (+84) 0388 997 186
            </a>
            <a
              href="mailto:techcafedanang@gmail.com"
              className="px-8 py-4 bg-gray-700 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors"
            >
              Email Us
            </a>
          </div>
          <p className="text-gray-400 mt-6">
            Located on the 4th Floor of Tech Coffee<br />
            90 Bạch Đằng, Hải Châu, Đà Nẵng
          </p>
        </section>
      </div>
    </div>
  );
}
