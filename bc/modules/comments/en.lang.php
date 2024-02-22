<?php
/* $Id: en.lang.php 6265 2012-02-21 13:53:01Z nastya $ */

// main
define("NETCAT_MODULE_COMMENTS_GUEST", "Guest");
define("NETCAT_MODULE_COMMENTS_DESCRIPTION", "This module is for the organization of comments.");
// links
define("NETCAT_MODULE_COMMENTS_LINK_REPLY", "reply");
define("NETCAT_MODULE_COMMENTS_LINK_EDIT", "edit");
define("NETCAT_MODULE_COMMENTS_LINK_DELETE", "delete");
define("NETCAT_MODULE_COMMENTS_LINK_COMMENT", "comment");
define("NETCAT_MODULE_COMMENTS_SUBSCRIBE_TO_ALL", "subscribe to all comments");
define("NETCAT_MODULE_COMMENTS_UNSUBSCRIBE_FROM_ALL", "unsubscribe");

define("NETCAT_MODULE_COMMENTS_ADD_FORM_DELETE_QUESTION", "Delete this comment?");

// errors
define("NETCAT_MODULE_COMMENTS_NO_ACCESS", "No permission to complete this operation!");
define("NETCAT_MODULE_COMMENTS_UNCORRECT_DATA", "Uncorrect data format!");

define("NETCAT_MODULE_COMMENTS_ADD_FORM_APPEND_BUTTON", "Save");
define("NETCAT_MODULE_COMMENTS_ADD_FORM_UPDATE_BUTTON", "Update");
define("NETCAT_MODULE_COMMENTS_ADD_FORM_DELETE_BUTTON", "Delete");
define("NETCAT_MODULE_COMMENTS_ADD_FORM_CANCEL_BUTTON", "Cancel");
define("NETCAT_MODULE_COMMENTS_ADD_FORM_LOADING_TEXT", "Loading data...");

// admin interface
define("NETCAT_MODULE_COMMENTS_ADMIN_MAINSETTINGS_SAVE_BUTTON", "Save");

//tabs
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_LIST_TAB", "Comments list");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_TEMPLATE_TAB", "Template settings");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_TEMPLATE_MAIN", "Common settings");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SUBSCRIBE_TAB", "Subscriptions and notifications");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_CONVERTER_TAB", "Converter");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_OPTIMIZE_TAB", "Optimize data");

//comments list
define("NETCAT_MODULE_COMMENTS_ADMIN_CHECK", "check");
define("NETCAT_MODULE_COMMENTS_ADMIN_CHECK_OK", "Comment has been successfully checked");
define("NETCAT_MODULE_COMMENTS_ADMIN_CHECK_COMMENTS_OK", "Selected comments were checked");
define("NETCAT_MODULE_COMMENTS_ADMIN_UNCHECK", "uncheck");
define("NETCAT_MODULE_COMMENTS_ADMIN_UNCHECK_OK", "Comment has been successfully unchecked");
define("NETCAT_MODULE_COMMENTS_ADMIN_UNCHECK_COMMENTS_OK", "Selected comments were unchecked");
define("NETCAT_MODULE_COMMENTS_ADMIN_NO_SELECTED_COMMENTS", "Comments are not selected");
define("NETCAT_MODULE_COMMENTS_ADMIN_EDIT", "change");
define("NETCAT_MODULE_COMMENTS_ADMIN_EDIT_OK", "Comment has been successfully changed");
define("NETCAT_MODULE_COMMENTS_ADMIN_DEL_OK", "Selected comments have been successfully removed");
define("NETCAT_MODULE_COMMENTS_ADMIN_DEL_ALL_OK", "All comments have been successfully removed");
define("NETCAT_MODULE_COMMENTS_ADMIN_DEL_BACK", "Return");

//filter
define("NETCAT_MODULE_COMMENTS_ADMIN_COMMENTS_LIST_SELECT", "Fetching comments");
define("NETCAT_MODULE_COMMENTS_ADMIN_NO_COMMENTS", "No comments");
define("NETCAT_MODULE_COMMENTS_ADMIN_SUBDIVISION", "Select subdivision");
define("NETCAT_MODULE_COMMENTS_ADMIN_CLASS", "Select component");
define("NETCAT_MODULE_COMMENTS_ADMIN_ALLUSERS", "all");
define("NETCAT_MODULE_COMMENTS_ADMIN_ONUSERS", "checked");
define("NETCAT_MODULE_COMMENTS_ADMIN_OFFUSERS", "unchecked");
define("NETCAT_MODULE_COMMENTS_ADMIN_DOGET", "Select");

define("NETCAT_MODULE_COMMENTS_ADMIN_CONFIRM_DEL", "Confirm delete");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONFIRM_DEL_ALL", "Confirm the deletion of all comments");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONFIRM_DEL_OK", "Confirm");

//edit
define("NETCAT_MODULE_COMMENTS_ADMIN_EDIT_TEXT", "Comment text");
define("NETCAT_MODULE_COMMENTS_ADMIN_EDIT_AUTHOR", "Comment author:");
define("NETCAT_MODULE_COMMENTS_ADMIN_EDIT_AUTHOR_USER", "User:");
define("NETCAT_MODULE_COMMENTS_ADMIN_EDIT_AUTHOR_NAME", "Guest name:");
define("NETCAT_MODULE_COMMENTS_ADMIN_EDIT_AUTHOR_EMAIL", "Guest email:");
define("NETCAT_MODULE_COMMENTS_ADMIN_EDIT_SAVE_EMAIL_ERROR", "Enter the correct email");
define("NETCAT_MODULE_COMMENTS_ADMIN_EDIT_SAVE_OK", "Changes saved.");


