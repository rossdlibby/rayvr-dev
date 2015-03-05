<?php

use RAYVR\Storage\User\UserRepository as User;

class LandingController extends BaseController {

	/**
	 * Use User Repository
	 */
	protected $user;

	/**
	 * Inject the User Repository
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
	}

	/**
	 * Direct logged in user to the
	 * appropriate dashboard
	 */
	public function index($referral = null)
	{
		/**
		 * Return business dashboard
		 * if the user is a business
		 * user
		 * 
		 * Otherwise return the user
		 * dashboard
		 * 
		 * If the user's preferences
		 * have not been completed,
		 * redirect to the
		 * preferences page
		 */
		if(Auth::user())
		{
			if(Auth::user()->business && Auth::user()->active == 1)
			{
				if(Auth::user()->address && Auth::user()->email && Auth::user()->first_name && Auth::user()->last_name && Auth::user()->zip && Auth::user()->country && Auth::user()->phone && Auth::user()->stripe_plan)
				{
					/**
					 * If the user has no approved offers,
					 * redirect the user to the holding
					 * page
					 */
					if(!empty(json_decode(Offer::where('business_id',Auth::user()->id)->where('approved',true)->get())))
					{
						/**
						 * If the user has not subscribed to
						 * a membership, direct the user to
						 * the membership page
						 */
						if((Auth::user()->trial_ends_at < date('Y-m-d') && Auth::user()->subscription_ends_at < ('Y-m-d')) || !Auth::user()->stripe_plan)
							return Redirect::to('payments');
						else
							return Redirect::to('offers/track');
					}
					else
					{
						return Redirect::to('offers/review');
					}
				}
				else
				{
					if(!Auth::user()->address || !Auth::user()->email || !Auth::user()->first_name || !Auth::user()->last_name || !Auth::user()->zip || !Auth::user()->country || !Auth::user()->phone)
						return Redirect::to('settings');
					else if(!Auth::user()->stripe_plan)
						return Redirect::to('payments');
					else
						return Redirect::to('offers/track');
				}
			}
			else if(Auth::user()->business && Auth::user()->active == 3)
			{
				return Redirect::to('admin/control');
			}
			else
			{
				return Redirect::to('offers/current');
			}
		}
		else
		{
			return View::make('session.register')->with('referral', $referral);
		}
	}

	/**
	 * Direct any user to the contact
	 * page
	 */
	public function contact()
	{
		return View::make('landing.contact');
	}
}