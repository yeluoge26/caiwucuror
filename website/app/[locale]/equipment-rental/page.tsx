import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Camera Rental Da Nang | Pocket 3, GoPro, Insta360, X5',
  description: 'Rent Pocket 3, GoPro, Insta360 X5, action cameras, accessories, and vlog gear directly from Tech Coffee.',
};

export default function EquipmentRentalPage() {
  const cameras = [
    {
      name: 'DJI Pocket 3',
      description: 'Ultra-compact 4K camera with gimbal stabilization',
      price: '200,000 VND/day',
      features: ['4K Video', 'Gimbal Stabilization', 'Compact Design'],
    },
    {
      name: 'Insta360 X5',
      description: '360-degree action camera with advanced features',
      price: '250,000 VND/day',
      features: ['360° Video', '5.7K Resolution', 'Waterproof'],
    },
    {
      name: 'GoPro Hero 12',
      description: 'Professional action camera for extreme sports',
      price: '180,000 VND/day',
      features: ['4K 60fps', 'HyperSmooth', 'Waterproof'],
    },
    {
      name: 'DJI Action',
      description: 'High-performance action camera with RockSteady',
      price: '170,000 VND/day',
      features: ['4K Video', 'RockSteady EIS', 'Long Battery'],
    },
  ];

  const accessories = [
    { name: 'SD Card (128GB)', price: '50,000 VND/day' },
    { name: 'Extra Battery', price: '30,000 VND/day' },
    { name: 'Microphone', price: '40,000 VND/day' },
    { name: 'Tripod', price: '35,000 VND/day' },
    { name: 'ND Filter Set', price: '45,000 VND/day' },
  ];

  return (
    <div className="min-h-screen py-12 md:py-20">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        {/* Hero Section */}
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">Equipment Rental</h1>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            Rent professional cameras and accessories for your creative projects
          </p>
        </div>

        {/* Camera List */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">Cameras</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {cameras.map((camera, index) => (
              <div key={index} className="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <div className="h-48 bg-gray-100 rounded mb-4 flex items-center justify-center">
                  <span className="text-gray-400">{camera.name}</span>
                </div>
                <h3 className="text-xl font-bold mb-2">{camera.name}</h3>
                <p className="text-gray-600 text-sm mb-4">{camera.description}</p>
                <div className="mb-4">
                  <ul className="text-sm text-gray-500 space-y-1">
                    {camera.features.map((feature, i) => (
                      <li key={i}>✓ {feature}</li>
                    ))}
                  </ul>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-primary font-bold text-lg">{camera.price}</span>
                  <button className="px-6 py-2 bg-primary text-white rounded-lg hover:opacity-90 transition-opacity">
                    Rent Now
                  </button>
                </div>
              </div>
            ))}
          </div>
        </section>

        {/* Accessories */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8 text-center">Accessories</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {accessories.map((item, index) => (
              <div key={index} className="bg-white p-4 rounded-lg shadow-md border border-gray-200 flex justify-between items-center">
                <div>
                  <h3 className="font-semibold">{item.name}</h3>
                  <p className="text-primary font-bold text-sm">{item.price}</p>
                </div>
                <button className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                  Add
                </button>
              </div>
            ))}
          </div>
        </section>

        {/* Rental Rules */}
        <section className="bg-gray-50 rounded-lg p-8 md:p-12">
          <h2 className="text-3xl font-bold mb-6">Rental Policy</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
              <h3 className="text-xl font-semibold mb-4">Terms & Conditions</h3>
              <ul className="space-y-2 text-gray-600">
                <li className="flex items-start">
                  <span className="text-primary mr-2">•</span>
                  <span>Deposit required: 50% of rental value</span>
                </li>
                <li className="flex items-start">
                  <span className="text-primary mr-2">•</span>
                  <span>Rental period: Minimum 1 day</span>
                </li>
                <li className="flex items-start">
                  <span className="text-primary mr-2">•</span>
                  <span>Late return fee: 20% per day</span>
                </li>
                <li className="flex items-start">
                  <span className="text-primary mr-2">•</span>
                  <span>Damage deposit: Full replacement value</span>
                </li>
              </ul>
            </div>
            <div>
              <h3 className="text-xl font-semibold mb-4">How to Rent</h3>
              <ol className="space-y-2 text-gray-600">
                <li className="flex items-start">
                  <span className="text-primary mr-2">1.</span>
                  <span>Browse available equipment</span>
                </li>
                <li className="flex items-start">
                  <span className="text-primary mr-2">2.</span>
                  <span>Select rental period</span>
                </li>
                <li className="flex items-start">
                  <span className="text-primary mr-2">3.</span>
                  <span>Complete reservation form</span>
                </li>
                <li className="flex items-start">
                  <span className="text-primary mr-2">4.</span>
                  <span>Pay deposit and pick up equipment</span>
                </li>
              </ol>
            </div>
          </div>
        </section>
      </div>
    </div>
  );
}



