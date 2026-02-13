import Footer_1 from '@/Shared/Footer/Footer_1';
import Navbar_1 from '@/Shared/Navbar/Navbar_1';
import React, { useEffect } from 'react';

const PublicLayout = ({ children, navItems }) => {
  // Ensure the page starts at the top on navigation
  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  return (
    <div className="flex flex-col min-h-screen">
      <Navbar_1 navItems={navItems} />
      <main className="flex-grow bg-gray-50">
        {children}
      </main>
      <Footer_1 />
    </div>
  );
};

export default PublicLayout;