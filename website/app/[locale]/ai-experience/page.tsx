import { Metadata } from 'next';
import Image from 'next/image';

export const metadata: Metadata = {
  title: 'AI Experience Zone Da Nang | AI Drawing, Video Editing & Stable Diffusion',
  description: 'Experience cutting-edge AI tools at Tech Coffee Da Nang. Try AI drawing with Stable Diffusion, AI video editing, photo restoration, OCR, and more. From free trials to full-day access.',
  keywords: [
    'AI experience Da Nang',
    'AI drawing Da Nang',
    'Stable Diffusion Vietnam',
    'AI video editing Da Nang',
    'AI photo restoration',
    'OCR service Da Nang',
    'AI art generation Vietnam',
    'text to image Da Nang',
    'AI tools cafe',
    'creative AI space Vietnam'
  ],
  openGraph: {
    title: 'AI Experience Space Da Nang | AI Drawing & Video Editing',
    description: 'Try AI drawing, AI pets, video editing with AI, Stable Diffusion, and OCR tools at Tech Coffee\'s AI Space in Da Nang.',
    images: ['/images/equipment/coffee-machine.jpg'],
  },
};

export default function AIExperiencePage() {
  return (
    <div className="min-h-screen py-12 md:py-20">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        {/* Hero Section */}
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">AI Experience Zone</h1>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            Explore the future of AI-powered creativity
          </p>
          <p className="text-primary font-semibold mt-4">
            Da Nang's First AI Creative Space
          </p>
        </div>

        {/* Hero Image */}
        <section className="mb-16">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="relative rounded-lg overflow-hidden shadow-xl">
              <Image
                src="/images/equipment/coffee-machine.jpg"
                alt="Tech Coffee AI Equipment - Smart coffee machine with app control and AI features"
                width={600}
                height={800}
                className="w-full h-auto"
                priority
              />
            </div>
            <div className="relative rounded-lg overflow-hidden shadow-xl">
              <Image
                src="/images/drinks/coffee-robot.jpg"
                alt="Tech Coffee Robot Barista - AI robot serving specialty coffee with chess board"
                width={600}
                height={800}
                className="w-full h-auto"
              />
            </div>
          </div>
        </section>

        {/* AI Tools Grid */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">AI Services</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {/* AI Drawing */}
            <div className="bg-gradient-primary text-white p-6 rounded-lg shadow-lg">
              <div className="text-4xl mb-4">🎨</div>
              <h3 className="text-xl font-bold mb-2">AI Drawing</h3>
              <p className="text-gray-100 text-sm mb-4">
                Create stunning artwork using Stable Diffusion and advanced AI image generation.
              </p>
              <ul className="text-sm text-gray-100 space-y-1">
                <li>• Stable Diffusion XL</li>
                <li>• Text-to-Image Generation</li>
                <li>• Image-to-Image Transformation</li>
                <li>• Style Transfer</li>
              </ul>
            </div>

            {/* AI Talking Pets */}
            <div className="bg-gradient-secondary text-white p-6 rounded-lg shadow-lg">
              <div className="text-4xl mb-4">🐾</div>
              <h3 className="text-xl font-bold mb-2">AI Talking Pets</h3>
              <p className="text-gray-100 text-sm mb-4">
                Interact with AI-powered virtual pets that can talk and respond to you.
              </p>
              <ul className="text-sm text-gray-100 space-y-1">
                <li>• Voice Interaction</li>
                <li>• Personality AI</li>
                <li>• Real-time Response</li>
                <li>• Fun for All Ages</li>
              </ul>
            </div>

            {/* AI Video Editing */}
            <div className="bg-gradient-primary text-white p-6 rounded-lg shadow-lg">
              <div className="text-4xl mb-4">🎬</div>
              <h3 className="text-xl font-bold mb-2">AI Video Editing</h3>
              <p className="text-gray-100 text-sm mb-4">
                Edit videos with AI assistance for automatic cuts, effects, and enhancements.
              </p>
              <ul className="text-sm text-gray-100 space-y-1">
                <li>• Auto Scene Detection</li>
                <li>• Smart Cuts & Transitions</li>
                <li>• AI Color Grading</li>
                <li>• Background Removal</li>
              </ul>
            </div>

            {/* PDF/OCR */}
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200">
              <div className="text-4xl mb-4">📄</div>
              <h3 className="text-xl font-bold mb-2">PDF/OCR Service</h3>
              <p className="text-gray-600 text-sm mb-4">
                Convert images to text, scan documents, and extract information from PDFs.
              </p>
              <ul className="text-sm text-gray-500 space-y-1">
                <li>• Document Scanning</li>
                <li>• Multi-language OCR</li>
                <li>• Table Extraction</li>
                <li>• Handwriting Recognition</li>
              </ul>
            </div>

            {/* Photo Restoration */}
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200">
              <div className="text-4xl mb-4">🖼️</div>
              <h3 className="text-xl font-bold mb-2">Photo Restoration</h3>
              <p className="text-gray-600 text-sm mb-4">
                Restore old, damaged, or faded photos using AI-powered restoration tools.
              </p>
              <ul className="text-sm text-gray-500 space-y-1">
                <li>• Old Photo Repair</li>
                <li>• AI Colorization</li>
                <li>• Scratch & Damage Removal</li>
                <li>• Face Enhancement</li>
              </ul>
            </div>

            {/* AI Chat Assistant */}
            <div className="bg-white p-6 rounded-lg shadow-md border border-gray-200">
              <div className="text-4xl mb-4">💬</div>
              <h3 className="text-xl font-bold mb-2">AI Chat Assistant</h3>
              <p className="text-gray-600 text-sm mb-4">
                Get help with creative projects, coding, writing, and more from AI assistants.
              </p>
              <ul className="text-sm text-gray-500 space-y-1">
                <li>• ChatGPT Pro Access</li>
                <li>• Creative Writing Help</li>
                <li>• Code Assistance</li>
                <li>• Research Support</li>
              </ul>
            </div>
          </div>
        </section>

        {/* Sample Gallery */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">AI-Generated Samples</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-square">
              <Image
                src="/images/drinks/matcha-space.jpg"
                alt="AI Generated Art Sample - Space themed matcha drink"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
              <div className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                <p className="text-white text-sm">Space Theme</p>
              </div>
            </div>
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-square">
              <Image
                src="/images/drinks/moon-drinks.jpg"
                alt="AI Generated Art Sample - Moonlight collection"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
              <div className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                <p className="text-white text-sm">Moonlight Theme</p>
              </div>
            </div>
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-square">
              <Image
                src="/images/drinks/fruit-drinks.jpg"
                alt="AI Generated Art Sample - Tropical theme"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
              <div className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                <p className="text-white text-sm">Tropical Theme</p>
              </div>
            </div>
            <div className="relative overflow-hidden rounded-lg shadow-lg aspect-square">
              <Image
                src="/images/interior/toy-display.jpg"
                alt="AI Generated Art Sample - Neon aesthetic"
                fill
                className="object-cover hover:scale-105 transition-transform duration-300"
              />
              <div className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                <p className="text-white text-sm">Neon Aesthetic</p>
              </div>
            </div>
          </div>
        </section>

        {/* Pricing */}
        <section className="mb-16 bg-gray-50 rounded-lg p-8 md:p-12">
          <h2 className="text-3xl font-bold mb-8 text-center">Pricing</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <div className="bg-white p-6 rounded-lg shadow-md text-center border-2 border-gray-200">
              <h3 className="text-xl font-bold mb-2">Basic</h3>
              <p className="text-3xl font-bold text-primary mb-4">Free</p>
              <p className="text-gray-500 text-sm mb-4">Just order a drink</p>
              <ul className="text-sm text-gray-600 space-y-2 mb-6 text-left">
                <li className="flex items-center">
                  <span className="text-green-500 mr-2">✓</span>
                  30 min AI session
                </li>
                <li className="flex items-center">
                  <span className="text-green-500 mr-2">✓</span>
                  Basic AI tools
                </li>
                <li className="flex items-center">
                  <span className="text-green-500 mr-2">✓</span>
                  Sample outputs
                </li>
                <li className="flex items-center">
                  <span className="text-green-500 mr-2">✓</span>
                  Staff assistance
                </li>
              </ul>
              <p className="text-xs text-gray-400">Perfect for trying out</p>
            </div>
            <div className="bg-gradient-primary text-white p-6 rounded-lg shadow-lg text-center transform scale-105 border-2 border-primary">
              <div className="bg-yellow-400 text-gray-900 text-xs font-bold px-2 py-1 rounded inline-block mb-2">
                MOST POPULAR
              </div>
              <h3 className="text-xl font-bold mb-2">Premium</h3>
              <p className="text-3xl font-bold mb-4">150,000 ₫</p>
              <p className="text-gray-200 text-sm mb-4">2 hours of creativity</p>
              <ul className="text-sm space-y-2 mb-6 text-left">
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">✓</span>
                  2 hours AI session
                </li>
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">✓</span>
                  All AI tools access
                </li>
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">✓</span>
                  High-res outputs
                </li>
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">✓</span>
                  Priority support
                </li>
                <li className="flex items-center">
                  <span className="text-yellow-400 mr-2">✓</span>
                  1 free drink included
                </li>
              </ul>
              <p className="text-xs text-gray-200">Best value for creators</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md text-center border-2 border-gray-200">
              <h3 className="text-xl font-bold mb-2">Pro</h3>
              <p className="text-3xl font-bold text-primary mb-4">300,000 ₫</p>
              <p className="text-gray-500 text-sm mb-4">Full day access</p>
              <ul className="text-sm text-gray-600 space-y-2 mb-6 text-left">
                <li className="flex items-center">
                  <span className="text-green-500 mr-2">✓</span>
                  Full day access
                </li>
                <li className="flex items-center">
                  <span className="text-green-500 mr-2">✓</span>
                  All AI tools
                </li>
                <li className="flex items-center">
                  <span className="text-green-500 mr-2">✓</span>
                  Unlimited outputs
                </li>
                <li className="flex items-center">
                  <span className="text-green-500 mr-2">✓</span>
                  Expert guidance
                </li>
                <li className="flex items-center">
                  <span className="text-green-500 mr-2">✓</span>
                  2 free drinks included
                </li>
              </ul>
              <p className="text-xs text-gray-400">For serious projects</p>
            </div>
          </div>
        </section>

        {/* How It Works */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">How It Works</h2>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div className="text-center">
              <div className="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                1
              </div>
              <h3 className="font-semibold mb-2">Choose Your Package</h3>
              <p className="text-gray-600 text-sm">Select free trial or premium access</p>
            </div>
            <div className="text-center">
              <div className="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                2
              </div>
              <h3 className="font-semibold mb-2">Get Set Up</h3>
              <p className="text-gray-600 text-sm">Our staff will guide you to your station</p>
            </div>
            <div className="text-center">
              <div className="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                3
              </div>
              <h3 className="font-semibold mb-2">Create with AI</h3>
              <p className="text-gray-600 text-sm">Use our AI tools to bring your ideas to life</p>
            </div>
            <div className="text-center">
              <div className="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                4
              </div>
              <h3 className="font-semibold mb-2">Save & Share</h3>
              <p className="text-gray-600 text-sm">Download your creations or share online</p>
            </div>
          </div>
        </section>

        {/* CTA */}
        <section className="bg-gradient-primary text-white rounded-lg p-8 md:p-12 text-center">
          <h2 className="text-3xl font-bold mb-4">Ready to Experience AI?</h2>
          <p className="text-xl mb-6">Visit us today and unleash your creativity</p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <a
              href="https://maps.google.com/?q=90+Bach+Dang+Hai+Chau+Da+Nang"
              target="_blank"
              rel="noopener noreferrer"
              className="px-8 py-4 bg-white text-primary rounded-lg font-semibold hover:bg-gray-100 transition-colors"
            >
              Get Directions
            </a>
            <a
              href="tel:+84388997186"
              className="px-8 py-4 bg-transparent border-2 border-white text-white rounded-lg font-semibold hover:bg-white hover:text-primary transition-colors"
            >
              Call: (+84) 0388 997 186
            </a>
          </div>
        </section>
      </div>
    </div>
  );
}
