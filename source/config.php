<?php

/*
Pronagement config default file build 20220425

The following php extension need to be enable in php.ini:
bz2,curl,fileinfo,gd or gd2,gettext,intl,mbstring,exif,mysqli,openssl,pdo_mysql,pdo_sqlite
After uncomment the line to enable those above, restart Apache.

Everything in this sea of codes ARE cAsE SENsitIVE.

Read carefully all comments before proceeding. Failure and success at your own risks.

Extra note: You might want to copy value you want to change and put it into config.user.php (you can create it manually within the same directory as config.default.php)
so that you don't have to revalue config.php everytime there is new build of Pronagement.
*/

//set time zone for this Pronagement installation
putenv("TZ=Asia/Kuala_Lumpur");//set time zone reference: https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
date_default_timezone_set('Asia/Kuala_Lumpur');//set php time zone reference: https://www.php.net/manual/en/timezones.php

//database connection properties
$dbhost = "localhost";
$dbname = "pronagement_db";
$dbuser = "insert-database-username-here";
$dbpass = "insert-database-password-here";

/*
AES KEY
=======
you will need to very careful and do not change the $password_aes_key mid way when using the system as your user not be able to login
unless you revert back to the old key. set this once and never again edit it.
default: 45C799DB3EBC65DFBC69A0F36F605E6CA2447CD519C50B7DA0D0D45D2B0F2431
*/
$password_aes_key = "45C799DB3EBC65DFBC69A0F36F605E6CA2447CD519C50B7DA0D0D45D2B0F2431";

//system title - give a title to your Pronagement installation
$system_title = "Pronagement";

/*
system url (mention the subdirectory, if applicable)
must begin with http:// or https:// and end with /
eg. https://myir.myuni.edu/myrepo/ (if subdirectory) or https://myir.myuni.edu/ (if no subdirectory)
please take note this value will affect many part in the system codes. please make sure it has a correct value.
*/
$system_path = "https://mydomain.institution.edu/";

//this server ip address (internal network ip only)
$system_ip = "10.10.1.11";

//admin contact disclaimer
$system_admin_contact_disclaimer = "If you have enquiries with this system, kindly contact us at <a href='mailto:contactus@gmail.com'>contactus@gmail.com</a> or 01-2345678";

//registered owner
$system_owner = "Pronagement - A Project Management Utility";

//logo and icons
//you may use custom folder(s) to store your own logos, images and icons
$main_logo = "pg_assets/images/company.png";//default is pg_assets/images/company.png
    $main_logo_width = "250px";//can be px or %
$browser_icon = "pg_assets/images/company_www-icon.png";
$menu_icon = "pg_assets/images/company_big-icon.png";

//intro words below the main logo / html5 enabled. you may use html tag on it to control size etc.
$intro_words = "<div style='margin-top:10px;font-size:12px;'>Welcome to Pronagement.</div>";

//full text document extension for uploading new item
$system_docs_directory = "files/docs";//directory without the leading and following / you have to make sure the directory is apache rewritable
$system_allow_document_extension = "pdf";
$system_allow_document_maxsize = "100";//in MB
$allow_guest_access_to_ft = false; //allow guest access to full text document

//project
$tier1_menu = "Project";
    $tier1_menu_plural = "Projects";
    $tier1_menu_description = "Description";
    $tier1_menu_additional_multiline_notes = "Participant";
    $tier1_datestart = "Date Start";
    $tier1_dateend= "Date End";
//task
$tier2_menu = "Item";
    $tier2_menu_desc = "Description";
    $tier2_menu_plural = "Items";
    $tier2_target = "Full Target";
    $tier2_target_unit = "Target (Unit)";
    $tier2_datestart = "Date Start";
    $tier2_dateend = "Date End";
//task owner
$tier3_menu = "Owner";
    $tier3_menu_plural = "Owners";
    $tier3_target_statement = "Target Statement";
    $tier3_target_setting = "Target Setting";
//others
$tag_field4_simple = "Contact Person";
    $tag_field4_show = true;//must set to true
$tag_field5_simple = "Progress Statement";
    $tag_field5_show = true;//must set to true
$tag_field6_simple = "Achievement from Evidence";
    $tag_field6_show = true;//must set to true
$tag_filterfield_simple = "Publication";
    $tag_filterfield_show = true;
//token link
$token_links_menu = "Evidence Gathering Links";
//document submission
$document_summited_menu = "Document Searcher";

//shared tags
$text_completion = "Completion";
$text_options = "Options";
$text_items = "Items";
$text_current_achievement = "Current Achievement";
$text_user_submitted_achievement = "User Submitted Achievement";
$text_balance = "Balance";
$text_updated = "Updated";
$text_progress_report = "Progress Report";
$text_progress_evidence = "Progress Evidence";
$text_date = "Date";//new
$text_participant = "Participant";//new
$text_target_listing = "Target Listing";//new
$text_type = "Type";//new
$text_target_setting = "Target Setting";//new
$text_uploaded_on = "Uploaded On";//new
$text_pdf_attachment = "PDF Attachment";//new
$text_duration = "Duration";
$text_form = "Form";
$text_listing_and_control = "Listing & Controls";

//show highest performing task owner on dashboard
$show_highest_performing_task_owner = true;

//set the default password to reset whenever reset password tool is used by admin for a user
$default_password_if_forgotten = "1";

//default creation password when creating new user
$default_create_password = "1";

//num of login attempt before blocking mechanism start
$default_num_attempt_login = 5;

//verification for progress input, true = need, false = do not need
$need_verification = false;
