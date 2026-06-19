# Assets Folder Structure

```
public/assets/
├── vendor/
│   └── jquery/
│       └── jquery-3.6.0.min.js    (Downloaded from jQuery CDN)
├── css/
│   ├── app.css                    (Custom styles)
│   └── ...
└── js/
    ├── app.js                     (Main app script)
    ├── pages/
    │   ├── admin.js               (Admin page logic)
    │   ├── exam.js                (Exam page logic)
    │   └── ...
    └── ...
```

## Vendor Libraries

### jQuery
- **Version:** 3.6.0
- **Size:** ~88KB (minified)
- **Location:** `vendor/jquery/jquery-3.6.0.min.js`
- **CDN Source:** https://code.jquery.com/

## Usage

Include jQuery in your blade template:

```blade
<script src="{{ asset('assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
```

## Adding More Vendors

To add new vendor libraries:

1. Create a folder in `vendor/` with the library name
2. Download the library files
3. Include in your blade using `asset()` helper
4. Update this README

## CDN vs Local

**Current Setup:** Local (offline support, faster for repeated visits)

**Pros:**
- ✅ Works offline
- ✅ Faster on repeat visits (cached)
- ✅ No external dependencies
- ✅ Full control over versions

**Cons:**
- ❌ Slightly larger initial load (cached anyway)
- ❌ Need to manage updates manually

Generated: 2026-06-10
