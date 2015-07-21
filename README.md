Update Checklist
-------------
Pushing updates through github may fail without the following:

+ Update Version Number in `twillio.php`
+ Update Version Number in `README.md`
+ Update `WP_GItHub_Updater` `$config['tested']` to current version of WordPress plugin was tested on.*
 
**not sure if this is actually required but jic do it*

Optional:
+ Update `$config['requires']` to required version of WordPress

*This is for for GitHub Updater*

~Current Version:1.0.8~

#### changelog

**v1.0.8**
: Fixed WP_Github_Updater clash, now compatible with phone-number-swappy 1.1.10

**v1.0.7**
: Fixed incorrect index error in `twilio.php`

**v1.0.0 .. 6**
: Added update functionallity... various fixes throughout.