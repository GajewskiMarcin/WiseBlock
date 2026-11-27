# Changelog

All notable changes to this module are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the versioning follows
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.4.1] - 2026-11-27

### Changed

- **About & Help**: the "Support the Project" button links to Ko-fi, and its heading is
  centred like the rest of the block.

### Added

- **About & Help**: a Quince Project block with a link to the project on GitHub.
- Translations: 98 strings that existed only in Polish are now translated into all
  21 other languages.

### Fixed

- The Portuguese translation file was cut off in the middle of a line, which broke it
  with a PHP parse error. It is now complete.
- The package no longer ships `views/css/wiseblock-custom.css`. That file is written by
  the "Custom CSS" tool from the shop's own settings, and the copy in the package
  contained styles that do not belong to the module.
