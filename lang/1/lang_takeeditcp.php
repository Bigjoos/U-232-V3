<?php
$lang = array(
    //takeeditcp
    'takeeditcp_no_data' => "missing form data",
    'takeeditcp_pass_long' => "Sorry, password is too long (max is 40 chars)",
    'takeeditcp_pass_not_match' => "The passwords didn't match. Try again.",
    'takeeditcp_not_valid_email' => "That doesn't look like a valid email address.",
    'takeeditcp_address_taken' => "Could not change email, address already taken or password mismatch.",
    'takeeditcp_user_error' => "USER ERROR",
    'takeeditcp_image_error' => "Not an image or unsupported image!",
    'takeeditcp_small_image' => "Image is too small",
    'takeeditcp_confirm' => "profile change confirmation",
    'takeeditcp_avatar_not_allow' => "Sorry - Avatar changing disabled to your current user class",
);
$lang['takeeditcp_email_body'] = <<<EOD
You have requested that your user profile (username <#USERNAME#>)
on <#SITENAME#> should be updated with this email address (<#USEREMAIL#>) as
user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address <#IP_ADDRESS#>. Please do not reply.

To complete the update of your user profile, please follow this link:

<#CHANGE_LINK#>

Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
EOD;

?>
