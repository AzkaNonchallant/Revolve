# DESIGN.md

# Adidas-Inspired E-Commerce Design System

> A high-confidence visual and interaction reference derived from the public adidas US storefront at `https://www.adidas.com/us`.
>
> This document describes the visual language, layout behavior, spacing system, component patterns, responsive behavior, interaction rules, and content hierarchy observed from the reference.
>
> **Important:** Use this document as a design-language reference, not as an instruction to copy Adidas trademarks, proprietary assets, logos, product photography, or copyrighted content.

---

# 1. Design Direction

## Core Character

The interface should feel:

- athletic
- premium
- editorial
- modern
- confident
- minimal
- highly visual
- commerce-focused
- spacious without feeling empty
- functional before decorative

The design should communicate a premium sports/lifestyle brand rather than a generic SaaS dashboard.

## Design Principles

### 1. Strong visual hierarchy

Large imagery and short headlines dominate the page.

Do not overload sections with paragraphs.

Primary communication should generally follow:

```text
IMAGE
↓
SHORT HEADLINE
↓
ONE-SENTENCE DESCRIPTION
↓
PRIMARY ACTION
```

### 2. High contrast

Use strong foreground/background contrast.

Preferred foundation:

```text
Black
White
Near-white
Neutral gray
```

Accent colors may be introduced by campaigns, products, or contextual sections.

Do not create a permanently colorful UI.

### 3. Editorial first

Important sections should feel closer to an editorial fashion/sports magazine than a traditional ecommerce catalog.

Use:

- large photography
- strong crops
- bold headings
- short copy
- generous visual areas

### 4. Product information stays simple

Product cards should not become miniature dashboards.

Prioritize:

```text
Image
Product name
Category
Price
Color/variant
Optional badge
```

### 5. Content density changes by context

Marketing sections:

```text
LOW DENSITY
LARGE VISUALS
LARGE HEADLINES
```

Product browsing:

```text
HIGHER DENSITY
GRID
FILTERS
COMPACT INFORMATION
```

Navigation:

```text
COMPACT
FAST
HIGH CONTRAST
```

---

# 2. Global Layout

## Page Structure

Recommended global hierarchy:

```text
┌──────────────────────────────────────────────┐
│ Utility Bar                                  │
├──────────────────────────────────────────────┤
│ Main Navigation                              │
├──────────────────────────────────────────────┤
│                                              │
│ Hero / Campaign                              │
│                                              │
├──────────────────────────────────────────────┤
│ Section Header                               │
│                                              │
│ Horizontal Cards / Carousel                  │
│                                              │
├──────────────────────────────────────────────┤
│ Section Header                               │
│                                              │
│ Editorial / Product Content                  │
│                                              │
├──────────────────────────────────────────────┤
│ Category Links                               │
├──────────────────────────────────────────────┤
│ Large SEO / Brand Content                    │
├──────────────────────────────────────────────┤
│ Footer                                       │
└──────────────────────────────────────────────┘
```

## Width

Use the viewport aggressively.

Marketing sections should generally span:

```css
width: 100%;
```

Content-heavy sections may use:

```css
max-width: 1440px;
margin-inline: auto;
```

Do not constrain the entire site to a narrow centered container.

The reference uses large horizontal visual surfaces.

## Horizontal Padding

Desktop:

```text
24px - 48px
```

Large desktop:

```text
32px - 64px
```

Mobile:

```text
16px - 20px
```

Avoid excessive 80px+ page padding unless the section is intentionally editorial.

---

# 3. Spacing System

Use a consistent base spacing scale.

```text
4px
8px
12px
16px
24px
32px
40px
48px
64px
80px
96px
120px
```

## Common Usage

```text
4px   icon/internal alignment
8px   compact gaps
12px  metadata
16px  card padding / mobile spacing
24px  component spacing
32px  section internal spacing
48px  section spacing
64px  major section spacing
80px  hero/content separation
96px  large editorial spacing
120px major visual separation
```

Do not randomly use values such as:

```text
13px
27px
37px
53px
71px
```

unless required by an existing component.

