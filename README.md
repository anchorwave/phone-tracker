Update Checklist
-------------
Pushing updates through github may fail without the following:

+ Update Version Number in `twillio.php`
+ Update Version Number in `README.md`
+ Update `WP_GItHub_Updater` `$config['tested']` to current version of WordPress plugin was tested on.*
 
**not sure if this is actually required but jic do it*

Optional:
+ Update `$config['requires']` to required version of WordPress

*This is for for GitHub Updater* *Do not delete*

~Current Version:1.0.10~

#### changelog

**v1.0.10**
: Fixed some weird verbiage in various admin notices
: Changed **deactivation** hook to run on **uninstall**. SID and auth token get deleted when the plugin is deleted through wordpress, rather than on deactivation of the plugin.
: Reduced minor php errors
: Made Auth Token field a password input (note: this is not a security fix)

**v1.0.9**
: Changed verbiage -- "Inactivate" to "Deactivate"
: Added sanitization to new phone numbers. Spaces and non-numeric values will no longer be saved

**v1.0.8**
: Fixed WP_Github_Updater clash, now compatible with phone-number-swappy 1.1.10

**v1.0.7**
: Fixed incorrect index error in `twilio.php`

**v1.0.0 .. 6**
: Added update functionallity... various fixes throughout.