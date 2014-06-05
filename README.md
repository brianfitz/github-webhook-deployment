Simple Deploy using Github Webhooks
=======

After looking for a simple solution to automatically deploy a simply PHP repo housed in Github, I stitched together my own based on instructions and other code found in differing locations. Credit goes to http://github.com/kwangchin who wrote the ip_in_cidrs function to ensure that the webhook originated with Github. I tried other solutions out on the web and found the following issues:

1. There was enough complexity in the code that it was difficult to diagnose failures
2. They didn't support multiple environments such as development, staging, and production

##The steps to nirvana:

###First, clone your Github repo on the target server(s).   
In our case, this was two root folders on one Ubuntu server -- one for staging changes to our marketing web site, and the other for production. 

### We now have one github project, with the exact same repo in two folders on an Ubuntu server.  
Next, we need to create a branch for staging and a branch for production. In our case, we use the standard master branch which should always be production deployable. For our staging site, a new git branch was created (aptly named "staging") and was checked out in the staging folder.

###Performing a "git pull" in either folder will now merge in any changes to their corresponding branches (master and staging).  
However, when this command is issued by the web server, it will need the appropriate permissions to our github repo -- in our case, a private one. In order for this to work, we need to set up permissions for the web server user (www-data) to access our private Github repo. The first step is creating a .ssh folder in the user root folder:
```
sudo mkdir /var/www/.ssh
sudo chown -R www-data:www-data /var/www/.ssh/
```
Next, create the public/private key combination. Be sure to use an empty passphrase:
```
sudo -Hu www-data ssh-keygen -t rsa
pbcopy < /var/www/.ssh/id_rsa.pub
```
The public key is now in your copy bin, so it can be pasted into Github as a new public key that can be used to access your project. Once this has been added to Github, you are ready to set up the deploy project.

###The deployment app should go in another root folder on the web server.
You should also consider burying the deploy.php file by renaming it under a random folder such as: /var/www/deployment/sdj8329d/ksdfodh2.php. This is just another layer of security beyond the check to ensure that the request originated from a Github IP address. As a final step, for setup, you'll need to change the root web folders in the deploy.php file to match your own. If you have more than two environments, you can extend the if statement to support this.

###Once the deploy.php has been setup and named appropriately, you'll need to set up the webhook in Github.
Go to your web site Github project, and select "Settings" and then "Webhooks & Services". From the Webhooks & Services page, click the "Add webhook" button. Enter your URL for the deploy page -- such as https://deploy.www.mysite.com/sdj8329d/ksdfodh2.php from the proceeding naming example. This next step is **really** important. Be sure to select "application/x-www-form-urlencoded" from the content type dropdown. I left this the default json type and had problems with no POST data coming across. It was only when I changed this that everything began working. Finally, select "Just the push event" from the remaining option and ensure the "Active" box is checked.

###Test it out!
At this point, you should be able to git push to either master or staging and see the changes propogate to your staging and production web sites. If you run into any trouble, the first thing I would do would be to create a simply test.php page **inside** your staging root web folder that only does the following:
```
`git pull`
```
You can then hit this page from your web browser and see if the problem is with permissions or something else. You can add more debug statements around this page as you continue to troubleshoot. If you find this works, you can go back to the Github webhook set up page and see the recent calls made and the result returned. You can add debug output to your ksdfodh2.php (from our proceeding example) that will show in the "response" tab in the webhooks setup page for each call made by Github. You can also use the "Redeliver" button to make repeated calls to your deploy page for testing.


