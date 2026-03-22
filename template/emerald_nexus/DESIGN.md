# Emerald Nexus Design System

### 1. Overview & Creative North Star
**Creative North Star: "The Modern Agora"**
Emerald Nexus is a high-end editorial design system that bridges the tactile reliability of traditional marketplaces with the fluid efficiency of digital commerce. It rejects the "standard dashboard" aesthetic in favor of a curated, magazine-style layout. By utilizing aggressive typography scales, intentional whitespace, and high-energy accent colors, the system creates a sense of urgency and prestige. 

The system breaks the rigid grid through "The Floating Anchor" principle: while key information is grounded in a logical hierarchy, hero elements and interactive cards use generous shadows and backdrop blurs to appear as if floating over a clean, architectural canvas.

### 2. Colors
Emerald Nexus is built on a foundation of high-contrast neutrals and a signature "Electric Emerald" primary.

*   **Primary (#47eb7e):** Used for critical CTAs and brand identifiers. It is high-chroma and demands immediate attention.
*   **Surface Hierarchy:**
    *   **The "No-Line" Rule:** Sectioning is achieved through color blocks (e.g., transitioning from white to `#f6f8f6`) rather than 1px borders. If a boundary is required for clarity, use `outline` at 0.5 opacity.
    *   **Surface Nesting:** Content cards inhabit `surface_container_lowest` (Pure White) to pop against the `background` (Off-white).
*   **Signature Textures:** Use 10% opacity primary color glows (`primary/10`) for badges and background decorative elements to create depth without visual clutter.

### 3. Typography
The system uses **Inter** across all levels, relying on weight and tracking rather than font switching to create hierarchy.

**Ground Truth Scales:**
*   **Display (Headline):** 3rem (48px) to 4.5rem (72px). Use `font-black` (900) with `tracking-tight` (-0.025em) for high-impact hero sections.
*   **Headline/Title:** 1.875rem (30px) for section headers, maintaining a bold or black weight.
*   **Body:** 1.125rem (18px) for primary reading, 0.875rem (14px) for secondary metadata.
*   **Labels:** 0.75rem (12px) with `uppercase` and `tracking-widest` (0.1em) for category tags like "PREMIUM" or "NEW OPENING."

### 4. Elevation & Depth
Depth is a first-class citizen in Emerald Nexus, moving away from flat design toward a "layered architecture."

*   **The Layering Principle:** Use a stack of `surface` -> `surface_container` -> `surface_container_low` to define functional zones.
*   **Shadow Ground Truth:**
    *   **Low Elevation (shadow-sm):** Used for subtle card boundaries.
    *   **Mid Elevation (shadow-lg/xl):** Used for interactive elements like filter bars and primary CTAs.
    *   **High Elevation (shadow-2xl):** Reserved for hero imagery and modal elements to create a dramatic sense of z-axis depth.
*   **Glassmorphism:** Navigation bars and "floating" badges must use a `backdrop-blur-md` with an 80% opacity background color to maintain context while ensuring legibility.

### 5. Components
*   **Primary Buttons:** Large (16px+ padding), `rounded-xl` (12px), featuring `font-bold`. Use the primary emerald color with dark text (`slate-900`) for maximum contrast.
*   **Secondary Buttons:** Ghost-style with `border-slate-200` and `rounded-xl`. High-speed hover transitions are required.
*   **Interactive Cards:** Must feature `overflow-hidden` and a `group-hover:scale-105` effect on internal imagery to provide tactile feedback.
*   **Filter Bar:** A "floating" composite component that sits across section boundaries, using `shadow-xl` to emphasize its global utility.
*   **Badges:** Pill-shaped (`rounded-full`) with 10% opacity backgrounds of the text color.

### 6. Do's and Don'ts
*   **Do:** Use extreme font weight contrasts (e.g., black 900 vs regular 400) to guide the eye.
*   **Do:** Use "Electric Emerald" sparingly as a spotlight, not a floodlight.
*   **Don't:** Use 1px solid black borders. Use `slate-100` or `slate-200` if a border is unavoidable.
*   **Don't:** Crowd elements. If in doubt, increase the `spacing` variable to create an editorial feel.
*   **Do:** Ensure dark mode transitions maintain the same depth hierarchy by swapping slate neutrals for deeper obsidian tones.