Consistency matters more than arbitrary pixel precision.

---

# 4. Color System

## Base Palette

Use a monochrome-first palette.

```css
--color-black: #000000;
--color-white: #ffffff;

--color-neutral-50: #f7f7f7;
--color-neutral-100: #f3f3f3;
--color-neutral-200: #e6e6e6;
--color-neutral-300: #d1d1d1;
--color-neutral-400: #999999;
--color-neutral-500: #666666;
--color-neutral-600: #444444;
--color-neutral-700: #2b2b2b;
--color-neutral-800: #171717;
--color-neutral-900: #0a0a0a;
```

## Primary UI

Default:

```text
Background: #FFFFFF
Text: #000000
Secondary text: #666666
Borders: #E6E6E6
```

Dark sections:

```text
Background: #000000
Text: #FFFFFF
Secondary text: #BDBDBD
```

## Accent Color

Accent colors should be contextual.

Examples:

```text
red
green
blue
yellow
orange
```

Do not make an accent color appear on every component.

The accent should support campaigns, statuses, promotions, or selected states.

---

# 5. Typography

## Typography Character

The reference uses a modern commercial sans-serif style.

The visual goal is:

```text
clean
compact
bold
highly legible
```

Prefer:

```css
font-family:
  Inter,
  Helvetica Neue,
  Helvetica,
  Arial,
  sans-serif;
```

If the project already contains a licensed brand font, use that instead.

Do not download proprietary brand fonts merely to imitate the reference.

---

# 6. Type Scale

## Display

Hero:

```text
48px - 80px
font-weight: 700 - 800
line-height: 0.95 - 1.05
letter-spacing: -0.03em
```

Large desktop editorial:

```text
40px - 64px
```

## Heading

H1:

```text
40px - 56px
font-weight: 700
line-height: 1.0 - 1.1
```

H2:

```text
32px - 44px
font-weight: 700
line-height: 1.05 - 1.15
```

H3:

```text
24px - 32px
font-weight: 700
```

H4:

```text
18px - 24px
font-weight: 600 - 700
```

## Body

Large:

```text
18px
line-height: 1.5
```

Normal:

```text
16px
line-height: 1.45
```

Small:

```text
14px
line-height: 1.4
```

Metadata:

```text
12px - 14px
```

---

# 7. Letter Spacing

Large headlines:

```css
letter-spacing: -0.03em;
```

Normal headings:

```css
letter-spacing: -0.02em;
```

Body:

```css
letter-spacing: 0;
```

Navigation:

```css
letter-spacing: 0;
```

Uppercase labels may use:

```css
letter-spacing: 0.02em;
```

Do not excessively track text.

---

# 8. Navigation

## Utility Bar

A compact top-level utility navigation exists above the main navigation.

Typical items include:

```text
Store Finder
Help
Orders & Returns
Gift Cards
Membership
```

Characteristics:

```text
small typography
high information density
minimal vertical height
secondary visual importance
```

Suggested height:

```text
32px - 40px
```

Desktop only when appropriate.

---

# 9. Primary Navigation

The main navigation should be visually dominant but compact.

Structure:

```text
[Brand] [MEN] [WOMEN] [KIDS] [SALE] [GIFTS] [SPORTS] [NEW & TRENDING]    [SEARCH] [ACCOUNT] [BAG]
```

The reference exposes major categories such as MEN, WOMEN, KIDS, SALE, GIFTS, SPORTS and NEW & TRENDING.

## Navigation Characteristics

```text
height: 64px - 80px
background: white
text: black
font-weight: 600 - 700
```

Navigation items should have:

```text
strong hover state
clear hit area
minimal decorative treatment
```

Avoid pill-shaped navigation.

Avoid excessive shadows.

---

# 10. Search

Search should feel like a first-class navigation action.

Desktop:

```text
visible search field
```

Mobile:

```text
icon/button
```

Search field:

```text
background: #F3F3F3
border: none
height: 40px - 48px
padding-inline: 12px - 16px
```

Focus:

```text
visible outline
high contrast
```

