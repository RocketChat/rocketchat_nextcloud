# Rocket Chat Official App
Place this app in **nextcloud/apps/**

## Rocket Chat Configuration
- Open a new tab to create a Personal Access Token in Rocket Chat
    - In the new tab open Rocket Chat
    - As a Rocket Chat admin go to "Profile -> My Account -> Security -> Personal Access Tokens"
    - Enter a name for the Personal Access Token and then hit Add
    - The Personal Access Token and the user Id will be generated 
    
    
## NextCloud Configuration
- As an NextCloud admin go to "Settings -> Administration -> Additional settings"
- Provide the Rocket Chat installation URL
- Copy the token and insert it back in the NextCloud settings tab in the Personal Access Token input, similar for the user Id
- Click the Submit button
- You will be redirected to the Rocket Chat app in NextCloud.
- If you get something like "refused to connect" and/or "ERR_BLOCKED_BY_RESPONSE" then you need to go to "Administration -> General -> Options to X-Frame-Options" and add your Nextcloud URL e.g. https://rgwit.mydomain.com
- Now you can access the Rocket Chat app from the navigation bar in NextCloud and open a new chat about a file by clicking the three dots icon and then clicking Rocket
- In order to add members to a chat about a file directly from the discussion view an admin must go to "Administration -> Layout -> User Interface" and check "Show top navbar in embedded layout"  
- In order to change the Rocket Chat instance an admin can go to "Settings -> Administration -> Additional Settings" and then click the Reset Data button, this will remove the Rocket Chat URL, the Personal Access Token and the user Id from the database so you can change the Rocket Chat instance.


## Browser Notifications
While using NextCloud you can receive notifications from Rocket chat by allowing the browser to send desktop notifications.

You can learn how to enable browser notifications here: https://support.google.com/chrome/answer/3220216?co=GENIE.Platform%3DDesktop&hl=en
