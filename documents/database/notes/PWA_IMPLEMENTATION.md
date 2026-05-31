# PWA Implementation Documentation - SMIS

This document outlines the technical implementation of Progressive Web App (PWA) features for the SMIS application.

## 1. Core Configuration

### Package Installation
The PWA was initialized using the official Angular PWA package:
```bash
ng add @angular/pwa --project esmis
```
This added `@angular/service-worker` and configured the necessary build steps in `angular.json`.

### Web Manifest (`public/manifest.webmanifest`)
The application is branded as **SMIS** with a primary maroon theme:
*   **Name / Short Name:** SMIS
*   **Theme Color:** `#800000`
*   **Display:** `standalone` (removes browser UI when installed)

### Icons
Placeholder icons are located in `public/icons/`. For final branding, these should be replaced with actual SMIS logos maintaining the original filenames and dimensions (72x72 to 512x512).

---

## 2. Key Features

### Background Update Notifications
A dedicated service handles version detection to ensure users always have the latest code without manual intervention.

*   **Service:** `src/services/update.service.ts`
*   **Logic:** Listens for `VERSION_READY` events from Angular's `SwUpdate`.
*   **UI:** Triggers a persistent toast using `ToastService` with a **Reload** button.
*   **Action:** Clicking reload calls `document.location.reload()` to activate the new version immediately.

### Offline Indicator
To provide feedback during connectivity loss, a full-screen overlay was implemented.

*   **Component:** Integrated into `src/app/app.ts` and `app.html`.
*   **Logic:** Tracks `navigator.onLine` status using `@HostListener('window:online')` and `window:offline`.
*   **UI:** A full-screen overlay styled similarly to the 404 page, featuring a `bi-wifi-off` icon and instructions to check internet connection.
*   **State Preservation:** The overlay uses `[class.d-none]` to hide/show, ensuring that user data and application state remain intact underneath.

---

## 3. Backend Support (CORS)

To support testing the PWA locally (which often runs on port `8080`), the Laravel backend was updated.

*   **File:** `backend/smis/config/cors.php`
*   **Change:** Added `http://localhost:8080` to the `allowed_origins` array.
*   **Important:** This configuration must be mirrored on the remote production server to allow PWA installations to communicate with the live API.

---

## 4. Development & Testing Workflow

PWA features (Service Workers) are **disabled by default in development mode**. To test:

1.  **Ensure Production API URL:** Verify `src/environments/environment.prod.ts` points to the correct backend.
2.  **Build:**
    ```bash
    npm run build
    ```
3.  **Serve:** Use a static server to serve the `dist/esmis/browser` directory.
    ```bash
    npx http-server -p 8080 -c-1 dist/esmis/browser
    ```
4.  **Clear Cache:** If changes don't appear, use DevTools -> Application -> Storage -> **Clear site data**.

---

## 5. Reverting Changes

The PWA implementation was performed across multiple commits on the `feat/pwa` branch. To revert the entire implementation to the last stable state before PWA work began:

```bash
git reset --hard 4f0e9f0
```
*(Note: 4f0e9f0 is the commit ID for "fix: correct navigation path for requesting new password link")*
