import PublicLayout from "@/Layouts/PublicLayout";
import TopSlider_1 from "./TopSlider/TopSlider_1";
import TopSlider_2 from "./TopSlider/TopSlider_2";
import TopSlider_3 from "./TopSlider/TopSlider_3";
import AboutSection_1 from "./AboutSection/AboutSection_1";
import AboutSection_2 from "./AboutSection/AboutSection_2";
import AboutSection_3 from "./AboutSection/AboutSection_3";
import OurServices_1 from "./OurServices/OurServices_1";
import OurServices_2 from "./OurServices/OurServices_2";
import OurServices_3 from "./OurServices/OurServices_3";
import PricingSection_1 from "./PricingSection/PricingSection_1";
import PricingSection_2 from "./PricingSection/PricingSection_2";
import PricingSection_3 from "./PricingSection/PricingSection_3";
import BookAppointment_1 from "./BookAppointment/BookAppointment_1";
import BookAppointment_2 from "./BookAppointment/BookAppointment_2";
import BookAppointment_3 from "./BookAppointment/BookAppointment_3";

const Home = () => {
  return (
    <PublicLayout>

      <TopSlider_1 />
      <TopSlider_2 />
      <TopSlider_3 />

      <AboutSection_1 />
      <AboutSection_2 />
      <AboutSection_3 />

      <OurServices_1 />
      <OurServices_2 />
      <OurServices_3 />

      <PricingSection_1 />
      <PricingSection_2 />
      <PricingSection_3 />

      <BookAppointment_1 />
      <BookAppointment_2 />
      <BookAppointment_3 />

    </PublicLayout>
  );
};

export default Home;