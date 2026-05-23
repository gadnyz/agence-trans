---
name: KASHALA Trans Management
colors:
  surface: '#f8f9fb'
  surface-dim: '#d9dadc'
  surface-bright: '#f8f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f4f6'
  surface-container: '#edeef0'
  surface-container-high: '#e7e8ea'
  surface-container-highest: '#e1e2e4'
  on-surface: '#191c1e'
  on-surface-variant: '#444748'
  inverse-surface: '#2e3132'
  inverse-on-surface: '#f0f1f3'
  outline: '#747878'
  outline-variant: '#c4c7c8'
  surface-tint: '#5d5f5f'
  primary: '#5d5f5f'
  on-primary: '#ffffff'
  primary-container: '#ffffff'
  on-primary-container: '#747676'
  inverse-primary: '#c6c6c7'
  secondary: '#5c5f60'
  on-secondary: '#ffffff'
  secondary-container: '#e1e3e4'
  on-secondary-container: '#626566'
  tertiary: '#5d5f5f'
  on-tertiary: '#ffffff'
  tertiary-container: '#ffffff'
  on-tertiary-container: '#747676'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e2e2e2'
  primary-fixed-dim: '#c6c6c7'
  on-primary-fixed: '#1a1c1c'
  on-primary-fixed-variant: '#454747'
  secondary-fixed: '#e1e3e4'
  secondary-fixed-dim: '#c5c7c8'
  on-secondary-fixed: '#191c1d'
  on-secondary-fixed-variant: '#454748'
  tertiary-fixed: '#e2e2e2'
  tertiary-fixed-dim: '#c6c6c7'
  on-tertiary-fixed: '#1a1c1c'
  on-tertiary-fixed-variant: '#454747'
  background: '#f8f9fb'
  on-background: '#191c1e'
  surface-variant: '#e1e2e4'
typography:
  h1:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.02em
  h2:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 18px
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  status-badge:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 12px
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  gutter: 16px
  margin: 24px
---

## Brand & Style

The design system is rooted in the **Corporate / Modern** aesthetic, prioritizing administrative efficiency and clarity above all else. It draws inspiration from the organizational simplicity of Trello and the functional density of Odoo to create an environment where high-volume data management feels effortless.

The emotional response should be one of "controlled precision." By utilizing a restricted color palette and generous whitespace, the interface recedes to the background, allowing the reservation data and workflow statuses to become the primary focus. The target audience—logistics coordinators and ticket agents—requires a tool that minimizes cognitive load during high-traffic periods. The style is strictly utilitarian, avoiding decorative elements in favor of structural integrity and logical grouping.

## Colors

The palette for this design system is intentionally monochromatic to emphasize the "clean" aesthetic. The primary surface is **White (#FFFFFF)**, set against a **Light Gray (#F3F4F6)** application background to create a subtle layered effect reminiscent of a digital workspace.

**Accent Blue (#2563EB)** is reserved exclusively for primary calls to action (e.g., "Créer une réservation") and active navigation states. This ensures that the user's eye is immediately drawn to the next logical step in the workflow. 

Status colors follow a standard semantic convention to ensure immediate recognition:
- **Brouillon (Draft):** Neutral gray to signify an incomplete state.
- **Réservé (Reserved):** Warm yellow for pending confirmation.
- **Payé (Paid):** Secure green for successful transactions.
- **Embarqué (Boarded):** Informative blue for active logistics.
- **Annulé (Cancelled):** High-alert red for terminal states.

## Typography

The system utilizes **Inter** exclusively to leverage its exceptional legibility on digital screens. As a management tool, the typography is optimized for "scanning" rather than long-form reading. 

Headlines use a tighter letter-spacing and heavier weights to provide clear section anchoring. The default body size is set to **14px** to allow for high information density without sacrificing readability. Given the French language requirements (which often result in longer string lengths than English), the typography scales gracefully; condensed spacing is used for labels and status badges to prevent UI overflow in narrow table columns or cards.

## Layout & Spacing

This design system employs a **Fluid Grid** philosophy within a structured shell. 
- **The Shell:** A fixed-height **Topbar** (Odoo-style) houses the module switcher and breadcrumbs. 
- **The Canvas:** Below the topbar, the main content area uses a light gray background (#F3F4F6) where cards and data tables live.
- **Spacing Rhythm:** An 8px linear scale (with a 4px half-step for tight components) governs all padding and margins. 

Data tables should occupy the full width of their containers to maximize column visibility, while individual reservation "cards" should have a maximum width in Kanban views to maintain the Trello-inspired vertical stack.

## Elevation & Depth

To maintain the clean, minimalist aesthetic, this design system avoids heavy shadows or complex gradients. Instead, it uses **Low-contrast outlines** and **Tonal layers**.

- **Level 0 (Background):** #F3F4F6 – The foundation of the application.
- **Level 1 (Cards/Worksheets):** #FFFFFF – These elements sit on the background with a very soft, diffused ambient shadow (`0 1px 3px rgba(0,0,0,0.05)`) and a subtle border (`1px solid #E5E7EB`).
- **Level 2 (Dropdowns/Modals):** These use a more pronounced shadow to indicate temporal focus, but maintain the same white background and border style.

The goal is to simulate physical paper cards sitting on a clean desk; the depth is enough to separate elements but not so much as to distract.

## Shapes

The shape language is **Soft** and professional. A standard radius of **4px (0.25rem)** is applied to buttons, input fields, and status badges. This provides a modern touch while maintaining a serious, institutional feel.

Larger containers like cards or the module switcher panel may use a **rounded-lg (8px)** corner to emphasize their role as structural anchors. Status badges are the only exception where a fully rounded (pill-shaped) geometry may be used to differentiate them from interactive buttons.

## Components

### Buttons
- **Primary:** Solid #2563EB with white text. Flat, no gradient.
- **Secondary:** White background with #E5E7EB border and #4B5563 text.
- **Action Icons:** Transparent background, gray icons that turn blue on hover.

### Cards
- Trello-inspired: White background, 4px radius, 1px #E5E7EB border. Internal padding is usually 12px or 16px.

### Navigation (Odoo-inspired)
- **Topbar:** Compact (approx 48px height), dark gray or white background. 
- **Breadcrumbs:** Simple "Home / Reservations / Edit" string using the Accent Blue for links.

### Status Badges
- Small, uppercase text. 
- Style: Lightly tinted background (10% opacity of the status color) with the solid status color for the text, or a small colored dot next to gray text.

### Data Tables
- Clean, no vertical borders. 
- Horizontal separators in #E5E7EB. 
- Row hover state uses #F9FAFB to provide visual feedback without high contrast.

### Input Fields
- White background, 1px #E5E7EB border.
- On focus: Border changes to #2563EB with a 2px soft blue glow.