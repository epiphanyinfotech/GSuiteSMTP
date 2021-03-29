# GSuiteSMTP
Package to allow sending emails through Google Suite or even Gmail, without turning on the "allow less secure apps" and using OAuth2.0 token, client id, client secret and refresh token

Tested on Laravel 6.x

Before installing, it is assumed that you have created the App on Google and have the:
- Client ID
- Client Secret
- Refresh Token

If you don't have the refresh token or need to know more about getting these details, start from here:
[https://developers.google.com/google-ads/api/docs/first-call/refresh-token#php](https://developers.google.com/google-ads/api/docs/first-call/refresh-token#php)

_________________________________________________________________________________________________________________________

Once you have these:
1. Start by running composer from the root directory of your project and type:
```
composer require epiphany-infotech/gsuite-smtp
```
2. Add the provider in the 'providers' array in your `config\app.php` file:
```
EpiphanyInfotech\GSuiteSMTP\GSuiteSMTPServiceProvider::class,
```
3. Publish the config file using the command:
```
php artisan vendor:publish --tag=gsuiteconfig
```
4. Add the following in your .env file:
```
  ENABLE_GSUITESMTP=true
  SMTP_USER_EMAIL=youremail@tld.com
  SMTP_REFRESH_TOKEN="your referesh token" #preferred to be in quotes
  SMTP_CLIENT_SECRET="your client secret" #preferred to be in quotes
  SMTP_CLIENT_ID="your client id" #preferred to be in quotes
```  
5. Delete the cached files present in `bootstrap\cache`. No need to delete the .gitignore file.

6. Run the coommands:
```
  php artisan config:cache
  php artsian config:clear
```
  
This should get you going and **you shouldn't have to enable "allow less secure apps"** just to have the SMTP working

__________________________________________________________________________________________________________________________

Points to keep in mind:

1. Your other configuration for SMTP in the `.env` should remain as it would be without this package, i.e.:
```
  MAIL_DRIVER=smtp
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=587
  MAIL_USERNAME=youremail@tld.com
  MAIL_PASSWORD=NULL  #Can be anything but since we are bypassing it, it's just mentioned as null
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=youremail@tld.com
  MAIL_FROM_NAME="${APP_NAME}"
```
2. This is not a production level package and is there just to get you started. It will work and give you the desired results but this is the only documentation at the moment and offers no gurantee whatsoever.

Contributors are welcomed to work further on this project. Remove flaws, add documentation or more features, more smtp providers etc.

For any inquires regarding a new Laravel Project or any web or mobile development related work, please visit:

[https://www.epiphanyinfotech.com](https://www.epiphanyinfotech.com)

Or email at:

`brajinder@epiphanyinfotech.com`

