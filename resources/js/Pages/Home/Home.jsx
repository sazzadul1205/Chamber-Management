import PublicLayout from "@/Layouts/PublicLayout";
import { Head } from "@inertiajs/react";
import { Suspense, lazy, useEffect, useState } from 'react';

// Regular Section Imports
const TopSlider_1 = lazy(() => import("./TopSlider/TopSlider_1"));
const TopSlider_2 = lazy(() => import("./TopSlider/TopSlider_2"));
const TopSlider_3 = lazy(() => import("./TopSlider/TopSlider_3"));
const AboutSection_1 = lazy(() => import("./AboutSection/AboutSection_1"));
const AboutSection_2 = lazy(() => import("./AboutSection/AboutSection_2"));
const AboutSection_3 = lazy(() => import("./AboutSection/AboutSection_3"));
const OurServices_1 = lazy(() => import("./OurServices/OurServices_1"));
const OurServices_2 = lazy(() => import("./OurServices/OurServices_2"));
const OurServices_3 = lazy(() => import("./OurServices/OurServices_3"));
const PricingSection_1 = lazy(() => import("./PricingSection/PricingSection_1"));
const PricingSection_2 = lazy(() => import("./PricingSection/PricingSection_2"));
const PricingSection_3 = lazy(() => import("./PricingSection/PricingSection_3"));
const BookAppointment_1 = lazy(() => import("./BookAppointment/BookAppointment_1"));
const BookAppointment_2 = lazy(() => import("./BookAppointment/BookAppointment_2"));
const BookAppointment_3 = lazy(() => import("./BookAppointment/BookAppointment_3"));
const OurDentist_1 = lazy(() => import("./OurDentist/OurDentist_1"));
const OurDentist_2 = lazy(() => import("./OurDentist/OurDentist_2"));
const OurDentist_3 = lazy(() => import("./OurDentist/OurDentist_3"));
const Testimonials_1 = lazy(() => import("./TestimonialsSection/Testimonials_1"));
const Testimonials_2 = lazy(() => import("./TestimonialsSection/Testimonials_2"));
const Testimonials_3 = lazy(() => import("./TestimonialsSection/Testimonials_3"));
const LatestNews_1 = lazy(() => import("./LatestNewsSection/LatestNews_1"));
const LatestNews_2 = lazy(() => import("./LatestNewsSection/LatestNews_2"));
const LatestNews_3 = lazy(() => import("./LatestNewsSection/LatestNews_3"));

// Map of standard components
const componentMap = {
  TopSlider_1, TopSlider_2, TopSlider_3,
  AboutSection_1, AboutSection_2, AboutSection_3,
  OurServices_1, OurServices_2, OurServices_3,
  PricingSection_1, PricingSection_2, PricingSection_3,
  BookAppointment_1, BookAppointment_2, BookAppointment_3,
  OurDentist_1, OurDentist_2, OurDentist_3,
  Testimonials_1, Testimonials_2, Testimonials_3,
  LatestNews_1, LatestNews_2, LatestNews_3,
};

// Lazy load custom components dynamically
const customComponentsMap = import.meta.glob('./Custom/*.jsx');

// Create a mapping from component name -> importer function
const customComponentsLookup = {};
for (const path in customComponentsMap) {
  const match = path.match(/\/([^/]+)\.jsx$/); // match file name
  if (match) {
    const name = match[1]; // e.g., Test1
    customComponentsLookup[name] = customComponentsMap[path];
  }
}

async function importCustomComponent(name) {
  const importer = customComponentsLookup[name];
  if (!importer) {
    console.error(`Custom component not found: ${name}`);
    return null;
  }
  const module = await importer();
  return module.default;
}

// Default Sections
const defaultSections = [
  { id: '1', type: 'top_slider', variant: 'TopSlider_1', order: 1, settings: { is_visible: true } },
  { id: '2', type: 'about_section', variant: 'AboutSection_1', order: 2, settings: { is_visible: true } },
  { id: '3', type: 'our_services', variant: 'OurServices_1', order: 3, settings: { is_visible: true } },
  { id: '4', type: 'pricing_section', variant: 'PricingSection_1', order: 4, settings: { is_visible: true } },
  { id: '5', type: 'book_appointment', variant: 'BookAppointment_1', order: 5, settings: { is_visible: true } },
  { id: '6', type: 'our_dentist', variant: 'OurDentist_1', order: 6, settings: { is_visible: true } },
  { id: '7', type: 'testimonials', variant: 'Testimonials_1', order: 7, settings: { is_visible: true } },
  { id: '8', type: 'latest_news', variant: 'LatestNews_1', order: 8, settings: { is_visible: true } }
];

const Home = ({ pageConfig }) => {
  const { sections = defaultSections } = pageConfig || {};
  const [loadedCustomComponents, setLoadedCustomComponents] = useState({});
  console.log(pageConfig);


  // Load custom components dynamically
  useEffect(() => {
    const loadCustomComponents = async () => {
      const customSections = sections.filter(s => !componentMap[s.variant]);
      const components = { ...loadedCustomComponents };

      for (const section of customSections) {
        if (!components[section.variant]) {
          const Component = await importCustomComponent(section.variant);
          if (Component) components[section.variant] = Component;
        }
      }

      setLoadedCustomComponents(components);
    };

    loadCustomComponents();
  }, [sections]);

  const sortedSections = [...sections].sort((a, b) => a.order - b.order);

  return (
    <PublicLayout>
      <Head title="Home Page" />
      <Suspense fallback={<div>Loading...</div>}>
        {sortedSections.map(section => {
          if (section.settings?.is_visible === false) return null;

          // Check if it's a standard component
          let Component = componentMap[section.variant];

          // If not standard, try custom
          if (!Component) Component = loadedCustomComponents[section.variant];

          // Still not loaded? show a loading placeholder
          if (!Component) return (
            <div key={section.id}>Loading {section.variant}...</div>
          );

          return <Component key={section.id} settings={section.settings} />;
        })}
      </Suspense>
    </PublicLayout>
  );
};

export default Home;
