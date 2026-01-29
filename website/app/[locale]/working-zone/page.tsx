import { Metadata } from 'next';
import Image from 'next/image';

export const metadata: Metadata = {
  title: 'Free Working Zone Da Nang | High-Speed WiFi, PS5, 5080 GPU, ChatGPT Pro',
  description: 'Enjoy Da Nang\'s only completely free co-working space with ChatGPT Pro, Final Cut Pro, Adobe CC, NVIDIA 5080 GPU workstation, PS5, Xbox, and 100-inch TV. No fees, just order a drink!',
  keywords: [
    'free working space Da Nang',
    'free coworking Da Nang',
    'free WiFi cafe Da Nang',
    'ChatGPT Pro free access Vietnam',
    'Adobe Creative Cloud cafe',
    'Final Cut Pro cafe',
    'PS5 gaming cafe Da Nang',
    'Xbox gaming Da Nang',
    'GPU workstation Da Nang',
    'digital nomad Da Nang',
    'remote work Da Nang',
    'free workspace Vietnam'
  ],
  openGraph: {
    title: 'Free Working Zone Da Nang | ChatGPT Pro, Adobe, PS5, GPU',
    description: 'Da Nang\'s only completely free co-working space with premium tools and gaming.',
    images: ['/images/interior/neon-lounge.jpg'],
  },
};

