<?php

	/*
	Copyight: Ilya Zhuravlev 2017
	License: MIT, see the LICENCE file
	*/

	if(!defined("__IN_SYMPHONY__")) die("<h2>Error</h2><p>You cannot directly access this file</p>");

	Class extension_google_recaptcha extends Extension {

		public function getSubscribedDelegates() {
			return array(
				array(
					'page' => '/system/preferences/',
					'delegate' => 'AddCustomPreferenceFieldsets',
					'callback' => 'addCustomPreferenceFieldsets'
				),
				array(
					'page' => '/blueprints/events/edit/',
					'delegate' => 'AppendEventFilter',
					'callback' => 'appendEventFilter'
				),
				array(
					'page' => '/blueprints/events/new/',
					'delegate' => 'AppendEventFilter',
					'callback' => 'appendEventFilter'
				),
				array(
					'page' => '/frontend/',
					'delegate' => 'FrontendParamsResolve',
					'callback' => 'frontendParamsResolve'
				),
				array(
					'page' => '/frontend/',
					'delegate' => 'EventPreSaveFilter',
					'callback' => 'eventPreSaveFilter'
				),
			);
		}

		public function uninstall(){
			Administration::Configuration()->remove('google_recaptcha');
			Administration::Configuration()->write();
		}

		public function appendEventFilter($context){
			$handle = 'google_recaptcha';
			$selected = (in_array($handle, $context['selected']));
			$context['options'][] = array(
				$handle, $selected, General::sanitize('Google reCAPTCHA')
			);
		}

		public function addCustomPreferenceFieldsets($context){
			$group = new XMLElement('fieldset');
			$group->setAttribute('class', 'settings');
			$group->appendChild(
				new XMLElement('legend', 'Google reCAPTCHA')
			);

			$site_key = Widget::Label(__('Site key'));
			$site_key->appendChild(Widget::Input(
				'settings[google_recaptcha][site_key]', General::Sanitize(Administration::Configuration()->get('site_key', 'google_recaptcha'))
			));
			$group->appendChild($site_key);

			$site_key = Widget::Label(__('Secret key'));
			$site_key->appendChild(Widget::Input(
				'settings[google_recaptcha][secret_key]', General::Sanitize(Administration::Configuration()->get('secret_key', 'google_recaptcha'))
			));
			$group->appendChild($site_key);

			$context['wrapper']->appendChild($group);
		}

		public function frontendParamsResolve($context){
			$context['params']['google_recaptcha'] = Symphony::Configuration()->get('site_key', 'google_recaptcha');
		}

		public function eventPreSaveFilter($context){
			$url = 'https://www.google.com/recaptcha/api/siteverify';
			$post = array(
				'secret' => Symphony::Configuration()->get('secret_key', 'google_recaptcha'),
				'response' => $_POST['g-recaptcha-response']
			);

			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

			$result = json_decode(curl_exec($curl));

			curl_close($curl);

			$handle = 'google_recaptcha';
			if(in_array($handle, (array)$context['event']->eParamFILTERS)){
				if($result->success != 1){
					$context['messages'][] = array('google_recaptcha', false, 'Google reCAPTCHA failed');
				}
			}
		}
	}
