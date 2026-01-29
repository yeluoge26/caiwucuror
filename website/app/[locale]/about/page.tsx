import { Metadata } from 'next';
import Image from 'next/image';

export const metadata: Metadata = {
  title: 'About Tech Coffee Da Nang | Tech AI Space & Creative Hub',
  description: 'Learn about Tech Coffee\'s vision as Da Nang\'s leading AI experience hub, free workspace, and premium whisky bar. Our mission is to democratize access to advanced technology.',
  keywords: [
    'about Tech Coffee',
    'Tech Coffee Da Nang story',
    'AI cafe Vietnam',
    'creative space Da Nang',
    'tech hub Vietnam',
    'innovation cafe',
    'digital nomad space',
    'coworking Da Nang'
  ],
  openGraph: {
    title: 'About Tech Coffee Da Nang | Tech AI Space & Creative Hub',
    description: 'Learn about Tech Coffee\'s vision as Da Nang\'s leading AI experience hub.',
    images: ['/images/interior/main-hall.jpg'],
  },
};

export default function AboutPage() {
  return (
    <div className="min-h-screen py-12 md:py-20">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        {/* Hero Section */}
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">About Tech Coffee</h1>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            Da Nang's First AI-Powered Creative Space
          </p>
        </div>

        {/* Hero Image */}
        <section className="mb-16">
          <div className="relative rounded-lg overflow-hidden shadow-xl">
            <Image
              src="/images/interior/main-hall.jpg"
              alt="Tech Coffee Main Hall - Modern cafe with spiral staircase and tech-themed decor"
              width={1200}
              height={600}
              className="w-full h-auto"
              priority
            />
          </div>
        </section>

        {/* Our Story */}
        <section className="mb-16">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl font-bold mb-4">Our Story</h2>
              <p className="text-gray-600 mb-4">
                Tech Coffee was born from a simple idea: to create a unique space where technology, 
                creativity, and community converge. Located in the heart of Da Nang at 90 Bạch Đằng, 
                we offer a completely free working zone equipped with cutting-edge AI tools, 
                professional software, and premium hardware.
              </p>
              <p className="text-gray-600 mb-4">
                Our mission is to democratize access to advanced technology, making professional-grade 
                tools available to creators, students, and digital nomads without barriers. Whether 
                you're a content creator needing Adobe Suite, a developer wanting ChatGPT Pro access, 
                or a gamer looking for PS5 fun, Tech Coffee has you covered.
              </p>
              <p className="text-gray-600">
                Beyond technology, we're passionate about great coffee, innovative drinks, and 
                creating memorable experiences. Our T Whisky Bar on the 4th floor offers a 
                sophisticated evening escape with premium whiskies and signature cocktails.
              </p>
            </div>
            <div className="relative rounded-lg overflow-hidden shadow-xl">
              <Image
                src="/images/interior/seating-area.jpg"
                alt="Tech Coffee Seating Area - Comfortable seating with robot dog and coffee art wall"
                width={600}
                height={400}
                className="w-full h-auto"
              />
            </div>
          </div>
        </section>

        {/* Vision & Mission */}
        <section className="mb-16 bg-gray-50 rounded-lg p-8 md:p-12">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div className="text-center p-6">
              <div className="text-5xl mb-4">🎯</div>
              <h3 className="text-xl font-bold mb-2">Vision</h3>
              <p className="text-gray-600">
                To be Vietnam's leading creative technology hub, inspiring innovation and 
                fostering a community of creators, developers, and dreamers.
              </p>
            </div>
            <div className="text-center p-6">
              <div className="text-5xl mb-4">🚀</div>
              <h3 className="text-xl font-bold mb-2">Mission</h3>
              <p className="text-gray-600">
                To democratize access to advanced technology and create a welcoming space 
                where anyone can learn, create, and connect.
              </p>
            </div>
            <div className="text-center p-6">
              <div className="text-5xl mb-4">💡</div>
              <h3 className="text-xl font-bold mb-2">Values</h3>
              <p className="text-gray-600">
                Innovation, accessibility, community, quality, and the belief that 
                technology should empower everyone.
              </p>
            </div>
          </div>
        </section>

        {/* What We Offer */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">What We Offer</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200 text-center">
              <div className="text-4xl mb-4">☕</div>
              <h3 className="font-bold mb-2">Quality Drinks</h3>
              <p className="text-gray-600 text-sm">
                Specialty coffee, milk tea, matcha, and signature drinks crafted with passion
              </p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200 text-center">
              <div className="text-4xl mb-4">🖥️</div>
              <h3 className="font-bold mb-2">Free Tech Access</h3>
              <p className="text-gray-600 text-sm">
                ChatGPT Pro, Adobe CC, Final Cut Pro, GPU workstations - all free
              </p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200 text-center">
              <div className="text-4xl mb-4">🤖</div>
              <h3 className="font-bold mb-2">AI Experience</h3>
              <p className="text-gray-600 text-sm">
                AI drawing, video editing, photo restoration, and more
              </p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200 text-center">
              <div className="text-4xl mb-4">🥃</div>
              <h3 className="font-bold mb-2">T Whisky Bar</h3>
              <p className="text-gray-600 text-sm">
                Premium whisky and signature cocktails on the 4th floor
              </p>
            </div>
          </div>
        </section>

        {/* Gallery */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">Our Space</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video">
              <Image
                src="/images/interior/neon-lounge.jpg"
                alt="Tech Coffee Neon Lounge - Blue neon lit space"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video">
              <Image
                src="/images/interior/toy-display.jpg"
                alt="Tech Coffee Toy Display - Collectibles and neon lights"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-video">
              <Image
                src="/images/interior/table-detail.jpg"
                alt="Tech Coffee Table Detail - Cozy seating with flowers"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
            </div>
          </div>
        </section>

        {/* Location & Hours */}
        <section className="bg-gray-50 rounded-lg p-8 md:p-12">
          <h2 className="text-3xl font-bold mb-6 text-center">Visit Us</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div>
              <div className="text-4xl mb-4">📍</div>
              <h3 className="text-xl font-semibold mb-2">Location</h3>
              <p className="text-gray-600 mb-2">
                90 Bạch Đằng, Hải Châu<br />Đà Nẵng 550000, Vietnam
              </p>
              <a
                href="https://maps.google.com/?q=90+Bach+Dang+Hai+Chau+Da+Nang"
                target="_blank"
                rel="noopener noreferrer"
                className="text-primary hover:underline"
              >
                View on Google Maps →
              </a>
            </div>
            <div>
              <div className="text-4xl mb-4">⏰</div>
              <h3 className="text-xl font-semibold mb-2">Business Hours</h3>
              <p className="text-gray-600">Mon–Sun<br />8:00 AM – 11:00 PM</p>
            </div>
            <div>
              <div className="text-4xl mb-4">📞</div>
              <h3 className="text-xl font-semibold mb-2">Contact</h3>
              <p className="text-gray-600">
                <a href="tel:+84388997186" className="hover:text-primary">(+84) 0388 997 186</a><br />
                <a href="mailto:techcafedanang@gmail.com" className="hover:text-primary">techcafedanang@gmail.com</a>
              </p>
            </div>
          </div>
        </section>
      </div>
    </div>
  );
}



