BOOK HUB — Static Export

What this folder contains
- `index.html`, `books.html`, `about.html`, `gallery.html`, `contact.html` — static pages that mirror the React UI.
- `styles.css` — compiled styles adapted from the app's Tailwind-based design.
- `scripts.js` — small vanilla JS for interactions (menu, search placeholder, contact form alert).

Notes about images
- Pages reference the original project images at `../src/assets/*.jpg` so you can preview locally without copying images.
- If you want a fully self-contained `static-site`, copy the files from `src/assets/` into `static-site/assets/` and update image paths (or keep the same names and place them in `static-site/assets/`).

Quick preview (PowerShell)

1. From project root run a simple static server (built-in Python):

```powershell
# start a simple HTTP server from repo root
python -m http.server 8000; Start-Process "http://localhost:8000/static-site/index.html"
```

2. Or open the file directly in your browser: open `static-site/index.html`.

Next steps (suggested)
- Optionally copy images from `src/assets/` into `static-site/assets/` for a portable package.
- Tweak `styles.css` or `index.html` content to match any visual refinements you want.
- Deploy `static-site/` to Netlify, Vercel (static), GitHub Pages, or any static host.

If you want, I can:
- Copy the images into `static-site/assets/` for you (I can't move binary files automatically here, but I can add placeholders or show commands). 
- Update `index.html` to reference `../src/assets/` consistently (if you'd prefer that). 

Tell me which of the above you want next.