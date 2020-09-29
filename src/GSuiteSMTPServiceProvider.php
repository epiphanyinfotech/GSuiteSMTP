<?php

namespace EpiphanyInfotech\GSuiteSMTP;

use EpiphanyInfotech\GSuiteSMTP\Includes\OAuth;
use Swift_Mailer;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Swift_SmtpTransport as SmtpTransport;
use Illuminate\Contracts\Support\DeferrableProvider;

class GSuiteSMTPServiceProvider extends ServiceProvider implements DeferrableProvider
{
	
	public function register()
	{
	
		$mail_config = config('mail'); //get the default mail settings
		$gss_config = config('GSuiteSMTP'); //GSuiteSMTP Config
		
		$this->app->singleton('mailer', function ($app) use ($mail_config, $gss_config) {
			
			//check if GSuiteSMTP is enabled in the env file by having it's value set to "true"
			$enable_gsuite_smtp = $_ENV['ENABLE_GSUITESMTP']; 
			
			$oauthUserEmail = config('GSuiteSMTP.user_email');
			$oauthClientSecret = config('GSuiteSMTP.client_secret');
			$oauthClientId = config('GSuiteSMTP.client_id');
			$oauthRefreshToken = config('GSuiteSMTP.refresh_token');
			
			$oauth_options = array(
								'userName' 		=> $oauthUserEmail,
								'clientSecret' 	=> $oauthClientSecret,
								'clientId' 		=> $oauthClientId,
								'refreshToken' 	=> $oauthRefreshToken							
							);
			
			if($enable_gsuite_smtp == 'true'){
								
				$google_oauth = New OAuth($oauth_options);
			
				$gsuite_token = $google_oauth->getOauth();
				
				
				// Once we have create the mailer instance, we will set a container instance
				// on the mailer. This allows us to resolve mailer classes via containers
				// for maximum testability on said classes instead of passing Closures.
				$mailer = new Mailer(
					$app['view'], new Swift_Mailer($this->createSmtpDriver($mail_config, $gss_config, $gsuite_token)), $app['events']
				);
				
				
			}else{
				
				$mailer = new Mailer(
					$app['view'], $app['swift.mailer'] , $app['events']
				);
				
			}
			
			$from_email = $mail_config['from']['address'];
			$from_name = $mail_config['from']['name'];
			
			$mailer->alwaysFrom($from_email, $from_name);
			$mailer->alwaysReplyTo($from_email, $from_name);

		
			if ($app->bound('queue')) {
				$mailer->setQueue($app['queue']);
			}
			
			return $mailer;
		});
		
	}
	
	
	/**
     * Create an instance of the SMTP Swift Transport driver.
     *
     * @param array $config
     *
     * @return \Swift_SmtpTransport
     */
    protected function createSmtpDriver($config, $gss_config, $gsuite_token)
    {
        // The Swift SMTP transport instance will allow us to use any SMTP backend
        // for delivering mail such as Sendgrid, Amazon SES, or a custom server
        // a developer has available. We will just pass this configured host.
        $transport = new SmtpTransport($config['host'], $config['port']);

        if (isset($config['encryption'])) {
            $transport->setEncryption($config['encryption']);
        }

        // Once we have the transport we will check for the presence of a username
        // and password. If we have it we will set the credentials on the Swift
        // transporter instance so that we'll properly authenticate delivery.
        if (isset($config['username'])) {
            $transport->setUsername($config['username']);

            $transport->setPassword($gsuite_token);
        }

        // Next we will set any stream context options specified for the transport
        // and then return it. The option is not required any may not be inside
        // the configuration array at all so we'll verify that before adding.
        if (isset($config['stream'])) {
            $transport->setStreamOptions($config['stream']);
        }


        return $transport;
    }
	
	public function boot() 
    { 
		$this->publishes([
            __DIR__.'/config/GSuiteSMTP.php' => config_path('GSuiteSMTP.php')
        ], 'gsuiteconfig');
    }
 
    
	
	/**
     * Implement DeferrableProvider in subclass
     */
    public function provides()
    {        
        return [
            'mailer'
        ];
    }
	
	
}