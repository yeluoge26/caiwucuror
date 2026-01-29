import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Events at Tech Coffee | Workshops, Gaming Nights & Whisky Tastings',
  description: 'Join tech workshops, gaming events, whisky tastings, and AI seminars at Tech Coffee Da Nang.',
};

export default function EventsPage() {
  const events = [
    {
      type: 'Workshop',
      title: 'AI Tools Workshop',
      date: '2025-02-15',
      time: '2:00 PM - 4:00 PM',
      description: 'Learn how to use AI tools for creative projects',
      icon: '🎓',
    },
    {
      type: 'Gaming',
      title: 'Gaming Night Tournament',
      date: '2025-02-20',
      time: '7:00 PM - 11:00 PM',
      description: 'Competitive gaming tournament with prizes',
      icon: '🎮',
    },
    {
      type: 'Whisky',
      title: 'Whisky Tasting Event',
      date: '2025-02-25',
      time: '6:00 PM - 9:00 PM',
      description: 'Sample premium whiskies with expert guidance',
      icon: '🥃',
    },
    {
      type: 'Tech',
      title: 'AI Seminar',
      date: '2025-03-01',
      time: '3:00 PM - 5:00 PM',
      description: 'Expert talks on AI and creative technology',
      icon: '🤖',
    },
  ];

  return (
    <div className="min-h-screen py-12 md:py-20">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        {/* Hero Section */}
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">Events & Community</h1>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            Join our vibrant community of creators, tech enthusiasts, and innovators
          </p>
        </div>

        {/* Events List */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold mb-8">Upcoming Events</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {events.map((event, index) => (
              <div key={index} className="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
                <div className="flex items-start justify-between mb-4">
                  <div className="flex items-center space-x-3">
                    <div className="text-4xl">{event.icon}</div>
                    <div>
                      <span className="inline-block px-3 py-1 bg-primary text-white text-xs font-semibold rounded-full mb-2">
                        {event.type}
                      </span>
                      <h3 className="text-xl font-bold">{event.title}</h3>
                    </div>
                  </div>
                </div>
                <div className="mb-4">
                  <p className="text-gray-600 text-sm mb-2">
                    <span className="font-semibold">Date:</span> {event.date}
                  </p>
                  <p className="text-gray-600 text-sm mb-2">
                    <span className="font-semibold">Time:</span> {event.time}
                  </p>
                  <p className="text-gray-600">{event.description}</p>
                </div>
                <button className="w-full px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:opacity-90 transition-opacity">
                  Register Now
                </button>
              </div>
            ))}
          </div>
        </section>

        {/* Event Categories */}
        <section className="bg-gray-50 rounded-lg p-8 md:p-12">
          <h2 className="text-3xl font-bold mb-8 text-center">Event Categories</h2>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            {[
              { name: 'Workshops', icon: '🎓', count: '12' },
              { name: 'Gaming Nights', icon: '🎮', count: '8' },
              { name: 'Tech Seminars', icon: '🤖', count: '6' },
              { name: 'Whisky Tastings', icon: '🥃', count: '4' },
            ].map((category, index) => (
              <div key={index} className="bg-white p-6 rounded-lg shadow-md text-center">
                <div className="text-5xl mb-4">{category.icon}</div>
                <h3 className="text-xl font-bold mb-2">{category.name}</h3>
                <p className="text-gray-600">{category.count} events</p>
              </div>
            ))}
          </div>
        </section>
      </div>
    </div>
  );
}



