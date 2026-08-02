# HTML Head Code

A simple [Jyavani CMS](https://jyavani.com/) plugin that lets admins paste custom HTML, scripts, or meta tags into the public `<head>` via a CodeMirror editor.

## Use cases

- Meta Pixel base code
- Google Site Verification meta tag
- Custom analytics scripts
- Any other snippet that belongs in `<head>`

## Requirements

- Jyavani CMS ≥ 2.1.3
- PHP ≥ 8.1
- `pdo` and `json` extensions

## Installation

1. Download the plugin as a ZIP from GitHub or clone this repo:
   ```bash
   git clone https://github.com/adammuizweb/html-head-code.git
   ```
2. Upload the plugin through **Dashboard → Plugins → Upload Plugin**.
3. Activate it.
4. Go to **Tools → HTML Head Code** and paste your code.

## How it works

The plugin stores the snippet in the CMS settings table and outputs it through the `jy_head` action, right before `</head>`.

## Security

- Admin-only access.
- CSRF token validation on save.
- The input is output raw (it is meant to be HTML/JS), so only trusted admins should be allowed to use it.

## License

This plugin is provided as-is for Jyavani CMS users.
