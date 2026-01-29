import { Metadata } from 'next';
import Link from 'next/link';

export const metadata: Metadata = {
  title: 'Tech Coffee Blog | AI, Coffee, Creative Space & Renovation Stories',
  description: 'Read the latest updates, renovation diaries, AI tutorials, and coffee knowledge from Tech Coffee.',
};

export default function BlogPage() {
  const posts = [
    {
      id: 1,
      title: 'Opening Day: Our Journey Begins',
      date: '2025-01-15',
      category: 'News',
      excerpt: 'The story of how Tech Coffee came to life in Da Nang...',
    },
    {
      id: 2,
      title: 'AI Tools for Creators: A Complete Guide',
      date: '2025-01-20',
      category: 'Tutorial',
      excerpt: 'Learn how to leverage AI tools for your creative projects...',
    },
    {
      id: 3,
      title: 'Renovation Diary: Building the Perfect Space',
      date: '2025-01-25',
      category: 'Story',
      excerpt: 'Behind the scenes of creating our unique creative space...',
    },
  ];

  return (
    <div className="min-h-screen py-12 md:py-20">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        {/* Hero Section */}
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">Blog / News</h1>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            Stay updated with our latest stories, tutorials, and news
          </p>
        </div>

        {/* Blog List */}
        <section className="mb-16">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {posts.map((post) => (
              <Link
                key={post.id}
                href={`/blog/${post.id}`}
                className="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow"
              >
                <div className="h-48 bg-gray-100 rounded mb-4 flex items-center justify-center">
                  <span className="text-gray-400">Image</span>
                </div>
                <span className="inline-block px-3 py-1 bg-primary text-white text-xs font-semibold rounded-full mb-2">
                  {post.category}
                </span>
                <h3 className="text-xl font-bold mb-2">{post.title}</h3>
                <p className="text-gray-600 text-sm mb-4">{post.excerpt}</p>
                <p className="text-gray-400 text-xs">{post.date}</p>
              </Link>
            ))}
          </div>
        </section>

        {/* Categories */}
        <section className="bg-gray-50 rounded-lg p-8">
          <h2 className="text-2xl font-bold mb-6 text-center">Categories</h2>
          <div className="flex flex-wrap justify-center gap-4">
            {['All', 'News', 'Tutorial', 'Story', 'AI', 'Coffee'].map((category) => (
              <button
                key={category}
                className="px-6 py-2 bg-white border border-gray-300 rounded-lg hover:bg-primary hover:text-white transition-colors"
              >
                {category}
              </button>
            ))}
          </div>
        </section>
      </div>
    </div>
  );
}



