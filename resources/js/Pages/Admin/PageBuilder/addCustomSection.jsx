const addCustomSection = async (componentName) => {
  // Preload the custom component immediately when adding
  try {
    const module = await import(`../../Pages/Home/Custom/${componentName}.jsx`);
    setCustomComponentCache(prev => ({
      ...prev,
      [componentName]: module.default
    }));
  } catch (error) {
    console.error(`Failed to load custom component: ${componentName}`, error);
  }

  const newSection = {
    id: generateUniqueId(),
    type: componentName,
    variant: componentName,
    order: activeSections.length + 1,
    // CRITICAL: Explicitly set is_custom to true
    is_custom: true,
    settings: {
      is_visible: true,
      title: componentName,
      created_at: new Date().toISOString()
    }
  };

  const updatedSections = [...activeSections, newSection];
  setActiveSections(updatedSections);

  MySwal.fire({
    icon: 'success',
    title: 'Custom Component Added',
    text: `${componentName} has been added to your layout.`,
    timer: 1500,
    showConfirmButton: false,
    position: 'bottom-end',
    toast: true,
    background: 'white'
  });
};