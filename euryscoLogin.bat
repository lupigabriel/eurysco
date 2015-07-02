@echo off

setlocal enableextensions
cd /d "%~dp0"

set /p euryscoPort=<".\chromium\euryscoLogin.prt"
if exist ".\chromium\euryscogui.exe" start "euryscoLogin" ".\chromium\euryscogui.exe" --disable-legacy-window --disable-translate --no-proxy-server --no-service-autorun --no-network-profile-warning --no-message-box --no-managed-user-acknowledgment-check --no-first-run --no-experiments --no-events --no-default-browser-check --no-announcement --disable-save-password-bubble --disable-drop-sync-credential --disable-password-generation --disable-password-link --disable-password-manager-reauthentication --disable-infobars --disable-default-apps --ignore-certificate-errors --disable-search-button-in-omnibox --disable-plugins --disable-canvas-aa --disable-d3d11 --disable-direct-write --disable-gpu --disable-pepper-3d --disable-prefer-compositing-to-lcd-text --disable-settings-window --disable-text-blobs --disable-webgl --disable-webaudio --disallow-autofill-sync-credential --disable-3d-apis --disable-accelerated-2d-canvas --disable-harfbuzz-rendertext --app="https://localhost:%euryscoPort%"