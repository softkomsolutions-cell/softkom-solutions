# Direct WordPress deployment and WPForms repair

This branch is preparation only. **Do not deploy the existing theme from this repository over production.** The live WordPress theme has diverged from GitHub: the current live homepage includes WPForms form 2501, while this branch's homepage template does not.

## Safe migration

1. Back up production database and `wp-content/themes/softkom-v3`, including media and plugins. Verify restoration access.
2. Export the **current active theme** and separately the WPVibe draft theme from WordPress hosting using SFTP/SSH or the hosting file manager. Never commit passwords, `wp-config.php`, uploads, or database dumps.
3. Compare each exported theme against the repository. Import the intended current theme into a **new branch**, and review the differences before modifying any files.
4. Identify the real form asset handle and dependencies from the installed WPForms Lite version, then enqueue its frontend assets for pages that render `[wpforms id="2501"]` through PHP templates. Verify `wp_head()` and `wp_footer()` are called and that no optimization or dequeue hooks remove the assets.
5. Verify the browser loads required scripts, there are no JavaScript console errors, client-side validation works, a real test submission succeeds, and the notification arrives at `info@softkomsolutions.com`.
6. Test mobile and desktop visual parity, content, navigation, CTA and conversion tracking on staging. Publish only after explicit approval and successful backup.

## Current evidence

- WPForms global assets setting was set to `1` in WordPress.
- The WPVibe draft had jQuery and `wpforms` enqueue calls added, but their effectiveness was not verified.
- Earlier HTML inspections showed no jQuery or WPForms core frontend script. The HTML inspection alone does not prove delivery failure; a browser submission and mailbox check are required.
- Existing GitHub code is **not** an authoritative copy of the current WordPress draft.

## Deployment prerequisite

Obtain authorized SFTP/SSH or hosting file-manager access, or an exported zip of both current theme directories. Avoid WPVibe for this workflow. No production changes should be made by this branch alone.
