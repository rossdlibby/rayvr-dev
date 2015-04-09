<?php namespace RAYVR\Storage\Preference;

use Preference, User, Session, Category, Lob;

class EloquentPreferenceRepository implements PreferenceRepository {

	public function all()
	{
		return Preference::all();
	}

	public function find($id)
	{
		return Preference::find($id);
	}

	public function create($input)
	{
		return Preference::create($input);
	}

	public function interests($preferences, $interests)
	{
		/**
		 * Get the new user's id from the entered email
		 */
		$user_id = Session::get('new_user');

		/**
		 * Set user preferences
		 */
		$user = User::find($user_id);
		$s = $user->fill($preferences);

		/**
		 * Store first name in a session variable for
		 * access on the Welcome page
		 */
		Session::put('name', $preferences['first_name']);

		/**
		 * Create a user relationship with each
		 * category they are interested in
		 */
		for($i = 0; $i < count($interests); $i++)
		{
			/**
			 * Find category associated with user selection
			 */
			$category = Category::find($interests[$i]);

			/**
			 * Build array for interest creation
			 */
			$interest = array_merge(['user_id' => $user_id], ['cat_id' => $category->id]);

			/**
			 * Create new Interest (user-category relationship)
			 */
			$user->interest()->create($interest);
		}

		if($s->save())
			return $s;
		else
			return false;
	}

	public function setInterests($user, $interests)
	{
		/**
		 * Delete old user interests
		 */
		$user->interest()->delete();
		/**
		 * Create a user relationship with each
		 * category they are interested in
		 */
		for($i = 0; $i < count($interests['interest']); $i++)
		{
			/**
			 * Find category associated with user selection
			 */
			$category = Category::find((int)($interests['interest'][$i]));

			/**
			 * Build array for interest creation
			 */
			$interest = array_merge(['user_id' => $user->id], ['cat_id' => $category->id]);

			/**
			 * Create new Interest (user-category relationship)
			 */
			$user->interest()->create($interest);
		}
	}

	/**
	 * This sets up preferences without
	 * associating any user <--> category
	 * relationships (interests)
	 */
	public function preferences($preferences, $user)
	{
		/**
		 * Check if preference record exists.
		 * If true, update
		 * If false, create
		 */
		$pref = $user;

		$pref->fill($preferences);

		/**
		 * If this is the first time setting
		 * preferences, redirect the user to
		 * 'invite your friends' page
		 */
		if(!$user->times_updated && !$user->business)
		{
			$user->times_updated = 1;
			$user->save();
			return Redirect::route('invite')
				->with('success', 'Your account setup is complete.');
		}
		else if(!$user->times_updated && $user->business)
		{
			$user->times_updated = 1;
			$user->save();
			return \Redirect::route('billing')
				->with('success', 'Your account setup is complete.');
		}
		else
		{
			$user->times_updated = (int)($user->times_updated) + 1;
			$user->save();

			if($user->business)
			{
				return Redirect::route('business.preferences')
					->with('success', 'Your settings have been saved');
			}
		}

		/**
		 * Make sure the user didn't skip any fields
		 */
		if(!$pref->first_name || !$pref->last_name || !$pref->email || !$pref->address || !$pref->city || !$pref->country || !$pref->zip || !$pref->phone)
		{
			$s = $pref->save();

			// return list of empty __required__ fields
		}

		$s = $pref->save();

		return $s;
	}

	public function interestCategories($user)
	{
		/**
		 * Select all the categories
		 */
		$categories = Category::all();

		/**
		 * Select all the user's interests
		 */
		$interests = $user->interest;

		/**
		 * Build an array of the
		 * categories, assigning a value
		 * of true to those that are
		 * interests and false to those
		 * that are not
		 */
		$cats = [];

		foreach($categories as $category)
		{
			$isInterest = false;

			/**
			 * Iterate through the $interests
			 * array to find a match for the
			 * category ID
			 */
			foreach($interests as $interest)
			{
				if($interest->cat_id == $category->id)
				{
					$isInterest = true;
					break;
				}
			}

			$cat = [
				'id'		=> $category->id,
				'title'		=> $category->title,
				'interest'	=> $isInterest
			];
			array_push($cats, $cat);
		}

		$categories = $cats;

		return $categories;
	}

	public function postcard($user)
	{
		/**
		 * Initiate Lob
		 */
		$lob = new \Lob\Lob($_ENV['lob_api_key']);

		/**
		 * If the user entered their address
		 * and their account is unverified,
		 * send a verification post card
		 */
		if(!$user->verified && $user->first_name != '' && $user->last_name != '' && $user->address != '' && $user->zip != '' && $user->state != '' && $user->city != '' && $user->country != '')
		{
			/**
			 * Create the receiving address
			 */
			$toAddress = $lob->addresses()->create([
				'description'		=> $user->email.' - Home',
				'name'				=> $user->first_name.' '.$user->last_name,
				'address_line1'		=> $user->address,
				'address_line2'		=> $user->address_2,
				'address_city'		=> $user->city,
				'address_state'		=> $user->state,
				'address_country'	=> $user->country,
				'address_zip'		=> $user->zip,
				'email'				=> $user->email,
				'phone'				=> $user->phone
			]);

			/**
			 * Create the sending address
			 */
			$fromAddress = $lob->addresses()->create([
				'description'		=> 'RAYVR, LLC',
				'name'				=> 'The RAYVR Team',
				'address_line1'		=> '1704 Lake Avenue',
				'address_line2'		=> '1/2',
				'address_city'		=> 'West Palm Beach',
				'address_state'		=> 'FL',
				'address_country'	=> 'US',
				'address_zip'		=> '33401',
				'email'				=> 'info@rayvr.com',
				'phone'				=> ''
			]);

			/**
			 * Extract the address ID from
			 * the newly created address
			 */
			$address = $toAddress['id'];
			$from = $fromAddress['id'];
			$postcard = $lob->postcards()->create([
				'name'			=> $user->first_name,
				'to'			=> $address,
				'from'			=> $from,
				'full_bleed'	=> 1,
				'template'		=> 1,
				'front'			=> '<html style="margin: 130px 0; font-size: 50;">Code: <strong>'.$user->confirm.'</strong></html>',
				'back'			=> 'https://lob.com/postcardback.pdf',
			]);
			return $postcard;
		}
	}
}