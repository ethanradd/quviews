<?php

return array(

	'secret'  => getenv('NOCAPTCHA_SECRET') ?: '6LfdkwUTAAAAAOooy0gNso-0TlTsuWHBfeG9vcd7',
	'sitekey' => getenv('NOCAPTCHA_SITEKEY') ?: '6LfdkwUTAAAAALHUV5G_pUBu2q6y1yXbKWDtxBV9',

	'lang'    => app()->getLocale(),

);
