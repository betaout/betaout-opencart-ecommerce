------------------------------------------------------
Betaout OpenCart Ecommerce VQmod
------------------------------------------------------
Source Repository:	https://github.com/betaout/betaout-opencart-ecommerce

Author:			Rohit,Kapil  / www.betaout.com

Version:		0.1

Release Date:		2015-08-13

License:		GNU General Public License (GPL) version 3
------------------------------------------------------

DESCRIPTION
-----------
Implements Betaout Ecommerce tracking for OpenCart;
> Tracks Ecommerce product views (category views not yet implemented)
> Tracks Ecommerce cart add/update/delete
> Tracks Ecommerce orders

Fully VQmod'ed up! Does NOT overwrite any core files.



IMPORTANT
---------
1. Requires VQmod installed (Highly recommended - https://github.com/vqmod/vqmod/ ).


2. The default install assumes that Piwik is installed to the '/piwik/' folder at the root of your OpenCart site.

3. The default install assumes that your OpenCart Admin directory is in the '/admin/' folder at the root of your OpenCart site.
If you have used a custom Admin path then please place all files from 'betaout-opencart-ecommerce/admin/' to your custom OpenCart Admin folder.

4. FOR OPENCART 2.0 or higher! Only tested on OpenCart 2.0.2.0 with VQmod 2.5.1, and Betaout 1.0- if you are having issues please make sure to update your versions
(may well work on others - please tell me what you find out!)



INSTALL
-------
1) Upload the contents of the 'betaout-opencart-ecommerce' directory to the root of your OpenCart site.
2) Login to your OpenCart admin, go to the Extensions -> Modules page, and click 'Install' next to 'Betaout OpenCart Ecommerce VQmod'.
3) After install, click 'Edit' next to 'Betaout OpenCart Ecommerce VQmod', and on the settings page enter the details about the betaout installation;


a) "API Key" - This is your API Key . Get this from the Click  Tab 'Idea & Docs ' then APIKEY tab on your Betaout admin panel.

b) "Project Id" - This is your  Project Id. Get this from the Click  Tab 'Idea & Docs ' then APIKEY tab on your Betaout admin panel





UNINSTALL
---------
In OpenCart admin, go to the Extensions -> Modules page, and simply click 'Uninstall' next to 'Betaout OpenCart Ecommerce VQmod'.
This will ensure the configuration settings are deleted and that none of the main functions of the mod will run.
Some files will still remain - however these should be perfectly safe and not affect anything but to fully remove please delete all files which you uploaded during the install.



LIMITATIONS
-----------
This is the first release for OC2.x and would benefit from further feedback. Please report any bugs found!
1) There is only  track page views / cart add/updates/remove / orders.
3) The cart tracking uses the main product 'price' attribute, not taking into account special offers / taxes. However ecommerce orders do correctly take account of specials & taxes.




VERSION HISTORY
---------------

v0.1 - 2015/08/13
First pre-release version.



SUPPORT
-------
I'm happy to help if you have any problems (though can't promise large amounts of time).
I'm also keen to get your feedback (dont be afraid to be critical if I've done something wrong!).
It would be great to hear of your experiences and what could be improved.
You can contact me using the form on https://www.betaout.com/contact-us

You can see the current bugs/features being worked on at https://github.com/betaout/betaout-opencart-ecommerce/issues
Feel free to raise new issues if you find anything which could be improved (or even better, contribute some code!).

