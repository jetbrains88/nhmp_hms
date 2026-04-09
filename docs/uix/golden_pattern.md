# 🌟 HMS Golden Pattern: UI/UX Specification

This document defines the **"Golden Pattern"** for the Hospital Management System—a design language intended to create a premium, futuristic, and highly standardized user experience across all modules.

---

## 🏗️ 1. Core Layout Architecture

### 🔲 The Bento Grid System

- **Cards**: Use `bg-white`, `rounded-3xl` (or `rounded-[2rem]` for main panels), and `shadow-xl`.
- **Borders**: Subtle `border-slate-100` or `border-indigo-50/50`.
- **Spacing**: Generous `p-6` or `p-8`. Use `gap-6` for grids.

### 📐 Structural Layout

- **Main Panels**: Left column spans `lg:col-span-9`, Right Sidebar (Filters) spans `lg:col-span-3`.
- **Sticky Sidebars**: Sidebars should be `sticky top-8` with `max-h-[calc(100vh-100px)]` and `overflow-y-auto`.
- **Floating Controls**: Use fixed floating buttons (e.g., Filter Toggle) with `backdrop-blur` and `shadow-glow`.

---

## 🎨 2. Color System & Aesthetics

### 💎 Primary Palette

- **Professional**: `blue-600` for primary actions, `indigo-600` for deep focus.
- **Success**: `emerald-500` for active status, stock in range, or completed tasks.
- **Critical**: `rose-500` for low stock, "Rx Required", or expired items.
- **Warning**: `amber-500` for global items or pending approvals.
- **Muted**: `slate-400` for secondary labels, `slate-50` for background sections.

### ✨ Visual Effects

- **Gradients**: Subtle linear gradients (e.g., `from-blue-50 to-indigo-50`) for headers.
- **Glow Shadows**: Use color-tinted shadows for emphasis: `shadow-blue-500/20` or `shadow-rose-500/20`.
- **Glassmorphism**: Use `backdrop-blur-sm` or `backdrop-blur-[2px]` for overlays and floating panels.

---

## 🔠 3. Typography & Micro-Interactions

### ✍️ Font Standards

- **Font Family**: `Outfit` (Primary) + `Mono` for numbers/codes.
- **Header Weights**: `font-black` or `font-extrabold` for titles and pill labels.
- **Tracking**: Extreme `tracking-[0.2em]` or `tracking-widest` for all-caps labels.
- **Dual Labeling**: Combine a large `font-black` title with a small `font-black tracking-widest uppercase` sub-label.

### ⚡ Animation & Hover States

- **Hover Scale**: `hover:-translate-y-1` and `hover:scale-[1.02]` for cards.
- **Interactions**: `active:scale-95` for all buttons.
- **Transitions**: `transition-all duration-300` on every interactive element.

---

## 📦 4. Standard Components

### 🔘 Quick Intelligence Buttons (Filter Pills)

- **Layout**: Balanced grid (`grid-cols-2 gap-3` for sidebars).
- **Style**: Pill-shaped (`rounded-xl` or `rounded-2xl`), uppercase text, specific icon for each category.
- **State**: Clear differentiation between "Active" (solid color + shadow) and "Inactive" (light slate bg + thin border).

### 🛠️ View Management

- **Dual View Toggle**: **Required** for every module that offers both "Table/List" and "Grid/Card" views.
  - **Structure**: A small `p-1` container with `rounded-xl`, `bg-white`, and `border-indigo-100`.
  - **Buttons**: Two `w-9 h-9` buttons with `rounded-lg` and `transition-all`.
  - **Active State**: Use `bg-indigo-600 text-white shadow-md` for the active mode.
  - **Inactive State**: Use `text-slate-400 hover:text-indigo-600`.
- **Switchers**: Premium toggle for `List View` vs `Card/Grid View` (as defined above).
- **Density**: Local `condensed` vs `spacious` toggle for tables.
- **Status Toggles**: Standardized Alpine.js switches with vibrant color transitions (Emerald for ON, Rose for OFF). 
  - **Predictive Interaction**: Must include `@mouseenter`/`@mouseleave` logic to show the target action (e.g., "Active" → hover → "Hide").

### ⏳ Loading & Feedback

- **Overlays**: Full-panel `backdrop-blur-[2px]`.
- **Spinners**: Modern spinning rings with high-intensity secondary colors.
- **Labels**: "Syncing Data...", "Refining Search...", "Processing...".

---

---

## 💎 5. Premium Components (The Standard)

### 📈 5.1 Stats Cards (Floating Icon Model)
- **Container**: `relative flex flex-col bg-gradient-to-br from-[color]-50 to-[color]-100 rounded-2xl shadow-lg border border-[color]-200`.
- **Icon**: `absolute -top-6 left-4 h-14 w-14 rounded-xl bg-gradient-to-tr from-[color]-600 to-[color]-400 shadow-lg shadow-[color]-900/20 border border-[color]-300`.
- **Interactivity**: `hover:-translate-y-2 transition-all duration-300 group cursor-pointer`.
- **Feedback**: A bottom-border indicator with a `h-1.5 w-1.5 rounded-full animate-pulse` dot.

### 🔄 5.2 Status Toggles (Predictive)
- **Logic**: Use `x-data="{ hover: false }"` to track interaction.
- **Labels**: 
    - `is_active && !hover`: "Active"
    - `is_active && hover`: "Hide" or "Deactivate"
    - `!is_active && !hover`: "Hidden" or "Inactive"
    - `!is_active && hover`: "Show" or "Activate"

---

## 💎 6. Final Details (The "Wow" Factor)
