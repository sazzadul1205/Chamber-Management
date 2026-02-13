import { lazy } from "react";

// Lazy load all section components
export const componentMap = {
    // Top Slider Variants
    TopSlider_1: lazy(() => import("../../Home/TopSlider/TopSlider_1")),
    TopSlider_2: lazy(() => import("../../Home/TopSlider/TopSlider_2")),
    TopSlider_3: lazy(() => import("../../Home/TopSlider/TopSlider_3")),

    // About Section Variants
    AboutSection_1: lazy(
        () => import("../../Home/AboutSection/AboutSection_1"),
    ),
    AboutSection_2: lazy(
        () => import("../../Home/AboutSection/AboutSection_2"),
    ),
    AboutSection_3: lazy(
        () => import("../../Home/AboutSection/AboutSection_3"),
    ),

    // Our Services Variants
    OurServices_1: lazy(() => import("../../Home/OurServices/OurServices_1")),
    OurServices_2: lazy(() => import("../../Home/OurServices/OurServices_2")),
    OurServices_3: lazy(() => import("../../Home/OurServices/OurServices_3")),

    // Pricing Section Variants
    PricingSection_1: lazy(
        () => import("../../Home/PricingSection/PricingSection_1"),
    ),
    PricingSection_2: lazy(
        () => import("../../Home/PricingSection/PricingSection_2"),
    ),
    PricingSection_3: lazy(
        () => import("../../Home/PricingSection/PricingSection_3"),
    ),

    // Book Appointment Variants
    BookAppointment_1: lazy(
        () => import("../../Home/BookAppointment/BookAppointment_1"),
    ),
    BookAppointment_2: lazy(
        () => import("../../Home/BookAppointment/BookAppointment_2"),
    ),
    BookAppointment_3: lazy(
        () => import("../../Home/BookAppointment/BookAppointment_3"),
    ),

    // Our Dentist Variants
    OurDentist_1: lazy(() => import("../../Home/OurDentist/OurDentist_1")),
    OurDentist_2: lazy(() => import("../../Home/OurDentist/OurDentist_2")),
    OurDentist_3: lazy(() => import("../../Home/OurDentist/OurDentist_3")),

    // Testimonials Variants
    Testimonials_1: lazy(
        () => import("../../Home/TestimonialsSection/Testimonials_1"),
    ),
    Testimonials_2: lazy(
        () => import("../../Home/TestimonialsSection/Testimonials_2"),
    ),
    Testimonials_3: lazy(
        () => import("../../Home/TestimonialsSection/Testimonials_3"),
    ),

    // Latest News Variants
    LatestNews_1: lazy(
        () => import("../../Home/LatestNewsSection/LatestNews_1"),
    ),
    LatestNews_2: lazy(
        () => import("../../Home/LatestNewsSection/LatestNews_2"),
    ),
    LatestNews_3: lazy(
        () => import("../../Home/LatestNewsSection/LatestNews_3"),
    ),
};

// Simple but reliable unique ID generator
export function generateUniqueId() {
    return `${Date.now()}-${Math.random().toString(36).substr(2, 9)}-${performance.now().toString(36)}`;
}

// Generate navId from text
export function generateNavId(text) {
    if (!text) return "";
    return text
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, "")
        .replace(/\s+/g, "-")
        .replace(/-+/g, "-");
}

// Default variants for sections
export const DEFAULT_VARIANTS = {
    basic: "Basic",
    sweet: "Sweet",
    dark: "Dark",
};

// Preload all components to prevent connection issues
export const preloadComponents = async () => {
    const preloadPromises = Object.values(componentMap).map(
        async (component) => {
            try {
                // Trigger the lazy component to load
                await component._init?.();
            } catch (error) {
                console.error("Failed to preload component:", error);
            }
        },
    );

    await Promise.allSettled(preloadPromises);
};

// Preload custom components
export const preloadCustomComponents = async (componentNames) => {
    const preloadPromises = componentNames.map(async (name) => {
        try {
            const module = await import(`../../Home/Custom/${name}.jsx`);
            return { name, loaded: true, module: module.default };
        } catch (error) {
            console.error(`Failed to preload custom component: ${name}`, error);
            return { name, loaded: false, error };
        }
    });

    return await Promise.allSettled(preloadPromises);
};

// Helper function to safely get variant name
export function getVariantName(sections, sectionType, variantKey) {
    if (!sections || !sections[sectionType]) return variantKey;
    return sections[sectionType]?.variants?.[variantKey] || variantKey;
}

// Helper function to safely get section name
export function getSectionName(sections, sectionType) {
    if (!sections || !sections[sectionType]) return sectionType;
    return sections[sectionType]?.name || sectionType;
}
