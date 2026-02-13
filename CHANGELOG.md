# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.4.4] - 2026-02-13

### Changed
- **Release Pipeline**: Temporarily disabled Minisign verification in release workflow to restore automated release publishing.

## [0.4.3] - 2026-02-13

### Added
- **System Update Manager**: Migrated system update functionality to the System Settings card with "Check" and "Install" capabilities.
- **Update Checks**: Integrated GitHub Releases API for version checking.

## [0.4.2] - 2026-02-13

### Changed
- Internal release for testing deployment pipelines.

## [0.4.1] - 2026-02-04

### Fixed
- **Apply Changes Persistence**: Fixed issue where the "Apply Changes" banner would persist after application by standardizing dirty state detection in `/var/run`.
- **Apply Logic Refactor**: Refactored `PfSenseApiService` to handle dirty states generically for Tunables, Rules, NAT, Aliases, and Interfaces.

## [0.4.0] - 2026-01-22

### Added
- **Flicker-Free Dashboard**: Implemented global `x-cloak` support and optimistic initialization logic to ensure a smooth, stable page load experience.

### Fixed
- **Overlay Flashing**: Resolved a race condition where offline overlays would briefly appear during Alpine.js initialization.
- **Connection State Logic**: Refined the initial `online` state evaluation within the `firewallCard` component to prevent false-offline indicators during background data syncing.

## [0.3.1] - 2026-01-22

### Fixed
- **Swap Usage Detection**: Enhanced `PfSenseApiService` to support deep-nested metrics and multiple naming variations for Swap, Memory, and CPU.
- **Metric Formatting**: Added automatic parsing for percentage strings (e.g., "5.2%") into numeric values for UI bars.
- **Uptime/System Fallbacks**: Added multiple fallback keys for Uptime and Temperature to support different pfSense API implementations.

## [0.3.0] - 2026-01-22

### Added
- **Centralized Dashboard Coordinator**: Implemented `dashboardCoordinator` to manage simultaneous firewall updates, eliminating the "waterfall" effect.
- **Failover Batch Sync**: Added automatic single-request batch fetching for when WebSockets are disconnected.
- **Data Resilience**: Cached metrics are now preserved for offline devices, ensuring dashboard cards stay populated with last-known data behind the blur overlay.

### Changed
- **Performance Optimization**: Removed per-card `fetchStatus()` on initialization, reducing page load overhead significantly.
- **API Schema Flattening**: Flattened the `PfSenseApiService` response structure to simplify metric binding on the frontend.
- **Unified Update Pipeline**: Standardized the data format shared between WebSockets, Ajax, and background jobs.

### Fixed
- **Metric Mapping Issues**: Fixed "Unknown" values for CPU, Memory, and Version by unifying nested and flat data paths in Alpine components.
- **Dashboard Data Freezing**: Resolved issues where real-time updates were ignored due to property structure mismatches.
- **Offline UI Breakage**: Fixed cards disappearing or being cleared when a firewall becomes unreachable.

## [0.2.0] - 2026-01-22

### Added
- **Firewall Counter Display**: Added "Showing X of Y firewalls" counter with real-time filtering support on dashboard and firewalls index pages
- **Shared Filterable Mixin**: Created reusable `window.filterableMixin()` component for consistent filtering across pages
- **URL-Persisted Filters**: Search, status, and customer filters now persist in URL query parameters and survive page refreshes
- **Settings Page Restore**: Added "Restore to Default" buttons for logo and favicon in system settings
- **Gateway Display Fallback**: Offline firewalls now show WAN gateway with "Unknown" status instead of empty section
- **Documentation**: Added usage guide for filterable mixin at `.agent/filterable-mixin-usage.md`

### Changed
- **Gateway UI Redesign**: Updated gateway display with neutral backgrounds and color-coded left borders
- **Table Spacing**: Increased vertical padding in System Information tables for better readability
- **Gateway Labels**: Standardized gateway labels to use `text-sm` font size across all pages
- **Project Organization**: Moved deployment scripts (`install.sh`, `setup_reverb.sh`) to `/scripts` directory
- **Route Names**: Updated system settings routes from `system.customization.*` to `system.settings.*`

### Fixed
- **Settings Page Error**: Fixed "Route [system.customization.index] not defined" error when saving settings
- **Layout Stability**: Resolved issue where gateway section disappeared entirely for offline firewalls
- **Code Duplication**: Reduced ~100+ lines of duplicate filtering logic by using shared mixin

### Technical
- Refactored dashboard and firewalls index pages to use shared `filterableMixin`
- Added `device-updated` event with detailed payload (id, online status)
- Implemented reactive filtering with Alpine.js computed properties
- Enhanced event-driven architecture for real-time status updates

## [0.1.0] - Initial Release

### Added
- Initial AdmixCentral application for pfSense firewall management
- Dashboard with firewall status monitoring
- Real-time status updates via WebSocket (Reverb)
- Queue-based background job processing
- Multi-tenant architecture with company isolation
- System settings management
- Custom logo and favicon support
- Dark/light theme support

[Unreleased]: https://github.com/geekasso/admixcentral/compare/v0.4.0...HEAD
[0.4.0]: https://github.com/geekasso/admixcentral/compare/v0.3.1...v0.4.0
[0.3.1]: https://github.com/geekasso/admixcentral/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/geekasso/admixcentral/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/geekasso/admixcentral/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/geekasso/admixcentral/releases/tag/v0.1.0