Avoid giant rounded search bars.

---

# 11. Hero Section

Hero sections are the strongest visual element.

## Structure

```text
┌──────────────────────────────────────────┐
│                                          │
│              LARGE IMAGE                 │
│                                          │
│                                          │
│       HEADLINE                           │
│       Short supporting copy              │
│       [PRIMARY ACTION]                   │
│                                          │
└──────────────────────────────────────────┘
```

## Hero Image

Desktop:

```text
min-height: 560px
preferred: 640px - 800px
```

Use:

```css
object-fit: cover;
width: 100%;
```

The image should dominate.

Do not put the hero inside a small card.

## Hero Text

Maximum width:

```text
420px - 560px
```

Keep copy concise.

Example structure:

```text
NEW COLLECTION

Built for movement.

Discover the latest collection.

[SHOP NOW]
```

---

# 12. Hero Overlay

Text may appear:

```text
bottom-left
bottom-center
center-left
center
```

depending on image composition.

Always prioritize readability.

Use:

```text
dark gradient overlay
```

only when necessary.

Avoid heavy overlays that visibly alter the image.

---

# 13. Campaign Cards

Campaign sections should use large visual cards.

Recommended desktop layouts:

```text
2 columns
3 columns
4 columns
```

depending on content.

Example:

```text
┌────────────────┐ ┌────────────────┐
│                │ │                │
│     IMAGE      │ │     IMAGE      │
│                │ │                │
├────────────────┤ ├────────────────┤
│ TITLE          │ │ TITLE          │
│ Description    │ │ Description    │
│ [SHOP NOW]     │ │ [SHOP NOW]     │
└────────────────┘ └────────────────┘
```

Cards should not have unnecessary borders.

---

# 14. Horizontal Carousel

The reference uses horizontal carousel sections for campaigns and collections.

Desktop:

```text
[ CARD ][ CARD ][ CARD ][ CARD ]
                         → scroll
```

Mobile:

```text
[ CARD ]
        [ partial CARD ]
```

The next card should be partially visible on mobile to communicate horizontal scrolling.

Controls:

```text
Previous
Next
```

Keyboard navigation must work.

Accessibility:

```text
aria-label
keyboard support
visible focus state
```

Do not rely exclusively on drag gestures.

---

# 15. Product Grid

Product browsing uses a denser structure than marketing content.

Desktop:

```text
4 columns
```

Large desktop:

```text
4 - 5 columns
```

Tablet:

```text
3 columns
```

Mobile:

```text
2 columns
```

Example:

```text
┌───────┐ ┌───────┐ ┌───────┐ ┌───────┐
│ IMAGE │ │ IMAGE │ │ IMAGE │ │ IMAGE │
│       │ │       │ │       │ │       │
├───────┤ ├───────┤ ├───────┤ ├───────┤
│ Name  │ │ Name  │ │ Name  │ │ Name  │
│ Price │ │ Price │ │ Price │ │ Price │
└───────┘ └───────┘ └───────┘ └───────┘
```

---

# 16. Product Cards

## Image

Product imagery should have a neutral visual environment.

Recommended:

```text
background: #F3F3F3
aspect-ratio: 1 / 1
object-fit: cover
```

or:

```text
aspect-ratio: 4 / 5
```

depending on photography.

## Product Information

Order:

```text
Product name
Category / collection
Price
Color
Optional promotional badge
```

Typography:

```text
name: 14px - 16px
metadata: 12px - 14px
price: 14px - 16px
```

Avoid excessive information.

---

# 17. Product Image Hover

Desktop hover can reveal:

```text
alternate image
quick action
favorite
```

Transition:

```text
150ms - 250ms
ease-out
```

Do not use exaggerated animation.

---

# 18. Badges

Badges should be small and functional.

Examples:

```text
NEW
SALE
BEST SELLER
LIMITED
```

Avoid giant colorful pills.

Preferred:

```text
font-size: 11px - 12px
font-weight: 700
text-transform: uppercase
```

---

# 19. Filters

Product listing pages require a filtering system.

Observed product browsing includes filtering dimensions such as:

