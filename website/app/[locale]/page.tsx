'use client';

import { useTranslations } from 'next-intl';
import Link from 'next/link';
import Image from 'next/image';

export default function HomePage() {
  const t = useTranslations('home');

  return (
    <div className="min-h-screen">
      {/* Hero Section with Background Image */}
      <section className="relative bg-gradient-primary text-white py-20 md:py-32 overflow-hidden">
        <div className="absolute inset-0 opacity-20">
          <Image
            src="/images/interior/main-hall.jpg"
            alt="Tech Coffee Da Nang Interior"
            fill
            className="object-cover"
            priority
          />
        </div>
        <div className="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="max-w-4xl mx-auto text-center">
            <h1 className="text-4xl md:text-6xl font-bold mb-6">
              {t('hero.title')}
            </h1>
            <p className="text-lg md:text-xl mb-8 text-gray-100">
              {t('hero.description')}
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link
                href="/working-zone"
                className="px-8 py-4 bg-white text-primary rounded-lg font-semibold hover:bg-gray-100 transition-colors"
              >
                {t('hero.cta1')}
              </Link>
              <Link
                href="/ai-experience"
                className="px-8 py-4 bg-transparent border-2 border-white text-white rounded-lg font-semibold hover:bg-white hover:text-primary transition-colors"
              >
                {t('hero.cta2')}
              </Link>
              <Link
                href="/menu"
                className="px-8 py-4 bg-transparent border-2 border-white text-white rounded-lg font-semibold hover:bg-white hover:text-primary transition-colors"
              >
                {t('hero.cta3')}
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Featured Drinks Gallery */}
      <section className="py-16 md:py-24 bg-white">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold mb-4">Signature Drinks</h2>
            <p className="text-xl text-gray-600">Crafted with creativity and passion</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="relative group overflow-hidden rounded-lg shadow-lg">
              <Image
                src="/images/drinks/fruit-drinks.jpg"
                alt="Tech Coffee Fruit Drinks - Colorful tropical beverages with astronaut decoration"
                width={400}
                height={500}
                className="w-full h-80 object-cover transition-transform duration-300 group-hover:scale-110"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
                <div className="text-white">
                  <h3 className="font-bold text-lg">Tropical Paradise</h3>
                  <p className="text-sm text-gray-200">Fresh fruit drinks</p>
                </div>
              </div>
            </div>
            <div className="relative group overflow-hidden rounded-lg shadow-lg">
              <Image
                src="/images/drinks/moon-drinks.jpg"
                alt="Tech Coffee Moon Drinks - Space themed cocktails with astronaut figurines"
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
                src="/images/drinks/matcha-space.jpg"
                alt="Tech Coffee Matcha Space - Green matcha latte with galaxy background"
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
                src="/images/drinks/coffee-robot.jpg"
                alt="Tech Coffee Robot Barista - AI robot serving specialty coffee"
                width={400}
                height={500}
                className="w-full h-80 object-cover transition-transform duration-300 group-hover:scale-110"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
                <div className="text-white">
                  <h3 className="font-bold text-lg">AI Crafted Coffee</h3>
                  <p className="text-sm text-gray-200">Robot-assisted brewing</p>
                </div>
              </div>
            </div>
          </div>
          <div className="text-center mt-8">
            <Link
              href="/menu"
              className="inline-block px-8 py-4 bg-gradient-primary text-white rounded-lg font-semibold hover:opacity-90 transition-opacity"
            >
              View Full Menu
            </Link>
          </div>
        </div>
      </section>

      {/* Free Working Zone Section */}
      <section className="py-16 md:py-24 bg-gray-50">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div className="relative rounded-lg overflow-hidden shadow-xl">
              <Image
                src="/images/interior/neon-lounge.jpg"
                alt="Tech Coffee Free Working Zone - Modern co-working space with neon lights"
                width={600}
                height={400}
                className="w-full h-auto"
              />
            </div>
            <div>
              <h2 className="text-3xl md:text-4xl font-bold mb-4">{t('workingZone.title')}</h2>
              <p className="text-xl text-gray-600 mb-6">{t('workingZone.subtitle')}</p>
              <div className="grid grid-cols-2 gap-4 mb-8">
                <div className="bg-white p-4 rounded-lg shadow-md text-center">
                  <div className="text-3xl mb-2">🖥️</div>
                  <h3 className="font-semibold text-sm">{t('workingZone.features.gpu')}</h3>
                </div>
                <div className="bg-white p-4 rounded-lg shadow-md text-center">
                  <div className="text-3xl mb-2">🤖</div>
                  <h3 className="font-semibold text-sm">{t('workingZone.features.chatgpt')}</h3>
                </div>
                <div className="bg-white p-4 rounded-lg shadow-md text-center">
                  <div className="text-3xl mb-2">🎨</div>
                  <h3 className="font-semibold text-sm">{t('workingZone.features.adobe')}</h3>
                </div>
                <div className="bg-white p-4 rounded-lg shadow-md text-center">
                  <div className="text-3xl mb-2">🎮</div>
                  <h3 className="font-semibold text-sm">{t('workingZone.features.gaming')}</h3>
                </div>
              </div>
              <Link
                href="/working-zone"
                className="inline-block px-8 py-4 bg-gradient-primary text-white rounded-lg font-semibold hover:opacity-90 transition-opacity"
              >
                {t('workingZone.button')}
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* AI Experience Zone Section */}
      <section className="py-16 md:py-24">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold mb-4">{t('aiExperience.title')}</h2>
              <p className="text-lg text-gray-600 mb-6">{t('aiExperience.description')}</p>
              <ul className="space-y-3 mb-6 text-gray-600">
                <li className="flex items-center">
                  <span className="text-primary mr-2">✓</span>
                  AI Drawing with Stable Diffusion
                </li>
                <li className="flex items-center">
                  <span className="text-primary mr-2">✓</span>
                  AI Video Editing Tools
                </li>
                <li className="flex items-center">
                  <span className="text-primary mr-2">✓</span>
                  Photo Restoration & Enhancement
                </li>
                <li className="flex items-center">
                  <span className="text-primary mr-2">✓</span>
                  OCR & Document Processing
                </li>
              </ul>
              <Link
                href="/ai-experience"
                className="inline-block px-8 py-4 bg-gradient-primary text-white rounded-lg font-semibold hover:opacity-90 transition-opacity"
              >
                {t('aiExperience.button')}
              </Link>
            </div>
            <div className="relative rounded-lg overflow-hidden shadow-xl">
              <Image
                src="/images/equipment/coffee-machine.jpg"
                alt="Tech Coffee AI Equipment - Smart coffee machine with app control"
                width={600}
                height={800}
                className="w-full h-auto"
              />
            </div>
          </div>
        </div>
      </section>

      {/* Interior Gallery */}
      <section className="py-16 md:py-24 bg-gray-50">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold mb-4">Our Space</h2>
            <p className="text-xl text-gray-600">A unique blend of technology and comfort</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video">
              <Image
                src="/images/interior/main-hall.jpg"
                alt="Tech Coffee Main Hall - Spacious cafe with modern design"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video">
              <Image
                src="/images/interior/seating-area.jpg"
                alt="Tech Coffee Seating Area - Comfortable seating with robot dog"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video">
              <Image
                src="/images/interior/toy-display.jpg"
                alt="Tech Coffee Toy Display - Collectible figurines and neon lights"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
          </div>
        </div>
      </section>

      {/* Camera Rental Section */}
      <section className="py-16 md:py-24 bg-white">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold mb-4">{t('cameraRental.title')}</h2>
            <p className="text-lg text-gray-600 mb-8">{t('cameraRental.description')}</p>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
              <div className="bg-gray-50 p-6 rounded-lg shadow-md">
                <div className="text-4xl mb-4">📷</div>
                <h3 className="font-semibold">DJI Pocket 3</h3>
                <p className="text-gray-600 text-sm mt-2">Perfect for vlogging</p>
              </div>
              <div className="bg-gray-50 p-6 rounded-lg shadow-md">
                <div className="text-4xl mb-4">📹</div>
                <h3 className="font-semibold">GoPro Hero</h3>
                <p className="text-gray-600 text-sm mt-2">Action camera rental</p>
              </div>
              <div className="bg-gray-50 p-6 rounded-lg shadow-md">
                <div className="text-4xl mb-4">🎥</div>
                <h3 className="font-semibold">Insta360 X5</h3>
                <p className="text-gray-600 text-sm mt-2">360° video capture</p>
              </div>
            </div>
            <Link
              href="/equipment-rental"
              className="inline-block px-8 py-4 bg-gradient-primary text-white rounded-lg font-semibold hover:opacity-90 transition-opacity"
            >
              {t('cameraRental.button')}
            </Link>
          </div>
        </div>
      </section>

      {/* Whisky Bar Section */}
      <section className="py-16 md:py-24 bg-gray-900 text-white relative overflow-hidden">
        <div className="absolute inset-0 opacity-30">
          <Image
            src="/images/interior/whisky-bar-sign.jpg"
            alt="T Whisky Bar Sign"
            fill
            className="object-cover"
          />
        </div>
        <div className="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold mb-4">{t('whiskyBar.title')}</h2>
              <p className="text-lg text-gray-300 mb-6">{t('whiskyBar.description')}</p>
              <ul className="space-y-3 mb-6 text-gray-300">
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">★</span>
                  Premium Whisky Collection
                </li>
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">★</span>
                  Signature Cocktails
                </li>
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">★</span>
                  Live Music Weekends
                </li>
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">★</span>
                  4th Floor Exclusive Lounge
                </li>
              </ul>
              <Link
                href="/whisky-bar"
                className="inline-block px-8 py-4 bg-yellow-600 text-white rounded-lg font-semibold hover:bg-yellow-700 transition-colors"
              >
                {t('whiskyBar.button')}
              </Link>
            </div>
            <div className="text-center">
              <div className="bg-gradient-to-br from-yellow-900/50 to-gray-800/50 backdrop-blur-sm rounded-lg p-8">
                <div className="text-6xl mb-4">🥃</div>
                <h3 className="text-2xl font-bold mb-2">T Whisky Bar</h3>
                <p className="text-xl text-gray-300">On the 4th Floor</p>
                <p className="text-gray-400 mt-2">Open Daily: 5PM - 11PM</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Events Section */}
      <section className="py-16 md:py-24">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold mb-4">{t('events.title')}</h2>
            <p className="text-lg text-gray-600 mb-8">{t('events.description')}</p>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
              <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <div className="text-4xl mb-4">🎓</div>
                <h3 className="font-semibold mb-2">Tech Workshops</h3>
                <p className="text-gray-600 text-sm">AI tutorials and creative sessions</p>
              </div>
              <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <div className="text-4xl mb-4">🎮</div>
                <h3 className="font-semibold mb-2">Gaming Nights</h3>
                <p className="text-gray-600 text-sm">PS5 & Xbox tournaments</p>
              </div>
              <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <div className="text-4xl mb-4">🥃</div>
                <h3 className="font-semibold mb-2">Whisky Tastings</h3>
                <p className="text-gray-600 text-sm">Premium whisky experiences</p>
              </div>
            </div>
            <Link
              href="/events"
              className="inline-block px-8 py-4 bg-gradient-primary text-white rounded-lg font-semibold hover:opacity-90 transition-opacity"
            >
              {t('events.button')}
            </Link>
          </div>
        </div>
      </section>

      {/* Location & Contact */}
      <section className="py-16 md:py-24 bg-gray-50">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold mb-4">Visit Us</h2>
            <p className="text-xl text-gray-600">Located in the heart of Da Nang</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <div className="text-center">
              <div className="text-4xl mb-4">📍</div>
              <h3 className="font-semibold mb-2">Address</h3>
              <p className="text-gray-600">90 Bạch Đằng, Hải Châu<br />Đà Nẵng 550000</p>
            </div>
            <div className="text-center">
              <div className="text-4xl mb-4">⏰</div>
              <h3 className="font-semibold mb-2">Hours</h3>
              <p className="text-gray-600">Mon - Sun<br />8:00 AM - 11:00 PM</p>
            </div>
            <div className="text-center">
              <div className="text-4xl mb-4">📞</div>
              <h3 className="font-semibold mb-2">Contact</h3>
              <p className="text-gray-600">(+84) 0388 997 186<br />techcafedanang@gmail.com</p>
            </div>
          </div>
          <div className="text-center mt-8">
            <Link
              href="/contact"
              className="inline-block px-8 py-4 bg-gradient-primary text-white rounded-lg font-semibold hover:opacity-90 transition-opacity"
            >
              Get Directions
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}
