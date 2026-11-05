# Changelog

All notable changes to this plugin are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the plugin follows
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-11-02

### Added

- *Connect for free*: a site connects with the administrator's email address and no licence key. It gets 15 days with every feature, then stays free for up to 20 members.
- A licence key can be pasted on a site that is already connected, without registering again.
- The *Connection* page shows the days left of the trial, and how many of the free seats are taken.
- A reinstalled plugin gets its site's connection back on its own: the site signs for itself with its LTI platform key.

### Changed

- The registered tool is updated through the External tool API (`lti_update_type()`), and only when it launches on the DTeach service domain.
- The privacy provider declares the administrator email sent by the free connection, and the launch data the External tool module sends DTeach.

## [1.0.0] - 2026-10-13

First release.

### Added

- *Connection* admin page: paste the licence key DTeach sent you, and the site registers with DTeach through LTI 1.3 Dynamic Registration.
- The key is kept, so the page can register again on its own if DTeach drops a registration link that was never used.
- Readable messages for the refusals DTeach can answer with: unknown key, key already spent on another site, licence no longer active, site already connected.
- Automatic activation of the registered tool: Dynamic Registration always leaves it pending and hidden from the activity chooser, so the plugin finishes the job.
- *Reconnect* button when the LMS lost the tool: the site registers again onto its existing connection, so teachers keep their DTeach courses.
- Reinstalling the plugin on a connected site: the licence key brings back the site's connection instead of being refused.
- The tool leaves the activity chooser when the plan ends, and returns when a plan is running again. Only its state, chooser visibility and launch container are touched: course category restrictions and other tool settings stay as the administrator left them.

[1.0.0]: https://github.com/Shyqi/moodle-local_dteach/releases/tag/v1.0.0