```text
Size
Gender
Product Type
Color
Sport
Activity
Style
Collection
```

The Adidas browse experience exposes a large filter taxonomy and sorting controls.

Desktop:

```text
LEFT SIDEBAR
```

Mobile:

```text
FILTER & SORT
```

Suggested structure:

```text
FILTERS
────────────
Gender
Product Type
Size
Color
Sport
Collection
Price
```

Each filter group should be collapsible.

---

# 20. Sort

Recommended sorting options:

```text
Newest
Top Sellers
Price: Low to High
Price: High to Low
```

The reference exposes similar sorting behavior.

Sort control should remain visually secondary to the product grid.

---

# 21. Buttons

## Primary Button

Visual style:

```text
background: #000000
color: #FFFFFF
border-radius: 0px - 2px
```

Height:

```text
44px - 52px
```

Padding:

```text
16px - 24px
```

Font:

```text
14px - 16px
font-weight: 700
```

Example:

```text
SHOP NOW
```

Do not use oversized rounded SaaS buttons.

---

# 22. Secondary Button

Default:

```text
background: #FFFFFF
color: #000000
border: 1px solid #000000
```

Hover:

```text
background: #000000
color: #FFFFFF
```

---

# 23. Link Buttons

For low-priority actions:

```text
text link
underline on hover
```

Do not convert every link into a button.

---

# 24. Border Radius

The visual language is predominantly sharp.

Preferred:

```text
0px
2px
4px
```

Avoid:

```text
12px
16px
24px
9999px
```

unless the component specifically requires a circular control.

The system should not look like a rounded SaaS dashboard.

---

# 25. Shadows

Use shadows sparingly.

Default:

```text
none
```

For floating menus:

```css
box-shadow:
  0 4px 20px rgba(0, 0, 0, 0.12);
```

For elevated overlays:

```text
soft
large
low-opacity
```

Never use heavy card shadows everywhere.

---

# 26. Cards

Marketing cards:

```text
border: none
background: transparent
radius: 0
```

Product cards:

```text
border: none
background: transparent
```

Interactive cards should rely on:

```text
image
spacing
typography
hover
```

rather than borders and shadows.

---

# 27. Editorial Sections

Editorial content should feel spacious.

Structure:

```text
SECTION LABEL

Large Heading

Short supporting paragraph

[IMAGE / CONTENT GRID]
```

Keep body copy constrained:

```text
max-width: 650px
```

Avoid full-width paragraphs.

---

# 28. Related Resources

The reference homepage includes a related-resource section containing editorial links and guides.

Use:

```text
article title
short description
optional category
```

Grid:

```text
3 - 4 columns desktop
1 - 2 columns mobile
```

Visual style:

```text
minimal
typography-led
high whitespace
```

---

# 29. Category Link Sections

Use simple grouped links.

Example:

```text
SEASONAL FOOTWEAR

Loafers
Mules & Clogs
Boots
Suede Shoes
Brown Shoes
```

The reference uses several grouped category collections near the lower portion of the homepage.

Do not render these as cards unless the content requires imagery.

---

# 30. Footer

Footer should be information-dense but organized.

Recommended structure:

```text
PRODUCTS
Sports
Collections
Support
Company
Social
```

Desktop:

```text
4 - 6 columns
```

Mobile:

```text
accordion sections
```

The Adidas footer groups navigation into products, sports, collections, support, company information, and social/legal areas.

---

# 31. Footer Typography

Heading:

```text
12px - 14px
font-weight: 700
text-transform: uppercase
```

Links:

```text
13px - 15px
line-height: 1.8
```

Footer should be dense but readable.

---

# 32. Membership / CTA Strip

A membership CTA may appear above the footer.

Pattern:

```text
┌─────────────────────────────────────────┐
│ JOIN OUR COMMUNITY                      │
│                                         │
│ Get exclusive benefits.                 │
│                                         │
│ [SIGN UP]                               │
└─────────────────────────────────────────┘
```

Use strong contrast.

Do not overdecorate.

---

# 33. Responsive Design

## Desktop

