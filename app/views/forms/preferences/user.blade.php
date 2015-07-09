@extends('includes.user-nav')

<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.5/angular.min.js"></script>
<!-- Modules -->
<script src="resources/js/userApp.js"></script>

<!-- Controllers -->
<script src="resources/js/controllers/userController.js"></script>

<!-- Directives -->
<script src="resources/js/directives/User/autoSaveForm.js"></script>

@section('content')
<div class="content-wrapper" ng-app="UserApp">
	<div class="col-md-12">
		<br>
		<br>
			<div class="col-md-8 col-md-offset-2 well">
				@if(Session::has('success'))
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&#10005;</button>
					<strong>{{ Session::get('success') }}</strong>
				</div>
				@endif
				@if(Auth::user()->address == '')
				<h3 class="raleway light text-center">Create your account</h3>
				<hr>
				@endif
				<br>
				<div ng-controller="UserController">
				{{ Form::open(['name' =>"form.state", 'post' => 'preferences.storeUser', 'class' => 'form-horizontal', 'id' => 'preferences', 'auto-save-form'=>"saveForm()"]) }}

					<div class="form-group" ng-class="{ 'has-error': form.state.text.$invalid }">
						{{ Form::label('first_name', 'First name', ['class' => 'control-label col-md-2']) }}
						<div class="col-md-7">
							{{ Form::text('first_name', $model['first_name'], ['class' => 'form-control subtle-input', 'id' => 'firstName', 'ng-model' =>"form.data.first_name", 'ng-init' => "form.data.first_name='$model[first_name]'"]) }}
						</div>
					</div>

					<hr>
				
					<div class="form-group">
						{{ Form::label('last_name', 'Last name', ['class' => 'control-label col-md-2']) }}
						<div class="col-md-7">
							{{ Form::text('last_name', $model['last_name'], ['class' => 'form-control subtle-input', 'id' => 'lastName', 'ng-model' =>"form.data.last_name", 'ng-init' => "form.data.last_name='$model[last_name]'"]) }}
						</div>
					</div>

					<hr>
				
					<div class="form-group">
						{{ Form::label('email', 'Email address', ['class' => 'control-label col-md-2']) }}
						<div class="col-md-7">
							{{ Form::text('email', $model['email'], ['class' => 'form-control subtle-input', 'id' => 'email', 'ng-model' =>"form.data.email", 'ng-init' => "form.data.email='$model[email]'"]) }}
						</div>
					</div>

					<hr>

