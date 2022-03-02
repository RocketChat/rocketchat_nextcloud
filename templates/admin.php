<?php
/*
 _____________________________________________________________
|   Rocket Chat NextCloud App                                 |
|   Authors: Ruvenss G. Wilches & Pierre Locus                |
|   Proudly working for Rocket.Chat Inc                       |
|   All licences and code belong to Rocket.Chat Inc           |
|   Live long and Prosper                                     | 
|_____________________________________________________________|                                                                                                                                                                             
*/
script('rocketchat_nextcloud', 'admin');
style('rocketchat_nextcloud', 'style');
?>
<div class="rocket-info-wrapper">
    
        <div class="section">
            <h2><img src="/apps/rocketchat_nextcloud/img/rocket-logo-black.png" width=15> Rocket Chat v.0.9.2 RC</h2>
      
            <div class="row">
                    <div class="col col-6">
                        <div id="rocketURL" class="infobox">
                            <p class="rocketp"> <img src="/apps/rocketchat_nextcloud/img/admin-rocket.svg" class="infoicon"> Admin User ID<br>
                                <input type="text"
                                placeholder="Enter Rocket Chat Admin User ID"
                                class="input rocketinput rocketform"
                                required
                                name="userId"
                                value="<?= p($_['user_id']); ?>"
                                id="userId" spellcheck="false" readonly="readonly">
                                <a class="clipboardButton icon icon-clippy" data-clipboard-target="#userId"></a>
                            </p>
                        </div>
                    </div>
                    <div class="col col-6">
                        <div id="rocketURL" class="infobox">
                            <p class="rocketp"> <img src="/apps/rocketchat_nextcloud/img/key-rocket.svg" class="infoicon"> Admin Token<br>
                                <input type="text"
                                placeholder="Admin Token"
                                class="input rocketinput rocketform"
                                required
                                name="personalAccessToken"
                                value="<?= p($_['personal_access_token']); ?>"
                                id="personalAccessToken"  spellcheck="false" readonly="readonly"><a class="clipboardButton icon icon-clippy" data-clipboard-target="#personalAccessToken"></a>
                            </p>
                            
                        </div>
                    </div>
                    
                    <div class="col col-12">
                        <div id="rocketURL" class="infobox">
                            <p class="rocketp"> <img src="/apps/rocketchat_nextcloud/img/login-svgrepo-com.svg" class="infoicon"> Auto generate Token and User ID <br><br>Your user name and password won't be saved. 
                                
                                    <input type="text" placeholder="rocket chat user" class="input rocketinput rocketform" name="rcuser" id="rcuser" require>
                                    <input type="password" placeholder="rocket chat password" class="input rocketinput rocketform" name="rcpassword" id="rcpassword" require>
                                    <input type="url" placeholder="https://your.rocket.chat.server.com" class="input rocketinput rocketform" name="url" id="rcurl" value="<?= p($_['rocketUrl']); ?>" required>
                                    <button class="button rocketform" id="rcconnect"> Connect and save</button>
                                   
                            </p>
                        </div>
                    </div>
            </div>
            
        </div>
        
    </form>
    <div class="section">
        <h2>Features</h2>
        <p>
            🔑 Automatic login.
        </p>
        <p>
            👤 Automatic user creation in Rocket.Chat
        </p>
        <p>
            ⚙️ Easy setup.
        </p>
    </div>
    <div class="section">
        <h2>Support</h2>
        <p>
            Tell us what would you like to see with this App or report any issue here:
            <a href="https://github.com/RocketChat/NextCloud/issues/new" target="_blank" class="button link-button"><img src="/apps/support/img/github.svg" width="12"> RocketChat NextCloud at GitHub</a>
        </p>
    </div>
</div>