Breakpoint:

```text
≥ 1024px
```

Characteristics:

```text
full navigation
multi-column grids
large hero
visible filters
horizontal carousels
expanded footer
```

## Tablet

Breakpoint:

```text
768px - 1023px
```

Characteristics:

```text
reduced navigation
3-column grids
smaller hero
condensed spacing
```

## Mobile

Breakpoint:

```text
< 768px
```

Characteristics:

```text
mobile navigation
2-column product grid
horizontal campaign cards
stacked editorial sections
compact typography
mobile filter/sort controls
```

---

# 34. Mobile Navigation

Recommended:

```text
[MENU] [BRAND] [SEARCH] [BAG]
```

Menu should open a full-height or large overlay.

Categories:

```text
MEN
WOMEN
KIDS
SALE
GIFTS
SPORTS
NEW & TRENDING
```

Use large touch targets.

Minimum:

```text
44px
```

---

# 35. Mobile Hero

Avoid excessively tall heroes.

Recommended:

```text
aspect-ratio: 4 / 5
```

or:

```text
min-height: 520px
```

depending on content.

Text should remain readable without covering the entire image.

---

# 36. Motion

Motion should communicate state.

Use:

```text
150ms
200ms
250ms
```

Preferred easing:

```text
ease-out
```

Use animation for:

```text
hover
menu opening
drawer
carousel
image transition
filter changes
```

Avoid:

```text
bouncy animations
large transforms
constant floating elements
scroll-jacking
```

The site should feel fast.

---

# 37. Hover Behavior

Links:

```text
underline
color shift
```

Buttons:

```text
background inversion
```

Images:

```text
alternate image / subtle scale
```

Cards:

```text
small visual response
```

Do not use large scale transforms.

---

# 38. Focus States

Keyboard navigation must remain obvious.

Use:

```css
outline: 2px solid #000;
outline-offset: 2px;
```

On dark backgrounds:

```css
outline-color: #fff;
```

Never remove focus indicators without replacement.

---

# 39. Loading States

Use restrained skeletons.

Example:

```text
┌──────────────┐
│              │
│   SKELETON   │
│              │
└──────────────┘
██████████████
████████
```

Avoid animated rainbow placeholders.

Keep skeleton motion subtle.

---

# 40. Empty States

Use concise messaging.

Structure:

```text
No products found.

Try adjusting your filters.

[RESET FILTERS]
```

Do not display unnecessary illustrations unless they serve the brand.

---

# 41. Error States

Errors should be direct.

```text
Something went wrong.

Please try again.

[TRY AGAIN]
```

Do not expose technical details to users.

---

# 42. Accessibility

Requirements:

- semantic HTML
- keyboard navigation
- visible focus states
- alt text for meaningful imagery
- decorative images use empty alt
- minimum 44px interactive target
- sufficient color contrast
- accessible carousel controls
- accessible dialogs
- accessible filter controls
- screen-reader labels for icon buttons

Never rely solely on color to communicate state.

---

# 43. Image Rules

Images are a major part of the visual language.

Preferred characteristics:

```text
high resolution
strong composition
clear subject
editorial photography
controlled cropping
```

Use:

```css
object-fit: cover;
```

for campaign imagery.

Product imagery should generally use:

```css
object-fit: cover;
```

or:

```css
object-fit: contain;
```

depending on the product photography.

Never distort product images.

---

# 44. Content Hierarchy

Every major section should answer one question.

Examples:

```text
What is new?
What should I buy?
What collection is featured?
What sport is relevant?
What products are popular?
What should I explore next?
```

Avoid sections that exist purely because there was empty space.

---

# 45. Homepage Composition

Recommended homepage sequence:

```text
UTILITY BAR
↓
PRIMARY NAV
↓
HERO / LATEST DROP
↓
FEATURED COLLECTION
↓
PRODUCT / CATEGORY CAROUSEL
↓
CAMPAIGN GRID
↓
EDITORIAL CONTENT
↓
CATEGORY LINKS
↓
BRAND / SEO CONTENT
↓
MEMBERSHIP CTA
↓
FOOTER
```

