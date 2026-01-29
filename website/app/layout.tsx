import './globals.css';
import { Metadata } from 'next';

export const metadata: Metadata = {
  metadataBase: new URL('https://techcafedanang.com'),
  title: {
    default: 'Tech Coffee Da Nang | AI Experience, Free Working Space & Whisky Bar',
    template: '%s | Tech Coffee Da Nang'
  },
  description: 'Tech Coffee is Da Nang\'s first AI-powered creative space offering free working areas, ChatGPT Pro, Adobe Suite, PS5, 5080 GPU workstation, camera rental, and T Whisky Bar on the 4th floor.',
  keywords: [
    'Tech Coffee Da Nang',
    'AI Coffee Shop Da Nang',
    'Free Working Space Da Nang',
    'Co-working Cafe Da Nang',
    'AI Experience Space Vietnam',
    'Whisky Bar Da Nang',
    'Tech AI Space',
    'Da Nang Coffee Shop',
    'Creative Space Da Nang',
    'Camera Rental Da Nang',
    'ChatGPT Pro Vietnam',
    'Adobe Creative Cloud Cafe',
    'PS5 Gaming Cafe Da Nang',
    'Free WiFi Cafe Da Nang',
    'Digital Nomad Da Nang'
  ],
  authors: [{ name: 'Tech Coffee', url: 'https://techcafedanang.com' }],
  creator: 'Tech Coffee',
  publisher: 'Tech Coffee',
  formatDetection: {
    email: true,
    address: true,
    telephone: true,
  },
  openGraph: {
    type: 'website',
    locale: 'vi_VN',
    alternateLocale: ['en_US', 'ko_KR'],
    url: 'https://techcafedanang.com',
    siteName: 'Tech Coffee Da Nang',
    title: 'Tech Coffee Da Nang — AI Experience & Free Working Space',
    description: 'Da Nang\'s first AI-powered coffee space with free working zone, camera rental, PS5 gaming, and a whisky bar.',
    images: [
      {
        url: '/images/interior/main-hall.jpg',
        width: 1200,
        height: 630,
        alt: 'Tech Coffee Da Nang - AI Experience & Creative Space',
      },
    ],
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Tech Coffee Da Nang | AI Experience & Free Working Space',
    description: 'Da Nang\'s first AI-powered coffee space with free working zone, camera rental, PS5 gaming, and a whisky bar.',
    images: ['/images/interior/main-hall.jpg'],
  },
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      'max-video-preview': -1,
      'max-image-preview': 'large',
      'max-snippet': -1,
    },
  },
  verification: {
    google: 'your-google-verification-code',
  },
  alternates: {
    canonical: 'https://techcafedanang.com',
    languages: {
      'en': 'https://techcafedanang.com/en',
      'vi': 'https://techcafedanang.com/vi',
      'ko': 'https://techcafedanang.com/ko',
    },
  },
  category: 'food & drink',
};

// JSON-LD Structured Data
const jsonLd = {
  '@context': 'https://schema.org',
  '@type': 'CafeOrCoffeeShop',
  name: 'Tech Coffee Da Nang',
  alternateName: 'Tech Coffee',
  description: 'Da Nang\'s first AI-powered creative space offering free working areas, ChatGPT Pro, Adobe Suite, PS5, GPU workstation, camera rental, and T Whisky Bar.',
  url: 'https://techcafedanang.com',
  telephone: '+84388997186',
  email: 'techcafedanang@gmail.com',
  address: {
    '@type': 'PostalAddress',
    streetAddress: '90 Bạch Đằng',
    addressLocality: 'Hải Châu',
    addressRegion: 'Đà Nẵng',
    postalCode: '550000',
    addressCountry: 'VN',
  },
  geo: {
    '@type': 'GeoCoordinates',
    latitude: 16.0544,
    longitude: 108.2022,
  },
  openingHoursSpecification: [
    {
      '@type': 'OpeningHoursSpecification',
      dayOfWeek: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
      opens: '08:00',
      closes: '23:00',
    },
  ],
  servesCuisine: ['Coffee', 'Tea', 'Cocktails', 'Whisky'],
  priceRange: '$$',
  image: [
    'https://techcafedanang.com/images/interior/main-hall.jpg',
    'https://techcafedanang.com/images/drinks/fruit-drinks.jpg',
    'https://techcafedanang.com/images/interior/neon-lounge.jpg',
  ],
  amenityFeature: [
    { '@type': 'LocationFeatureSpecification', name: 'Free WiFi', value: true },
    { '@type': 'LocationFeatureSpecification', name: 'Free Working Space', value: true },
    { '@type': 'LocationFeatureSpecification', name: 'ChatGPT Pro Access', value: true },
    { '@type': 'LocationFeatureSpecification', name: 'Adobe Creative Cloud', value: true },
    { '@type': 'LocationFeatureSpecification', name: 'PS5 Gaming', value: true },
    { '@type': 'LocationFeatureSpecification', name: 'Camera Rental', value: true },
    { '@type': 'LocationFeatureSpecification', name: 'Whisky Bar', value: true },
  ],
  sameAs: [
    'https://www.facebook.com/profile.php?id=61580547364249',
    'https://www.tiktok.com/@techcafedanang',
  ],
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html>
      <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="address" content="90 Bạch Đằng, Hải Châu, Đà Nẵng 550000, Vietnam" />
        <meta name="phone" content="(+84) 0388 997 186" />
        <meta name="email" content="techcafedanang@gmail.com" />
        <meta name="geo.region" content="VN-DN" />
        <meta name="geo.placename" content="Da Nang" />
        <meta name="geo.position" content="16.0544;108.2022" />
        <meta name="ICBM" content="16.0544, 108.2022" />
        <link rel="icon" href="/favicon.ico" sizes="any" />
        <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
        <link rel="manifest" href="/manifest.json" />
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
        />
      </head>
      <body className="min-h-screen bg-white">
        {children}
      </body>
    </html>
  );
}