export default function WorkingZonePage() {
  return (
    <div className="min-h-screen py-12 md:py-20">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        {/* Hero Section */}
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">Free Working Zone</h1>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            Da Nang's Only Completely Free AI & Creator Space
          </p>
          <p className="text-primary font-semibold mt-4">
            No fees • Just order a drink • Unlimited access
          </p>
        </div>

        {/* Hero Image */}
        <section className="mb-16">
          <div className="relative rounded-lg overflow-hidden shadow-xl">
            <Image
              src="/images/interior/neon-lounge.jpg"
              alt="Tech Coffee Free Working Zone - Modern co-working space with neon lights and comfortable seating"
              width={1200}
              height={600}
              className="w-full h-auto"
              priority
            />
          </div>
        </section>

        {/* Facilities Grid */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">Free Facilities</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {/* ChatGPT Pro Station */}
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
              <div className="text-4xl mb-4">🤖</div>
              <h3 className="text-xl font-bold mb-2">ChatGPT Pro Station</h3>
              <p className="text-gray-600 text-sm mb-4">
                Access to GPT-4, GPT-4 Vision, voice, and multimodal features. Completely free.
              </p>
              <ul className="text-sm text-gray-500 space-y-1">
                <li>✓ GPT-4 & GPT-4 Vision Access</li>
                <li>✓ Voice Conversations</li>
                <li>✓ Multimodal AI Features</li>
                <li>✓ Code Interpreter</li>
              </ul>
              <div className="mt-4 text-primary font-bold">FREE</div>
            </div>

            {/* Adobe Workstation */}
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
              <div className="text-4xl mb-4">🎨</div>
              <h3 className="text-xl font-bold mb-2">Adobe Creative Cloud</h3>
              <p className="text-gray-600 text-sm mb-4">
                Full Adobe Creative Cloud suite. Photoshop, Illustrator, Premiere, After Effects, and more.
              </p>
              <ul className="text-sm text-gray-500 space-y-1">
                <li>✓ Photoshop CC</li>
                <li>✓ Premiere Pro</li>
                <li>✓ After Effects</li>
                <li>✓ Illustrator & InDesign</li>
              </ul>
              <div className="mt-4 text-primary font-bold">FREE</div>
            </div>

            {/* 5080 GPU Rig */}
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
              <div className="text-4xl mb-4">🖥️</div>
              <h3 className="text-xl font-bold mb-2">NVIDIA 5080 GPU Workstation</h3>
              <p className="text-gray-600 text-sm mb-4">
                High-performance AI workstation for video editing, AI image generation, and model inference.
              </p>
              <ul className="text-sm text-gray-500 space-y-1">
                <li>✓ NVIDIA RTX 5080 GPU</li>
                <li>✓ 4K/8K Video Editing</li>
                <li>✓ AI Image Generation</li>
                <li>✓ Machine Learning Ready</li>
              </ul>
              <div className="mt-4 text-primary font-bold">FREE</div>
            </div>

            {/* PS5 / Xbox */}
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
              <div className="text-4xl mb-4">🎮</div>
              <h3 className="text-xl font-bold mb-2">Gaming Zone</h3>
              <p className="text-gray-600 text-sm mb-4">
                PS5, Xbox Series X, and Nintendo Switch 2. Free gaming for all visitors.
              </p>
              <ul className="text-sm text-gray-500 space-y-1">
                <li>✓ PlayStation 5</li>
                <li>✓ Xbox Series X</li>
                <li>✓ Nintendo Switch 2</li>
                <li>✓ Latest Game Library</li>
              </ul>
              <div className="mt-4 text-primary font-bold">FREE</div>
            </div>

            {/* 100-inch TV */}
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
              <div className="text-4xl mb-4">📺</div>
              <h3 className="text-xl font-bold mb-2">100-inch TV Workspace</h3>
              <p className="text-gray-600 text-sm mb-4">
                Perfect for presentations, team collaboration, and media viewing.
              </p>
              <ul className="text-sm text-gray-500 space-y-1">
                <li>✓ 100" 4K Display</li>
                <li>✓ Wireless Screen Sharing</li>
                <li>✓ Team Presentations</li>
                <li>✓ Movie Screenings</li>
              </ul>
              <div className="mt-4 text-primary font-bold">FREE</div>
            </div>

            {/* Final Cut Pro */}
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
              <div className="text-4xl mb-4">✂️</div>
              <h3 className="text-xl font-bold mb-2">Final Cut Pro</h3>
              <p className="text-gray-600 text-sm mb-4">
                Professional video editing software on Mac workstations. Free for all users.
              </p>
              <ul className="text-sm text-gray-500 space-y-1">
                <li>✓ Professional Editing</li>
                <li>✓ 4K/8K Support</li>
                <li>✓ Motion Graphics</li>
                <li>✓ Color Grading</li>
              </ul>
              <div className="mt-4 text-primary font-bold">FREE</div>
            </div>
          </div>
        </section>

        {/* Interior Gallery */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">Our Space</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video">
              <Image
                src="/images/interior/main-hall.jpg"
                alt="Tech Coffee Main Hall - Spacious cafe with modern design and spiral staircase"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video">
              <Image
                src="/images/interior/seating-area.jpg"
                alt="Tech Coffee Seating Area - Comfortable seating with robot dog and coffee art"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video">
              <Image
                src="/images/interior/toy-display.jpg"
                alt="Tech Coffee Toy Display - Collectible figurines with neon lighting"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video">
              <Image
                src="/images/interior/table-detail.jpg"
                alt="Tech Coffee Table Detail - Cozy seating with flowers and figurines"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video md:col-span-2">
              <Image
                src="/images/interior/neon-lounge.jpg"
                alt="Tech Coffee Neon Lounge - Blue neon lit space with modern furniture"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
          </div>
        </section>

        {/* How It Works */}
        <section className="mb-16 bg-gray-50 rounded-lg p-8 md:p-12">
          <h2 className="text-3xl font-bold mb-8 text-center">How It Works</h2>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div className="text-center">
              <div className="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                1
              </div>
              <h3 className="text-xl font-semibold mb-2">Visit Us</h3>
              <p className="text-gray-600">Come to 90 Bạch Đằng, Hải Châu, Đà Nẵng</p>
            </div>
            <div className="text-center">
              <div className="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                2
              </div>
              <h3 className="text-xl font-semibold mb-2">Order a Drink</h3>
              <p className="text-gray-600">Choose from our menu starting at 30,000 VND</p>
            </div>
            <div className="text-center">
              <div className="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                3
              </div>
              <h3 className="text-xl font-semibold mb-2">Choose Your Station</h3>
              <p className="text-gray-600">Select the workstation or area you want to use</p>
            </div>
            <div className="text-center">
              <div className="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                4
              </div>
              <h3 className="text-xl font-semibold mb-2">Start Creating</h3>
              <p className="text-gray-600">Enjoy unlimited free access to all tools</p>
            </div>
          </div>
        </section>

        {/* WiFi Info */}
        <section className="mb-16 bg-gradient-primary text-white rounded-lg p-8 text-center">
          <h2 className="text-3xl font-bold mb-4">Free High-Speed WiFi</h2>
          <p className="text-xl mb-4">Network: <strong>TECH COFFEE</strong></p>
          <p className="text-4xl font-bold">Password: 88888888</p>
          <p className="text-gray-200 mt-4">Fiber optic connection • Fast & reliable</p>
        </section>

        {/* Rules */}
        <section className="bg-white rounded-lg p-8 border border-gray-200">
          <h2 className="text-3xl font-bold mb-6">Rules & Guidelines</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <ul className="space-y-3 text-gray-600">
              <li className="flex items-start">
                <span className="text-primary mr-2">✓</span>
                <span>All facilities are free to use during business hours (8AM - 11PM)</span>
              </li>
              <li className="flex items-start">
                <span className="text-primary mr-2">✓</span>
                <span>Please be respectful of other users and maintain a quiet environment</span>
              </li>
              <li className="flex items-start">
                <span className="text-primary mr-2">✓</span>
                <span>Food and drinks from our menu are allowed at workstations</span>
              </li>
            </ul>
            <ul className="space-y-3 text-gray-600">
              <li className="flex items-start">
                <span className="text-primary mr-2">✓</span>
                <span>Equipment must be used responsibly and returned to original state</span>
              </li>
              <li className="flex items-start">
                <span className="text-primary mr-2">✓</span>
                <span>Reservations recommended for peak hours (weekends)</span>
              </li>
              <li className="flex items-start">
                <span className="text-primary mr-2">✓</span>
                <span>Staff available to assist with any technical questions</span>
              </li>
            </ul>
          </div>
        </section>

        {/* CTA */}
        <section className="mt-12 text-center">
          <h2 className="text-2xl font-bold mb-4">Ready to Create?</h2>
          <p className="text-gray-600 mb-6">Visit us today and experience Da Nang's best free working space</p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <a
              href="https://maps.google.com/?q=90+Bach+Dang+Hai+Chau+Da+Nang"
              target="_blank"
              rel="noopener noreferrer"
              className="px-8 py-4 bg-gradient-primary text-white rounded-lg font-semibold hover:opacity-90 transition-opacity"
            >
              Get Directions
            </a>
            <a
              href="tel:+84388997186"
              className="px-8 py-4 bg-gray-100 text-gray-800 rounded-lg font-semibold hover:bg-gray-200 transition-colors"
            >
              Call Us: (+84) 0388 997 186
            </a>
          </div>
        </section>
      </div>
    </div>
  );
}
