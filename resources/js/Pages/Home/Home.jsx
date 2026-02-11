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

    </PublicLayout>
  );
};

export default Home;