The reference homepage follows a similar marketing-heavy composition, with latest drops, collection sections, holiday/campaign content, resources, category links, long-form brand content and footer navigation.

---

# 46. Product Listing Composition

```text
UTILITY BAR
↓
PRIMARY NAV
↓
PAGE TITLE
↓
CATEGORY / GENDER NAVIGATION
↓
FILTER + SORT BAR
↓
PRODUCT GRID
↓
LOAD MORE / PAGINATION
↓
RELATED CONTENT
↓
FOOTER
```

---

# 47. Product Detail Composition

Recommended:

```text
BREADCRUMB
↓
PRODUCT IMAGE GALLERY       PRODUCT INFORMATION
                           PRODUCT NAME
                           PRICE
                           RATING
                           COLOR
                           SIZE
                           SIZE GUIDE
                           ADD TO BAG
                           FAVORITE
                           DELIVERY INFO
↓
PRODUCT DESCRIPTION
↓
FEATURES / TECHNOLOGY
↓
RELATED PRODUCTS
↓
RECENTLY VIEWED
↓
FOOTER
```

Desktop should use a two-column composition.

Mobile should stack content naturally.

---

# 48. Design Tokens

Recommended CSS variables:

```css
:root {
  --color-black: #000;
  --color-white: #fff;

  --color-gray-50: #f7f7f7;
  --color-gray-100: #f3f3f3;
  --color-gray-200: #e6e6e6;
  --color-gray-300: #d1d1d1;
  --color-gray-400: #999;
  --color-gray-500: #666;
  --color-gray-600: #444;
  --color-gray-700: #2b2b2b;
  --color-gray-800: #171717;

  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-5: 24px;
  --space-6: 32px;
  --space-7: 40px;
  --space-8: 48px;
  --space-9: 64px;
  --space-10: 80px;
  --space-11: 96px;
  --space-12: 120px;

  --radius-none: 0px;
  --radius-sm: 2px;
  --radius-md: 4px;
  --radius-full: 9999px;

  --shadow-none: none;
  --shadow-floating:
    0 4px 20px rgba(0, 0, 0, 0.12);

  --transition-fast: 150ms;
  --transition-normal: 200ms;
  --transition-slow: 300ms;
}
```

---

# 49. Tailwind Mapping

If Tailwind CSS is used, prefer the design system over arbitrary values.

Example:

```tsx
<section className="w-full px-4 md:px-8 lg:px-12">
```

Instead of:

```tsx
<section className="px-[37px]">
```

Use:

```text
gap-4
gap-6
gap-8
gap-12
gap-16
```

instead of arbitrary spacing wherever possible.

---

# 50. Component Naming

Recommended components:

```text
SiteHeader
UtilityBar
PrimaryNav
MobileNav
SearchButton
SearchOverlay

HeroSection
CampaignCard
CampaignGrid
ContentCarousel
SectionHeader

ProductCard
ProductGrid
ProductImage
ProductMeta
ProductBadge
ProductPrice

FilterSidebar
FilterDrawer
SortMenu

EditorialCard
EditorialGrid

CategoryLinks
MembershipBanner
SiteFooter
FooterColumn
```

Keep components composable.

---

# 51. Component Philosophy

Do not create one giant component.

Bad:

```text
Homepage.tsx
```

containing 2000 lines of markup.

Prefer:

```text
Homepage
├── Header
├── Hero
├── FeaturedCollection
├── CampaignGrid
├── ProductCarousel
├── EditorialSection
├── CategoryLinks
├── MembershipCTA
└── Footer
```

---

# 52. UI Density Rules

## Marketing

```text
large
airy
image-heavy
```

## Ecommerce

```text
medium
structured
information-rich
```

## Navigation

```text
compact
fast
minimal
```

Do not apply one density level to the entire website.

---

# 53. What NOT To Do

Do NOT introduce:

```text
huge rounded cards
glassmorphism
gradient-heavy backgrounds
neon gradients
excessive shadows
floating blobs
random decorative shapes
oversized dashboard widgets
excessive pill buttons
generic SaaS layouts
```

