# DTeach (`local_dteach`)

Connects Moodle to DTeach through LTI 1.3. Teachers add DTeach from the activity
chooser, and their students open it with their Moodle account.

DTeach is a hosted service: this plugin needs a DTeach licence key to connect a site.
Write to <contact@dteach.net> to get one.

## Install

Drop this repository into `local/dteach/` of your Moodle install (`public/local/dteach/`
from Moodle 5.1), then visit *Site administration → Notifications*.

## Set up

*Site administration → Plugins → Local plugins → DTeach → Connection*, then paste
the licence key DTeach sent you. The page walks the LMS through registration and
shows the plan afterwards.

Nothing else to configure. LTI Dynamic Registration always leaves a tool pending,
shown only as a preconfigured tool and launched in an embed; the plugin activates
it, puts it in the activity chooser and switches it to a new window afterwards. It
touches nothing else on the tool: course category restrictions and other settings you
make on it are kept.

## Requirements

Moodle 4.2 or later, with LTI 1.3 Dynamic Registration.

## Licence key

The plugin is GPL v3, but DTeach itself is a hosted service. Write to
<contact@dteach.net> for a licence key. One key opens one site. When the plan ends,
teachers can no longer open DTeach and the tool leaves the activity chooser; the
activities already in a course are kept.

## Privacy

The plugin itself stores no personal data. To connect your site, it sends DTeach the
address of your site, and either its licence key or the email address of the
administrator who connected it for free.

When a user opens a DTeach activity, Moodle's External tool (LTI) module sends DTeach
launch data about them: an identifier, and depending on the privacy settings of the
DTeach tool (*Site administration → Plugins → Activity modules → External tool →
Manage tools*), their name, email address, role and course. The plugin does not send
this data itself; the External tool module does, and declares it in Moodle's privacy
registry. DTeach handles it as described in its privacy policy:
<https://dteach.net/privacy>.

## Support

Bugs and feature requests: <https://github.com/Shyqi/moodle-local_dteach/issues>.
Anything else: <contact@dteach.net>.

## License

GNU GPL v3 or later. See [LICENSE](LICENSE).