{{-- 					<div class="form-group" id="profile_group">
						{{ Form::label('profile', 'Amazon&trade; Profile', ['class' => 'control-label required col-md-2']) }}
						<div class="col-md-10">
							<p class="h4 light">We need you to copy your profile link:</p>
							<p class="h4 light">Step 1. <a href="http://www.amazon.com/gp/pdp/profile/" target="_blank">Click here <i class="fa fa-external-link"></i></a> to get it, and <strong>log in</strong> if prompted</p>
							<p class="h4 light">Step 2. <strong>Paste the link</strong> below</p>
							<p>In order to use RAYVR you must link your Amazon profile to your RAYVR profile. This does not give us access to your Amazon account, it only allows us to verify that you are a legitimate Amazon shopper.</p>
							<p>Click {{ HTML::link('http://www.amazon.com/gp/pdp/profile/', 'this link', ['target' => '_blank']) }} and, if prompted, login to Amazon. Once you arrive on your profile page, copy the url and paste it in the input below.</p>
						</div>
						<div class="col-md-2">
						</div>
						<div class="col-md-7">
							{{ Form::text('profile', $model['profile'], ['class' => 'form-control required subtle-input', 'id' => 'profile']) }}
						</div>
					</div>

					<hr> --}}
				
					<div class="form-group">
						{{ Form::label('address', 'Address', ['class' => 'control-label col-md-2']) }}
						<div class="col-md-7">
							{{ Form::text('address', $model['address'], ['class' => 'form-control subtle-input', 'id' => 'address', 'ng-model' =>"form.data.address", 'ng-init' => "form.data.address='$model[address]'"]) }}
						</div>
					</div>

					<hr>
				
					<div class="form-group">
						{{ Form::label('address_2', 'Address line 2', ['class' => 'control-label col-md-2']) }}
						<div class="col-md-7">
							{{ Form::text('address_2', $model['address_2'], ['class' => 'form-control subtle-input', 'ng-model' =>"form.data.address_2", 'ng-init' => "form.data.address_2='$model[address_2]'"]) }}
						</div>
					</div>

					<hr>
				
					<div class="form-group">
						{{ Form::label('city', 'City', ['class' => 'control-label col-md-2']) }}
						<div class="col-md-3">
							{{ Form::text('city', $model['city'], ['class' => 'form-control subtle-input', 'id' => 'city', 'ng-model' =>"form.data.city", 'ng-init' => "form.data.city='$model[city]'"]) }}
						</div>
						{{ Form::label('state', 'State', ['class' => 'control-label col-md-1']) }}
						<div class="col-md-2">
							@include('forms.lists.statesjs')
						</div>
					</div>
					
					<div class="form-group">
						{{ Form::label('country', 'Country', ['class' => 'control-label col-md-2']) }}
						<div class="col-md-4">
							@include('forms.lists.countriesjs')
						</div>
						{{ Form::label('zip', 'Zip', ['class' => 'control-label col-md-2']) }}
						<div class="col-md-3">
							{{ Form::text('zip', $model['zip'], ['class' => 'form-control subtle-input', 'id' => 'zip', 'ng-model' =>"form.data.zip", 'ng-pattern' => "/^\d+$/", 'ng-init' => "form.data.zip='$model[zip]'"]) }}
						</div>
					</div>

					<span ng-if="form.state.$dirty && form.state.$valid" class="help-block">Above changes saving...</span>
					<span ng-if="!form.state.$dirty && form.state.$valid" class="help-block">Above changes saved.</span>
					<span ng-if="form.state.$dirty && !form.state.$valid" class="help-block">Above changes invalid.</span>

					<hr>

				
					<div class="form-group col-md-12">
						<div class="row">
							{{ Form::label(null, 'Gender', ['class' => 'control-label required col-md-2']) }}
						</div>
						<div class="row">
							<br>
							<div class="col-md-4">

								{{--*/
									/**
									 * This reflects whether a user is
									 * male or female
									 */
									$gender = $model['gender'];
									$male = null;
									$female = null;
									if(!$gender)
										$male = 1;
									elseif($gender)
										$female = 1;
								/*--}} 
								<p class="normal col-md-6 text-right">Male&nbsp;&nbsp;{{ Form::radio('gender', '0', $male, ['id' => 'male']) }}</p>
								<p class="normal col-md-6 text-right">Female&nbsp;&nbsp;{{ Form::radio('gender', '1', $female, ['id' => 'female']) }}</p>
							</div>
						</div>
					</div>

					<hr>

					<div class="form-group col-md-12">
						<div class="row">
							<p>{{ Form::label('interests', 'Interests', ['class' => 'control-label required col-md-2']) }}</p>
							<div class="col-md-7 col-md-offset-1">
								<p>Your interests determine which offers you receive. The more interests you check the more offers you will receive!</p>
							</div>
						</div>

						<div class="col-md-12">
							<br>
							@foreach ($categories as $interest)
								<div class="col-md-4">
									<div class="checkbox">
										{{ Form::checkbox('interest[]', $value = $interest['id'], $interest['interest'], array('id' =>  'interest_'.$interest['id'], 'class' => 'form-control')) }}
										{{ Form::label('interest_'.$interest['id'], $interest['title'], array('id' => 'interest_'.$interest['id'])) }}
									</div>
								</div>
							@endforeach
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-7 text-left source-sans-pro light">
							@if(Session::has('success'))
								<p><em>{{ Session::pull('success') }}</em></p>
							@endif
						</div>
						<div class="col-md-2 text-right">
							{{ Form::submit('Save 	settings', ['class' => 'btn btn-success']) }}
						</div>
					</div>
				{{ Form::close() }}
				</div>
				<hr>
				<div class="col-md-3">
					{{ Form::open(['route' => 'deactivate', 'id' => 'deactivate']) }}
						{{ Form::button('DEACTIVATE ACCOUNT', ['class' => 'btn btn-danger', 'id' => 'deactivateAccount']) }}
					{{ Form::close() }}
				</div>
				<div class="col-md-4">
					<p>This action cannot be undone</p>
				</div>
			</div>
	</div>
</div>
@stop