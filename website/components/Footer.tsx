'use client';

import { useTranslations } from 'next-intl';
import Link from 'next/link';

export default function Footer() {
  const t = useTranslations('footer');
  const tContact = useTranslations('contact');

  return (
    <footer className="bg-gray-900 text-white mt-20">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {/* About */}
          <div>
            <h3 className="text-lg font-bold mb-4">{t('title')}</h3>
            <p className="text-gray-400 text-sm">
              Da Nang's first AI-powered creative space offering free working areas, 
              AI experience, camera rental, and premium whisky bar.
            </p>
          </div>

          {/* Quick Links */}
          <div>
            <h3 className="text-lg font-bold mb-4">Quick Links</h3>
            <ul className="space-y-2 text-sm">
              <li>
                <Link href="/about" className="text-gray-400 hover:text-white transition-colors">
                  About Us
                </Link>
              </li>
              <li>
                <Link href="/working-zone" className="text-gray-400 hover:text-white transition-colors">
                  Free Working Zone
                </Link>
              </li>
              <li>
                <Link href="/ai-experience" className="text-gray-400 hover:text-white transition-colors">
                  AI Experience
                </Link>
              </li>
              <li>
                <Link href="/menu" className="text-gray-400 hover:text-white transition-colors">
                  Menu
                </Link>
              </li>
            </ul>
          </div>

          {/* Contact Info */}
          <div>
            <h3 className="text-lg font-bold mb-4">{tContact('title')}</h3>
            <ul className="space-y-2 text-sm text-gray-400">
              <li>
                <span className="font-medium text-white">{t('address')}:</span>
                <br />
                {tContact('address')}
              </li>
              <li>
                <span className="font-medium text-white">{t('phone')}:</span>{' '}
                <a href={`tel:${tContact('phone')}`} className="hover:text-white transition-colors">
                  {tContact('phone')}
                </a>
              </li>
              <li>
                <span className="font-medium text-white">{t('email')}:</span>{' '}
                <a href={`mailto:${tContact('email')}`} className="hover:text-white transition-colors">
                  {tContact('email')}
                </a>
              </li>
              <li>
                <span className="font-medium text-white">{t('hours')}:</span>
                <br />
                {t('hoursValue')}
              </li>
            </ul>
          </div>

          {/* Social Media */}
          <div>
            <h3 className="text-lg font-bold mb-4">Follow Us</h3>
            <div className="flex space-x-4 mb-4">
              <a
                href="https://www.facebook.com/profile.php?id=61580547364249"
                target="_blank"
                rel="noopener noreferrer"
                className="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors"
                aria-label="Facebook"
              >
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                </svg>
              </a>
              <a
                href="https://www.tiktok.com/@techcafedanang"
                target="_blank"
                rel="noopener noreferrer"
                className="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors"
                aria-label="TikTok"
              >
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                </svg>
              </a>
              <a
                href="https://maps.google.com/?q=90+Bach+Dang+Hai+Chau+Da+Nang"
                target="_blank"
                rel="noopener noreferrer"
                className="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors"
                aria-label="Google Maps"
              >
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
              </a>
            </div>
            <p className="text-gray-400 text-xs">
              WiFi: TECH COFFEE<br />
              Password: 88888888
            </p>
          </div>
        </div>

        <div className="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
          <p>{t('copyright')}</p>
        </div>
      </div>
    </footer>
  );
}

