# Use Argos CI for screenshot comparison and review

Keep the catalog-driven Pest/Playwright capture suite and its catalog coverage audit, but send complete ChipperCI captures to Argos for baseline comparison and pull-request review. Remove versioned baselines and the custom pixel comparator; retain a local/testing-only gallery for recent captures. Store the Argos project token only as a protected ChipperCI secret, map ChipperCI commit and branch metadata into the Argos CLI, and require the Argos visual check in repository branch protection.