//converter
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_RETURN_BUTTON", "Return");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_SAVE_BUTTON_4", "Select catalogue");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_SAVE_BUTTON_5", "Select subdivision");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_SAVE_BUTTON_6", "Convert");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_SELECT_CATALOGUE", "Catalogue");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_SELECT_SUBDIVISION", "Subdivision selection");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_SELECT_SUBCLASS", "Component selection");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERTER_DIALOG", "Select dialog");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_OK", "Comments converted.");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_ERROR", "Convertation error.");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_PARENT_ERROR", "Comments and replies convertation error. Duplicate data into the base.");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_DATA_ERROR", "Uncorrect data.");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_CLASS_ERROR", "Component error.");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_NO_CATALOGUE_ERROR", "No sites found.");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_NO_SUBDIVISION", "No subdivisions found.");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_NO_SUBCLASS", "No components found.");
define("NETCAT_MODULE_COMMENTS_ADMIN_CONVERT_NO_DATA", "No such objects data.");

//settings
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_MAIN_SETTINGS", "General Settings");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_USER_NAME", "System table \"User\" field for user name");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_USER_AVATAR", "System table \"User\" field for user avatar");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_GUEST_USER_NAME", "Allow an unauthorized user to input a name");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_GUEST_USER_EMAIL", "Allow an unauthorized user to input a email");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_GUEST_USER_NEED", "Necessarily");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_QTY", "Number of comments per page");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_SHOW_ALL", "Display the button \"Show All\"");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_ORDER_DESC", "Inverse sorting comments (newest first)");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_BBCODE", "Use BB-codes");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_USE_CAPTCHA", "Use captcha");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_RATING", "Enable rating");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_PREMODERATION", "Premoderation");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_ALLOW_PREMODERATION", "Allow comments premoderation");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_PREMODERATION_NO", "No");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_PREMODERATION_GUEST", "Only for unauthorized users");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_PREMODERATION_ALWAYS", "Always");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_NEW_COMMENTS", "New comments");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_HIGHLIGHT_NEW_COMMENTS", "Highlight new comments");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_SHOW_BUTTON_NEW_COMMENTS", "Show the button \"Next new comment\"");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_SHOW_ADD_BLOCK", "Immediately show add block");
define("NETCAT_MODULE_COMMENTS_ADMIN_SETTINGS_SAVE_OK", "Comments settings saved.");

//template
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_TEMPLATE", "Comments view template");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_TEMPLATE_NEW", "new template");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS", "Template settings");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_USE_DEFAULT", "use as default");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_NAME", "Template name");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_PREFIX", "Comments \"wall\" prefix");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_COMMENT_BLOCK", "Comment view block");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_REPLY_BLOCK", "Reply view block");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_LINK_COMMENT", "Comment link");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_LINK_REPLY", "Reply link");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_LINK_EDIT", "Edit link");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_LINK_DROP", "Delete link");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_APPEND_BLOCK", "Comment or reply append block");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_EDIT_BLOCK", "Comment or reply edit block");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_DROP_BLOCK", "Comment or reply delete block");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_SUFFIX", "Comments \"wall\" suffix");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_PAGINATION", "Pagination template");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_SHOW_ALL", "Button \"Show All\"");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_WARNTEXT", "Warning text");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_WARNTEXT_NAME", "Enter the name");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_WARNTEXT_EMAIL", "Enter or enter the correct email");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_WARNTEXT_NAME_EMAIL", "Enter the name and the email");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_WARNTEXT_PARENT", "Comment to which you've written reply has been removed or disabled");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_WARNTEXT_CAPTCHA", "Enter symbols shown on the picture");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_PREMODERATION", "Comments premoderation message");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_NEW_COMMENT_BUTTON", "Button \"Next new comment\"");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_SAVE_OK", "Template settings saved.");
define("NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SETTINGS_RATING_BLOCK", "Ratio block");

//subscribe
define("NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_ALLOW", "subscriber allow");
define("NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_BLOCK", "Subscribe link");
define("NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_MAIL_TEMPLATE", "Mail body");
define("NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_MAIL_SUBJECT", "Mail subject");
define("NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_SAVE_OK", "Subscriptions and notifications settings saved.");
define("NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_ADMIN", "Admin notification");
define("NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_ADMIN_ALLOW", "allow notify the administrator of new comments");
define("NETCAT_MODULE_COMMENTS_ADMIN_SUBSCRIBE_SETTINGS_ADMIN_EMAIL", "Notification email");

//optimize
define("NETCAT_MODULE_COMMENTS_ADMIN_OPTIMIZE", "Optimize data");
define("NETCAT_MODULE_COMMENTS_ADMIN_OPTIMIZE_DO", "Recalculate comments and replies");
define("NETCAT_MODULE_COMMENTS_ADMIN_OPTIMIZE_DO_BUTTON", "Optimize");
define("NETCAT_MODULE_COMMENTS_ADMIN_OPTIMIZE_OK", "Recalculated rows: %COUNT");
define("NETCAT_MODULE_COMMENTS_ADMIN_OPTIMIZE_NO_DATA", "No data to recalculate");

define("NETCAT_MODULE_COMMENTS_CLASS_UNRECOGNIZED_OBJECT_CALLING", "Unrecognized object calling");
define("NETCAT_MODULE_COMMENTS_CLASS_UNCORRECT_DATA_FORMAT", "Uncorrect data format");
?>