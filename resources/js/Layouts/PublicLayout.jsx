import Footer_1 from '@/Shared/Footer/Footer_1';
import Navbar_1 from '@/Shared/Navbar/Navbar_1';
import React from 'react';

const PublicLayout = ({ children }) => {
  return (
    <div>
      <Navbar_1 />
      <div className="min-h-screen bg-gray-50">{children}</div>
      <Footer_1 />
    </div>
  );
};

export default PublicLayout;