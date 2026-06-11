---
name: AeroCast
colors:
  surface: "#141312"
  surface-dim: "#141312"
  surface-bright: "#3b3937"
  surface-container-lowest: "#0f0e0d"
  surface-container-low: "#1d1b1a"
  surface-container: "#211f1e"
  surface-container-high: "#2b2a28"
  surface-container-highest: "#363433"
  on-surface: "#e6e1df"
  on-surface-variant: "#d0c5b8"
  inverse-surface: "#e6e1df"
  inverse-on-surface: "#32302f"
  outline: "#998f84"
  outline-variant: "#4d463c"
  surface-tint: "#dec29e"
  primary: "#dec29e"
  on-primary: "#3f2d14"
  primary-container: "#a68d6c"
  on-primary-container: "#37270e"
  inverse-primary: "#705b3d"
  secondary: "#cfc5bd"
  on-secondary: "#352f2a"
  secondary-container: "#4e4842"
  on-secondary-container: "#c0b7af"
  tertiary: "#d8c49f"
  on-tertiary: "#3b2f14"
  tertiary-container: "#a08f6d"
  on-tertiary-container: "#34280e"
  error: "#ffb4ab"
  on-error: "#690005"
  error-container: "#93000a"
  on-error-container: "#ffdad6"
  primary-fixed: "#fcdeb8"
  primary-fixed-dim: "#dec29e"
  on-primary-fixed: "#271903"
  on-primary-fixed-variant: "#574328"
  secondary-fixed: "#ebe1d8"
  secondary-fixed-dim: "#cfc5bd"
  on-secondary-fixed: "#1f1b16"
  on-secondary-fixed-variant: "#4c4640"
  tertiary-fixed: "#f5e0ba"
  tertiary-fixed-dim: "#d8c49f"
  on-tertiary-fixed: "#241a03"
  on-tertiary-fixed-variant: "#534529"
  background: "#141312"
  on-background: "#e6e1df"
  surface-variant: "#363433"
typography:
  headline-xl:
    fontFamily: Space Grotesk
    fontSize: 64px
    fontWeight: "700"
    lineHeight: "1.1"
    letterSpacing: -0.04em
  headline-lg:
    fontFamily: Space Grotesk
    fontSize: 40px
    fontWeight: "700"
    lineHeight: "1.2"
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Space Grotesk
    fontSize: 32px
    fontWeight: "700"
    lineHeight: "1.2"
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: "400"
    lineHeight: "1.6"
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: "400"
    lineHeight: "1.6"
  label-md:
    fontFamily: Space Grotesk
    fontSize: 14px
    fontWeight: "600"
    lineHeight: "1.0"
    letterSpacing: 0.1em
  mono-data:
    fontFamily: Space Grotesk
    fontSize: 12px
    fontWeight: "500"
    lineHeight: "1.0"
    letterSpacing: 0.05em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 8px
  gutter: 16px
  margin-mobile: 20px
  margin-desktop: 64px
  block-gap: 32px
---

## Brand & Style

The design system is rooted in **Atmospheric Brutalism**. It bridges the gap between raw, technical utility and a weathered, organic aesthetic. The UI should feel like a high-end flight instrument found in a sepia-toned landscape—precise and digital, yet tactile and aged.

The visual language draws heavily from early computing aesthetics (dithering, scanlines, and vertical patterns) combined with a sophisticated earthy palette. The emotional response is one of grounded reliability, technical expertise, and a "low-fi/high-tech" atmosphere. Every element should look intentionally placed, almost as if it were stamped or laser-etched onto a physical substrate.

## Colors

The palette is defined by a "Deep Sepia" spectrum. The background should predominantly use the secondary color (#26211C) or neutral (#121110) to create a sense of depth and weight.

- **Primary (#967E5E):** Used for key structural elements and mid-tone patterns.
- **Secondary (#26211C):** The foundational "ink" color for containers and deep shadows.
- **Tertiary (#D9C5A0):** A high-contrast highlight color for critical text and "stamped" UI elements.
- **Neutral (#121110):** The base for the canvas, providing a low-light environment that allows the earthy tones to glow.

Use **dithering patterns** (1px checkerboards or vertical stippling) to transition between these colors, avoiding smooth CSS gradients in favor of digital-texture transitions.

## Typography

Typography in this design system is architectural. **Space Grotesk** is used for all headlines and labels to provide a technical, geometric edge. All headlines must be set in **Uppercase** with tight letter-spacing to mimic block-printed headers.

**Inter** provides the necessary legibility for body copy and descriptions, acting as a neutral anchor to the more aggressive display type. For data visualization or technical readouts, use the `mono-data` role to emphasize the "flight data" aspect of the brand.

## Layout & Spacing

The layout follows a **Fixed-Fluid Hybrid** model. Content is organized within a 12-column grid that snaps to fixed widths on desktop (max-width 1440px) to maintain the "printed page" composition.

- **Vertical Rhythm:** Use the 8px base unit for all spacing. Elements should feel "locked" into the grid.
- **Vertical Line Patterns:** Use vertical dividers (1px solid) to separate columns instead of whitespace alone. This reinforces the technical, scanline aesthetic.
- **Breakpoints:**
  - **Mobile (<768px):** 4 columns, 20px margins. Content flows vertically.
  - **Tablet (768px - 1024px):** 8 columns, 32px margins.
  - **Desktop (>1024px):** 12 columns, 64px margins.

## Elevation & Depth

This design system eschews traditional soft shadows and blurs. Depth is achieved through **Solid Offsets** and **Tonal Stacking**.

1.  **Hard Shadows:** Use 4px or 8px solid offsets (no blur) in the primary or secondary color to lift elements off the background.
2.  **Texture Overlay:** A global "grain" or "dither" noise layer should be applied to the entire UI at low opacity (3-5%) to unify the digital and organic elements.
3.  **Patterned Depth:** Background elements utilize vertical line patterns of varying density to suggest distance or hierarchy—denser lines indicate "recessed" areas, while solid fills indicate "elevated" surfaces.
4.  **Borders:** Use high-contrast 2px borders to define the boundaries of all interactive blocks.

## Shapes

The shape language is primarily **Rectilinear**. However, to prevent the UI from feeling overly "default" or harsh, a subtle **0.25rem (Soft)** radius is applied to buttons and primary containers. This creates the "stamped" look—reminiscent of industrial parts that have been slightly smoothed at the edges.

Larger cards and sections should maintain sharp corners to emphasize the grid-based construction of the layout.

## Components

- **Buttons:** Large, blocky, and high-contrast. Use a solid fill for primary actions and a heavy 2px border for secondary. On hover, the button should shift its solid shadow (e.g., from 4px to 0px) to simulate a physical "press."
- **Inputs:** Simple rectangular boxes with a 2px border. Use the `mono-data` font for user input. The focus state should invert the border and text color rather than using a glow.
- **Chips / Tags:** Sharp-cornered boxes with vertical line dither fills on one side to indicate status or category.
- **Cards:** Defined by heavy borders and often accompanied by a "Technical Header"—a small label area above the main content that lists metadata (e.g., DATE // CODE // STATUS).
- **Lists:** Separated by horizontal 1px lines. Every third or fourth line can be slightly thicker or a different color to create a "computer printout" rhythm.
- **Dithered Dividers:** Instead of simple lines, use a repeating 1px dither pattern for section breaks.