Avoid visual patterns associated with generic AI-generated websites.

Do not use:

```text
purple-blue gradient
Inter + giant rounded cards + glass blur
```

as a default design language.

That combination has been beaten into the ground by the internet.

---

# 54. Anti-Slop Rules

The implementation must avoid:

### 1. Random spacing

All spacing must follow the defined scale.

### 2. Excessive rounding

Prefer sharp geometry.

### 3. Excessive cards

Not every section needs a card.

### 4. Fake depth

Do not add shadows simply to make elements "look premium".

### 5. Excessive animation

Motion must have a purpose.

### 6. Visual clutter

Every element must have a hierarchy.

### 7. Generic AI aesthetics

The result should not look like:

```text
AI Dashboard Template #4837
```

### 8. Overly narrow layouts

Large desktop screens should use available horizontal space.

### 9. Excessive text

Commerce interfaces communicate visually.

### 10. Inconsistent components

Buttons, typography, spacing and interaction patterns must remain consistent.

---

# 55. Reference Fidelity Priority

When implementing this design language, prioritize in this order:

```text
1. Layout proportions
2. Typography hierarchy
3. Image scale
4. Spacing
5. Navigation structure
6. Grid behavior
7. Component geometry
8. Color
9. Motion
10. Decorative details
```

Do not spend time perfecting shadows while the page geometry is wrong.

---

# 56. Agent Implementation Rules

When an AI coding agent reads this file:

1. Read this file before modifying UI.
2. Inspect the existing project architecture.
3. Reuse existing components when possible.
4. Do not rewrite working functionality just to change styling.
5. Preserve existing business logic.
6. Implement responsive behavior.
7. Prefer CSS/Tailwind tokens over arbitrary values.
8. Avoid unnecessary dependencies.
9. Do not introduce generic AI design patterns.
10. Verify the final implementation visually.

---

# 57. Visual QA Checklist

Before considering a UI implementation complete:

## Desktop

- [ ] Header proportions feel correct
- [ ] Navigation is compact
- [ ] Hero uses the majority of available width
- [ ] Typography hierarchy is obvious
- [ ] Grid is not cramped
- [ ] Cards are not excessively rounded
- [ ] Sections have intentional spacing
- [ ] Images are large enough
- [ ] Footer is organized

## Mobile

- [ ] Navigation is usable
- [ ] Hero remains visually strong
- [ ] Product grid uses 2 columns where appropriate
- [ ] Horizontal carousels remain usable
- [ ] Text does not overflow
- [ ] Buttons have adequate touch targets
- [ ] Filters are accessible
- [ ] Footer collapses cleanly

---

# 58. Final Visual Target

The final product should feel like:

```text
SPORTSWEAR
+
EDITORIAL
+
PREMIUM ECOMMERCE
+
MINIMAL UI
+
STRONG TYPOGRAPHY
+
LARGE IMAGERY
```

Not:

```text
GENERIC DASHBOARD
+
ROUNDED CARDS
+
GRADIENTS
+
AI SLOP
```

The visual identity should remain bold, monochrome-first, image-led, editorial, and commercially focused.

---

# 59. Source Reference

Primary reference:

```text
https://www.adidas.com/us
```

The reference was analyzed for:

- global navigation
- utility navigation
- homepage composition
- hero/campaign structure
- carousel behavior
- product/category organization
- filter taxonomy
- editorial content
- footer information architecture
- responsive design principles

The live reference currently exposes major navigation categories including MEN, WOMEN, KIDS, SALE, GIFTS, SPORTS, and NEW & TRENDING, followed by search and bag interactions.

The homepage also contains campaign/carousel sections, editorial resources, category-link groups, long-form brand content, membership CTA, and a multi-column footer.

---

# 60. Source-of-Truth Rule

When this document conflicts with an existing project convention:

```text
Existing functionality > Design system
Design system > arbitrary styling decisions
```

Never break working functionality merely to reproduce visual styling.

The goal is to reproduce the **design principles and visual behavior**, not to clone the source website.