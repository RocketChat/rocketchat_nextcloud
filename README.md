# Rocket Chat Official App
Place this app in **nextcloud/apps/**

## Rocket Chat Configuration
- If you get something like "refused to connect" and/or "ERR_BLOCKED_BY_RESPONSE" then you need to go to "Administration -> General -> Options to X-Frame-Options" and add your Nextcloud URL e.g. https://rgwit.mydomain.com
- In order to add members to a chat about a file directly from the discussion view an admin must go to "Administration -> Layout -> User Interface" and check "Show top navbar in embedded layout"
- To be able to register users and get an auth token, your RocketChat server MUST have the Environment Variable "CREATE_TOKENS_FOR_USERS=true" set. In a standard installation, just add it (without double quotes, for sure) in /lib/systemd/system/rocketchat.service
- UPDATE SINCE 4.8.x The user that will authenticate in the NextCloud MUST HAVE THE ROLE OF BOT!

## NextCloud Configuration
- As an NextCloud admin go to "Settings -> Administration -> Rocket Chat"
- Provide the Rocket Chat installation URL, Admin user name and Password and Click Connect and Register

## Browser Notifications
While using NextCloud you can receive notifications from Rocket chat by allowing the browser to send desktop notifications.

You can learn how to enable browser notifications here: https://support.google.com/chrome/answer/3220216?co=GENIE.Platform%3DDesktop&hl=en

## Full documented video to install both platforms from scratch in Ubuntu 20.0.x here:

[https://youtu.be/AshE2uG87GE](https://youtu.be/AshE2uG87GE)
