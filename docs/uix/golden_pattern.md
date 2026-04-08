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

- **Switchers**: Premium toggle for `List View` vs `Card/Grid View`.
- **Density**: Local `condensed` vs `spacious` toggle for tables.
- **Status Toggles**: Standardized Alpine.js switches with vibrant color transitions (Emerald for ON, Slate for OFF).

### ⏳ Loading & Feedback

- **Overlays**: Full-panel `backdrop-blur-[2px]`.
- **Spinners**: Modern spinning rings with high-intensity secondary colors.
- **Labels**: "Syncing Data...", "Refining Search...", "Processing...".

---

## 💎 5. Premium Details (The "Wow" Factor)

- **Icons**: Always wrapped in a container (e.g., `w-12 h-12 rounded-xl bg-blue-50 text-blue-600 shadow-inner`).
- **Footer**: Balanced, justified, and containing system metadata (Branch, Version, Server Status).
- **Placeholders**: Never use generic text; use `fas fa-search` or related imagery for empty states.
- **Scrollbars**: Custom thin, rounded scrollbars (`w-1`, `rounded-full`) that vanish when not in use.
